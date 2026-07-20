<?php

namespace Tests\Feature;

use App\Actions\MidtransAction;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'midtrans.endpoint' => 'https://api.midtrans.com/',
            'midtrans.server_key' => 'dummy-key',
            'midtrans.retry_delay' => 0,
            'midtrans.retry_times' => 3,
        ]);
    }

    private function payload(): array
    {
        return ['transaction_details' => ['order_id' => 'ORDER-1', 'gross_amount' => 50000]];
    }

    /**
     * Error yang dilaporkan di produksi: cURL 2 "getaddrinfo() thread failed
     * to start". Sebelum perbaikan ini exception-nya lolos ke atas dan
     * pelanggan melihat halaman error.
     */
    public function test_kegagalan_dns_dikembalikan_sebagai_array_bukan_exception(): void
    {
        $attempts = 0;

        Http::fake(function () use (&$attempts) {
            $attempts++;
            throw new RequestException(
                'cURL error 2: getaddrinfo() thread failed to start',
                new Request('POST', 'https://api.midtrans.com/v2/charge'),
                null,
                null,
                ['errno' => 2]
            );
        });

        $response = (new MidtransAction)->chargeTransaction($this->payload());

        $this->assertSame('0', $response['status_code']);
        $this->assertFalse((new MidtransAction)->isSuccessful($response));
        $this->assertSame(3, $attempts, 'error DNS/koneksi harus diulang');
    }

    /**
     * Timeout tidak boleh diulang: permintaan bisa saja sudah diterima
     * Midtrans, sehingga mengulang berisiko menagih pelanggan dua kali.
     */
    public function test_timeout_tidak_diulang_untuk_mencegah_penagihan_ganda(): void
    {
        $attempts = 0;

        Http::fake(function () use (&$attempts) {
            $attempts++;
            throw new ConnectException(
                'cURL error 28: Operation timed out',
                new Request('POST', 'https://api.midtrans.com/v2/charge'),
                null,
                ['errno' => 28]
            );
        });

        $response = (new MidtransAction)->chargeTransaction($this->payload());

        $this->assertSame('0', $response['status_code']);
        $this->assertSame(1, $attempts, 'timeout tidak boleh diulang');
    }

    public function test_charge_sukses_tetap_berjalan_normal(): void
    {
        Http::fake([
            '*' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'trx-123',
                'transaction_status' => 'pending',
            ]),
        ]);

        $action = new MidtransAction;
        $response = $action->chargeTransaction($this->payload());

        $this->assertTrue($action->isSuccessful($response));
        $this->assertSame('trx-123', $response['transaction_id']);
    }

    public function test_cek_status_transaksi_juga_aman_saat_gateway_mati(): void
    {
        Http::fake(function () {
            throw new RequestException(
                'cURL error 2: getaddrinfo() thread failed to start',
                new Request('GET', 'https://api.midtrans.com/v2/trx-1/status'),
                null,
                null,
                ['errno' => 2]
            );
        });

        $this->assertSame('0', (new MidtransAction)->getTransaction('trx-1')['status_code']);
    }
}
