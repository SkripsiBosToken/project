<?php

use Illuminate\Support\Str;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

/*
 * robots.txt disajikan lewat route (bukan file statis) supaya baris Sitemap
 * selalu berisi URL absolut sesuai APP_URL di tiap environment — spesifikasi
 * robots.txt mengharuskan URL absolut, path relatif bisa diabaikan crawler.
 */
Route::get('/robots.txt', function () {
    $disallow = [
        '/admin/', '/dashboard', '/cart', '/checkout', '/payment/', '/cancel-payment/',
        '/order-list', '/order-detail/', '/order/receipt/', '/profile',
        '/login', '/register', '/forgot-password', '/reset-password/',
    ];

    $lines = ['User-agent: *'];

    // Halaman privat & transaksional: spesifik per pengguna, tidak punya nilai
    // pencarian, dan hanya menghabiskan crawl budget.
    foreach ($disallow as $path) {
        $lines[] = 'Disallow: ' . $path;
    }

    $lines[] = '';
    $lines[] = 'Allow: /catalogue';
    $lines[] = 'Allow: /catalogue-detail/';
    $lines[] = '';
    $lines[] = 'Sitemap: ' . url('/sitemap.xml');

    return response(implode("\n", $lines) . "\n", 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(
            Url::create('/')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        )
        ->add(
            Url::create('/catalogue')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        )
        ->add(
            Url::create('/about')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.5)
        )
        ->add(
            Url::create('/contact-us')
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.5)
        );

    // Hanya produk yang benar-benar bisa dibeli yang dimasukkan. Produk tanpa
    // varian aktif akan menjadi halaman kosong — merugikan kualitas indeks.
    $products = \App\Models\Product::with('product_variants')->get()
        ->filter(fn ($product) => $product->product_variants->whereNull('deleted_at')->isNotEmpty());

    foreach ($products as $product) {
        // URL-nya harus sama persis dengan yang ditautkan di situs (berslug),
        // supaya sitemap dan tautan internal tidak menunjuk URL berbeda.
        $sitemap->add(
            Url::create('/catalogue-detail/' . $product->id . '/' . Str::slug($product->name))
                ->setLastModificationDate(
                    $product->updated_at instanceof \DateTimeInterface ? $product->updated_at : now()
                )
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8)
        );
    }

    return $sitemap;
});


Route::get('/', [GuestController::class, 'landing'])->name('home');
Route::get('about', [GuestController::class, 'about'])->name('about');
Route::get('contact-us', [GuestController::class, 'contact'])->name('contact');
Route::get('catalogue', [GuestController::class, 'catalogue'])->name('catalogue');
// Segmen {slug} bersifat opsional agar tautan lama (tanpa slug) tetap hidup.
// Sebelumnya route ini tidak punya segmen slug, sehingga slug yang dikirim
// dari view menempel sebagai query string (?slug=...) dan membuat setiap
// produk punya dua URL berbeda — sinyal peringkatnya jadi terpecah.
Route::get('catalogue-detail/{id}/{slug?}', [GuestController::class, 'catalogue_detail'])->name('catalogue-detail');

Route::get('login', [GuestController::class, 'login'])->name('login');
Route::post('login', [GuestController::class, 'auth'])->name('login');

Route::get('register', [GuestController::class, 'sign_up'])->name('register');
Route::post('register', [GuestController::class, 'register'])->name('register');

Route::get('forgot-password', [GuestController::class, 'forgotPassword'])->name('forgot-password');
Route::post('forgot-password', [GuestController::class, 'sendResetPassword'])->name('forgot-password');

Route::get('reset-password/{token}', [GuestController::class, 'resetPassword'])->name('reset-password');
Route::post('reset-password/{token}', [GuestController::class, 'requestResetPassword'])->name('reset-password');

Route::middleware(['auth'])->group(function () {
    Route::get('cart', [CustomerController::class, 'cart'])->name('cart');
    Route::post('cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::get('cart/delete/{id}', [CustomerController::class, 'deleteCart'])->name('cart.delete');

    /*
     * Halaman checkout hanya terbit lewat POST dari keranjang. Akses GET
     * (URL diketik langsung, bookmark, atau refresh) diarahkan ke keranjang.
     *
     * back() tidak dipakai di sini: tanpa Referer ia memakai "previous url"
     * dari session — yang justru bernilai /checkout sendiri karena setiap
     * permintaan GET menyimpan URL-nya ke session — sehingga rute ini
     * mengarahkan ke dirinya sendiri dan browser berputar sampai menyerah
     * (ERR_TOO_MANY_REDIRECTS).
     */
    Route::get('checkout', fn () => redirect()->route('cart'))->name('checkout');
    Route::post('checkout', [CustomerController::class, 'checkout'])->name('checkout');

    Route::post('checkout/payment', [CustomerController::class, 'checkout_order'])->name('checkout.payment');
    Route::get('payment/{id}', [CustomerController::class, 'payment'])->name('payment');
    // Dipakai halaman pembayaran untuk memantau status tanpa reload manual.
    Route::get('payment/{id}/status', [CustomerController::class, 'paymentStatus'])->name('payment.status');
    Route::get('cancel-payment/{id}', [CustomerController::class, 'cancelPayment'])->name('cancel.payment');

    Route::get('order-list', [CustomerController::class, 'order_list'])->name('order-list');
    Route::get('order-detail/{id}', [CustomerController::class, 'order_detail'])->name('order-detail');

    Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
    Route::post('profile', [CustomerController::class, 'updateProfile'])->name('profile.update');

    Route::post('submit-review', [CustomerController::class, 'submitReview']);
    
    Route::get('order/receipt/{id}', [AdminController::class, 'generateInvoice'])->name('getReceipt');

    Route::get('logout', [CustomerController::class, 'logout'])->name('logout');
});
