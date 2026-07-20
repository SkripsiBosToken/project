<?php

namespace Tests\Feature;

use App\Actions\PasswordResetAction;
use App\Models\User;
use App\Support\ShippingCalculator;
use Tests\TestCase;

/**
 * Menjaga rute GET agar tidak pernah mengarahkan ke dirinya sendiri.
 *
 * back() pada rute GET berbahaya: bila tidak ada header Referer, Laravel
 * memakai "previous url" dari session — dan setiap permintaan GET menyimpan
 * URL-nya sendiri ke sana. Akibatnya rute mengarahkan ke dirinya sendiri dan
 * browser mengikutinya sampai menyerah dengan ERR_TOO_MANY_REDIRECTS.
 */
class RedirectLoopTest extends TestCase
{
    private function user(): User
    {
        // Tidak disimpan ke database: rute yang diuji hanya melakukan redirect,
        // jadi cukup identitas untuk melewati middleware auth.
        return new User(['id' => 1, 'name' => 'Penguji', 'email' => 'uji@contoh.test']);
    }

    private function redirectPath($response): ?string
    {
        return parse_url((string) $response->headers->get('Location'), PHP_URL_PATH);
    }

    public function test_get_checkout_tidak_mengarah_ke_dirinya_sendiri(): void
    {
        $response = $this->actingAs($this->user())->get('/checkout');

        $this->assertNotSame('/checkout', $this->redirectPath($response));
    }

    /**
     * Kasus nyata di produksi: pelanggan membuka /checkout dua kali berturut-turut.
     * Permintaan pertama menyimpan /checkout sebagai previous url di session,
     * sehingga back() pada permintaan kedua menunjuk balik ke /checkout.
     */
    public function test_checkout_dua_kali_berturut_turut_tetap_aman(): void
    {
        $user = $this->user();

        $this->actingAs($user)->get('/checkout');
        $response = $this->actingAs($user)->get('/checkout');

        $this->assertNotSame('/checkout', $this->redirectPath($response));
    }

    /**
     * Saat halaman checkout di-refresh, browser mengirim Referer berisi
     * /checkout itu sendiri.
     */
    public function test_checkout_dengan_referer_dirinya_sendiri_tetap_aman(): void
    {
        $response = $this->actingAs($this->user())
            ->get('/checkout', ['Referer' => url('/checkout')]);

        $this->assertNotSame('/checkout', $this->redirectPath($response));
    }

    /**
     * Token dibuat "tidak ditemukan" lewat mock supaya tes tidak bergantung
     * pada isi database.
     */
    private function fakeTokenTidakDitemukan(): void
    {
        $this->mock(
            PasswordResetAction::class,
            fn ($mock) => $mock->shouldReceive('getByToken')->andReturn(null)
        );
    }

    /**
     * Terjadi saat pelanggan mengklik tautan reset password yang sudah
     * kedaluwarsa atau sudah terpakai — token tidak ditemukan.
     */
    public function test_reset_password_token_tidak_valid_tidak_berputar(): void
    {
        $this->fakeTokenTidakDitemukan();
        $path = '/reset-password/token-yang-tidak-ada';

        $this->get($path);
        $response = $this->get($path);

        $this->assertTrue($response->isRedirect(), 'token tidak valid harus dialihkan, bukan error');
        $this->assertNotSame($path, $this->redirectPath($response));
    }

    public function test_reset_password_dengan_referer_dirinya_sendiri_tetap_aman(): void
    {
        $this->fakeTokenTidakDitemukan();
        $path = '/reset-password/token-yang-tidak-ada';

        $response = $this->get($path, ['Referer' => url($path)]);

        $this->assertTrue($response->isRedirect(), 'token tidak valid harus dialihkan, bukan error');
        $this->assertNotSame($path, $this->redirectPath($response));
    }

    /**
     * Sebelum perbaikan ini, kegagalan checkout_order() (alamat luar
     * jangkauan, stok habis, charge Midtrans gagal, dst.) memakai back(),
     * yang mengarah ke Referer pengguna: halaman /checkout tempat tombol
     * "Bayar Sekarang" berada. Karena GET /checkout kini selalu melempar ke
     * /cart, back() akan memantul lewat /checkout dan pesan error-nya hilang
     * di tengah jalan — pelanggan hanya melihat "kembali ke cart" tanpa
     * keterangan apa pun.
     */
    public function test_checkout_order_gagal_langsung_ke_cart_bukan_lewat_checkout(): void
    {
        $this->mock(ShippingCalculator::class, function ($mock) {
            $mock->shouldReceive('resolveUserAddress')->andReturn([
                'address' => 'Jl. Uji Coba',
                'latitude' => -7.9,
                'longitude' => 112.6,
            ]);
            $mock->shouldReceive('isWithinServiceArea')->andReturn(false);
        });

        $response = $this->actingAs($this->user())
            ->from(url('/checkout'))
            ->post('/checkout/payment', [
                'type' => 'buy-cart',
                'payment_type' => 'bca',
                'item_details' => '[]',
            ]);

        $this->assertTrue($response->isRedirect());
        $this->assertNotSame(
            '/checkout',
            $this->redirectPath($response),
            'kegagalan checkout memantul lewat /checkout sehingga pesan error hilang'
        );
        $this->assertSame('/cart', $this->redirectPath($response));
        $response->assertSessionHas('error', 'Alamat Anda di luar jangkauan pengiriman kami.');
    }
}
