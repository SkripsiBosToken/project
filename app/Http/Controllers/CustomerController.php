<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Actions\AuthAction;
use App\Actions\CartAction;
use App\Actions\Cart_ItemAction;
use App\Actions\MidtransAction;
use App\Actions\Order_ItemAction;
use App\Actions\OrderAction;
use App\Actions\Product_VariantAction;
use App\Actions\RateAction;
use App\Actions\SystemAction;
use App\Actions\TransactionAction;
use App\Actions\UserAction;
use App\Models\Order;
use App\Models\Product_Variant;
use App\Support\OrderStatus;
use App\Support\ShippingCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CustomerController extends Controller
{
    public function cart(CartAction $cart_action)
    {
        $cart = $cart_action->firstOrCreateForUser(Auth::id());
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request, CartAction $cart_action, Cart_ItemAction $cart_item_action, AuthAction $auth_action)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'string', 'exists:product_variants,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $variant = Product_Variant::find($validated['product_variant_id']);
        $cart = $cart_action->firstOrCreateForUser(Auth::id());
        $matchData = $cart_item_action->matchCart($cart->id, $validated['product_variant_id']);

        // Jumlah di keranjang tidak boleh melebihi stok yang tersedia.
        $requested = (int) $validated['qty'] + ($matchData ? (int) $matchData['qty'] : 0);
        if ($requested > (int) $variant->stock) {
            return back()->with('error', 'Stok ' . $variant->name_type . ' tersisa ' . $variant->stock . '.');
        }

        if ($matchData) {
            $cart_item_action->updateStock($matchData['id'], $requested);
        } else {
            $request['qty'] = (int) $validated['qty'];
            $request['cart_id'] = $cart->id;
            $cart_item_action->create($request);
        }

        return redirect()->route('cart')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function deleteCart(Cart_ItemAction $cart_item_action, $id)
    {
        // Tanpa pengecekan kepemilikan, siapa pun bisa menghapus item
        // di keranjang user lain hanya dengan menebak id-nya.
        if (! $cart_item_action->deleteForUser($id, Auth::id())) {
            return redirect()->route('cart')->with('error', 'Item keranjang tidak ditemukan.');
        }

        return redirect()->route('cart')->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkout(Request $request, Product_VariantAction $product_variant_action, Cart_ItemAction $cart_item_action, CartAction $cart_action, AuthAction $auth_action, ShippingCalculator $shipping)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:buy-cart,buy-directly'],
        ]);

        $user = $auth_action->getuser();
        $type = $validated['type'];
        $datas = [];

        if ($type === 'buy-directly') {
            $request->validate([
                'product_variant_id' => ['required', 'string', 'exists:product_variants,id'],
                'qty' => ['required', 'integer', 'min:1'],
            ]);

            $datas[] = [
                'product' => $product_variant_action->getById($request['product_variant_id']),
                'qty' => (int) $request['qty'],
            ];
        }

        if ($type === 'buy-cart') {
            $items = json_decode((string) $request['items'], true);

            if (! is_array($items) || $items === []) {
                return redirect()->route('cart')->with('error', 'Pilih minimal satu produk untuk di-checkout.');
            }

            foreach ($items as $item) {
                $variant = $product_variant_action->getById($item['product_variant_id'] ?? null);

                if ($variant) {
                    $datas[] = ['product' => $variant, 'qty' => (int) ($item['qty'] ?? 0)];
                }
            }
        }

        if ($datas === []) {
            return redirect()->route('cart')->with('error', 'Produk yang dipilih tidak tersedia.');
        }

        // Ongkir dihitung dari alamat tersimpan milik user, bukan dari input.
        try {
            $address = $shipping->resolveUserAddress($user);
            $shippingCost = $shipping->cost($address['latitude'], $address['longitude']);
            $outOfRange = ! $shipping->isWithinServiceArea($address['latitude'], $address['longitude']);
        } catch (RuntimeException $e) {
            return redirect()->route('profile')->with('error', $e->getMessage());
        }

        return view('customer.checkout', compact('datas', 'type', 'shippingCost', 'address', 'outOfRange'));
    }

    public function logout(AuthAction $auth_action)
    {
        $auth_action->logout();
        return redirect()->route('login');
    }

    /**
     * Membuat pesanan lalu menagihkannya ke Midtrans.
     *
     * Alurnya dua tahap supaya tidak ada pelanggan yang tertagih tanpa punya
     * pesanan, dan tidak ada stok yang berkurang tanpa tagihan:
     *   1. Semua perubahan lokal (pesanan, item, potong stok, kosongkan
     *      keranjang) dijalankan dalam satu transaksi database.
     *   2. Baru menagih ke Midtrans. Bila penagihan gagal, perubahan tahap 1
     *      dikompensasi: stok dikembalikan dan pesanan ditandai gagal.
     */
    public function checkout_order(Request $request, MidtransAction $midtrans_action, AuthAction $auth_action, Product_VariantAction $product_variant_action, OrderAction $order_action, Order_ItemAction $order_item_action, CartAction $cart_action, Cart_ItemAction $cart_item_action, TransactionAction $transaction_action, ShippingCalculator $shipping)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:buy-cart,buy-directly'],
            'payment_type' => ['required', 'in:' . implode(',', config('midtrans.payment_types'))],
            'item_details' => ['required', 'string'],
        ]);

        $user = $auth_action->getuser();

        // Alamat dan ongkir SELALU diambil dari data user di server. Sebelumnya
        // nilai ini dibaca dari hidden input, sehingga pelanggan bisa mengirim
        // koordinat palsu untuk mendapatkan ongkir nol.
        try {
            $address = $shipping->resolveUserAddress($user);

            if (! $shipping->isWithinServiceArea($address['latitude'], $address['longitude'])) {
                return back()->with('error', 'Alamat Anda di luar jangkauan pengiriman kami.');
            }

            $shippingPrice = $shipping->cost($address['latitude'], $address['longitude']);
        } catch (RuntimeException $e) {
            return redirect()->route('profile')->with('error', $e->getMessage());
        }

        // Gabungkan jumlah per varian supaya id yang dikirim ganda tidak
        // membuat dua baris order_item untuk produk yang sama.
        $requestedQty = [];
        foreach ((array) json_decode($validated['item_details'], true) as $item) {
            $variantId = $item['id'] ?? null;
            $qty = (int) ($item['quantity'] ?? 0);

            if (! $variantId || $qty < 1) {
                return back()->with('error', 'Jumlah produk yang dipesan tidak valid.');
            }

            $requestedQty[$variantId] = ($requestedQty[$variantId] ?? 0) + $qty;
        }

        if ($requestedQty === []) {
            return back()->with('error', 'Tidak ada produk yang dipesan.');
        }

        $cartId = $validated['type'] === 'buy-cart'
            ? $cart_action->firstOrCreateForUser($user['id'])->id
            : null;

        try {
            [$orderId, $itemDetails, $grossAmount] = DB::transaction(function () use ($requestedQty, $shippingPrice, $address, $user, $cartId, $validated, $order_action, $order_item_action, $cart_item_action) {
                // lockForUpdate menahan baris varian sampai transaksi selesai,
                // sehingga dua checkout bersamaan tidak bisa sama-sama lolos
                // pengecekan stok dan menyebabkan overselling.
                $variants = Product_Variant::with('product')
                    ->whereIn('id', array_keys($requestedQty))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $itemDetails = [];
                $grossAmount = $shippingPrice;

                foreach ($requestedQty as $variantId => $qty) {
                    $variant = $variants->get($variantId);

                    if (! $variant) {
                        throw new RuntimeException('Salah satu produk yang dipesan sudah tidak tersedia.');
                    }

                    if ((int) $variant->stock < $qty) {
                        throw new RuntimeException(
                            'Stok ' . $variant->product->name . ' - ' . $variant->name_type .
                            ' tersisa ' . $variant->stock . ', tidak cukup untuk ' . $qty . ' pesanan.'
                        );
                    }

                    // Harga diambil dari database, bukan dari input pelanggan.
                    $price = (int) $variant->price;

                    $itemDetails[] = [
                        'id' => $variantId,
                        'name' => Str::limit($variant->product->name . ' - ' . $variant->name_type, 50, ''),
                        'quantity' => $qty,
                        'price' => $price,
                    ];

                    $grossAmount += $price * $qty;
                }

                $orderId = $order_action->create([
                    'status' => OrderStatus::UNPAID,
                    'total_price' => $grossAmount,
                    'shipping_address' => json_encode($address),
                    // Diisi setelah Midtrans mengembalikan transaction_id.
                    'transaction_id' => '',
                    'user_id' => $user['id'],
                ]);

                foreach ($itemDetails as $item) {
                    $order_item_action->create([
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'order_id' => $orderId,
                        'cart_id' => $cartId,
                        'product_variant_id' => $item['id'],
                    ]);

                    DB::table('product_variants')
                        ->where('id', $item['id'])
                        ->decrement('stock', $item['quantity']);
                }

                if ($validated['type'] === 'buy-cart' && $cartId) {
                    $cart_item_action->deleteByCartId($cartId);
                }

                // Ongkir ditambahkan terakhir agar tidak ikut jadi order_item.
                $itemDetails[] = [
                    'id' => 'shipping-cost',
                    'name' => 'Ongkos Kirim',
                    'quantity' => 1,
                    'price' => $shippingPrice,
                ];

                return [$orderId, $itemDetails, $grossAmount];
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $response = $midtrans_action->chargeTransaction(
            $this->buildChargePayload($validated['payment_type'], $orderId, $grossAmount, $itemDetails, $user)
        );

        // Penagihan gagal: kembalikan stok dan tandai pesanan gagal supaya
        // tidak ada stok yang tertahan oleh pesanan yang tidak pernah terbit.
        if (! $midtrans_action->isSuccessful($response) || empty($response['transaction_id'])) {
            $this->releaseOrder($orderId);

            Log::error('Checkout gagal saat charge Midtrans', [
                'order_id' => $orderId,
                'status_message' => $response['status_message'] ?? null,
            ]);

            return back()->with('error', 'Pembayaran gagal dibuat: ' .
                ($response['status_message'] ?? 'gateway tidak merespons') . '. Silakan coba lagi.');
        }

        $order_action->updateTransactionId($orderId, $response['transaction_id']);

        $transaction_action->create([
            'status' => $response['transaction_status'] ?? 'pending',
            'transaction_id' => $response['transaction_id'],
            'invoice_id' => $response['order_id'] ?? '',
            'order_id' => $orderId,
        ]);

        return redirect()->route('payment', ['id' => $orderId]);
    }

    /**
     * Menyusun payload charge Midtrans untuk VA bank maupun QRIS.
     */
    private function buildChargePayload(string $paymentType, string $orderId, int $grossAmount, array $itemDetails, $user): array
    {
        $nameParts = array_filter(explode(' ', trim((string) $user['name'])));
        $firstName = array_shift($nameParts) ?: 'Pelanggan';

        $payload = [
            'payment_type' => $paymentType === 'qris' ? 'qris' : 'bank_transfer',
            'transaction_details' => [
                // Menyertakan order id internal agar notifikasi Midtrans mudah
                // ditelusuri balik ke pesanan yang benar.
                'order_id' => 'KC-' . $orderId . '-' . time(),
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $firstName,
                'last_name' => implode(' ', $nameParts),
                'email' => $user['email'],
                'phone' => $user['phone_number'],
            ],
            'custom_expiry' => [
                'unit' => 'minute',
                'expiry_duration' => (int) config('midtrans.expiry_minutes'),
            ],
        ];

        if ($paymentType !== 'qris') {
            $payload['bank_transfer'] = [
                'bank' => $paymentType,
                $paymentType . '_va' => [
                    'recipient_name' => config('midtrans.recipient_name'),
                ],
            ];
        }

        return $payload;
    }

    /**
     * Kompensasi bila penagihan gagal: kembalikan stok dan tandai gagal.
     */
    private function releaseOrder(string $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::with('order_items')->find($orderId);

            if (! $order || OrderStatus::isFailed($order->status)) {
                return;
            }

            foreach ($order->order_items as $item) {
                if ($item->product_variant_id) {
                    DB::table('product_variants')
                        ->where('id', $item->product_variant_id)
                        ->increment('stock', (int) $item->quantity);
                }
            }

            $order->status = OrderStatus::FAILED;
            $order->save();
        });
    }

    public function payment($id, OrderAction $order_action, MidtransAction $midtrans_action)
    {
        // Tanpa filter user_id, siapa pun yang login bisa membuka halaman
        // pembayaran (termasuk nomor VA) milik pesanan orang lain.
        $order = $order_action->getByIdForUser($id, Auth::id());

        abort_if(! $order, 404);

        if (empty($order->transaction_id)) {
            return redirect()->route('order-list')
                ->with('error', 'Pembayaran untuk pesanan ini belum terbit.');
        }

        $data = $midtrans_action->getTransaction($order->transaction_id);

        // Sebelumnya kondisi ini me-redirect ke route yang sama sehingga
        // menghasilkan redirect loop tanpa akhir.
        if (empty($data['transaction_status'])) {
            return redirect()->route('order-detail', ['id' => $id])
                ->with('error', 'Status pembayaran belum bisa diambil. Silakan coba beberapa saat lagi.');
        }

        $url = '';
        if (($data['payment_type'] ?? null) === 'qris') {
            $url = $midtrans_action->endpoint . 'v2/qris/' . $data['transaction_id'] . '/qr-code';
        }

        return view('customer.payment', compact('data', 'url', 'order'));
    }

    public function callback(Request $request, MidtransAction $midtrans_action)
    {
        return $midtrans_action->callback($request);
    }

    /**
     * Endpoint JSON untuk halaman pembayaran.
     *
     * Selain melaporkan status, endpoint ini juga menyelaraskan status pesanan
     * dengan Midtrans. Dengan begitu pesanan tetap terupdate walaupun webhook
     * gagal sampai (mis. saat aplikasi berjalan di localhost).
     */
    public function paymentStatus($id, OrderAction $order_action, MidtransAction $midtrans_action)
    {
        $order = $order_action->getByIdForUser($id, Auth::id());

        if (! $order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        $data = $order->transaction_id
            ? $midtrans_action->getTransaction($order->transaction_id)
            : [];

        if (! empty($data['transaction_status'])) {
            $changed = $midtrans_action->applyTransactionStatus(
                $order,
                $data['transaction_status'],
                $data['fraud_status'] ?? null,
                (int) ($data['gross_amount'] ?? $order->total_price)
            );

            if ($changed) {
                $order->refresh();
            }
        }

        return response()->json([
            'order_status' => $order->status,
            'transaction_status' => $data['transaction_status'] ?? null,
            'expiry_time' => $data['expiry_time'] ?? null,
            'is_paid' => OrderStatus::isPaid($order->status),
            'is_failed' => OrderStatus::isFailed($order->status),
        ]);
    }

    /**
     * @deprecated Gunakan App\Support\ShippingCalculator. Dipertahankan agar
     *             pemanggil lama tidak putus.
     */
    public function calculateShippingCost($latitude, $longitude)
    {
        return app(ShippingCalculator::class)->cost((float) $latitude, (float) $longitude);
    }

    public function order_list(AuthAction $auth_action, RateAction $rate_action)
    {
        $datas = $auth_action->getuser()->orders;
        foreach ($datas as $key => $value) {
            $rate = $rate_action->getByOrder($value['id']);
            $datas[$key]['rate'] = $rate;
        }
        return view('customer.order-list', compact('datas'));
    }

    public function order_detail($id, TransactionAction $transaction_action, MidtransAction $midtrans_action, OrderAction $order_action, UserAction $user_action)
    {
        // Pesanan hanya boleh dibuka oleh pemiliknya.
        $order = $order_action->getByIdForUser($id, Auth::id());

        abort_if(! $order, 404);

        $invoiceData = $transaction_action->getByOrderId($id);

        // Pesanan yang penagihannya gagal tidak punya baris transaction,
        // jadi jangan sampai fatal saat mengakses transaction_id.
        $transaction = $invoiceData
            ? $midtrans_action->getTransaction($invoiceData['transaction_id'])
            : [];

        $user = $user_action->getById($order['user_id']);

        return view('customer.order-detail', compact('order', 'user', 'transaction'));
    }

    public function profile(AuthAction $auth_action)
    {
        $data = $auth_action->getuser();
        return view('customer.profile', compact('data'));
    }

    public function updateProfile(Request $request, AuthAction $auth_action, UserAction $user_action)
    {
        // Koordinat divalidasi di sini karena perhitungan ongkir bergantung
        // penuh pada isinya; nilai kosong sebelumnya tersimpan diam-diam dan
        // baru meledak saat checkout.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ]);

        $user = $auth_action->getuser();

        $user_action->update($user['id'], [
            'name' => $validated['name'],
            'address' => json_encode([
                'address' => $validated['address'],
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
                'postal_code' => $validated['postal_code'] ?? '',
            ]),
            'phone_number' => $validated['phone'],
            'birth_date' => $validated['birth_date'] ?? null,
        ]);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Membatalkan pesanan yang belum dibayar.
     *
     * Versi sebelumnya langsung menghapus order, order_items, dan transaction
     * tanpa memeriksa apakah Midtrans benar-benar menerima pembatalan, sehingga
     * pesanan yang sudah dibayar bisa ikut terhapus beserta riwayatnya.
     * Sekarang pembatalan hanya lanjut bila gateway mengonfirmasi, dan data
     * pesanan dipertahankan dengan status "Gagal".
     */
    public function cancelPayment($id, AuthAction $auth_action, OrderAction $order_action, Order_ItemAction $order_item_action, MidtransAction $midtrans_action, Product_VariantAction $product_variant_action, TransactionAction $transaction_action)
    {
        $order = $order_action->getByIdForUser($id, Auth::id());

        abort_if(! $order, 404);

        if ($order->status !== OrderStatus::UNPAID) {
            return redirect()->route('order-list')
                ->with('error', 'Hanya pesanan yang belum dibayar yang bisa dibatalkan.');
        }

        $response = $midtrans_action->cancelTransaction($order->transaction_id);

        // 412 = transaksi sudah berubah status di sisi Midtrans (mis. baru saja
        // dibayar). Jangan batalkan apa pun secara lokal bila itu terjadi.
        if (! $midtrans_action->isSuccessful($response)) {
            return redirect()->route('order-list')->with(
                'error',
                'Pesanan tidak dapat dibatalkan: ' .
                    ($response['status_message'] ?? 'gateway pembayaran menolak pembatalan.')
            );
        }

        DB::transaction(function () use ($order, $transaction_action) {
            foreach ($order->order_items as $item) {
                if ($item->product_variant_id) {
                    DB::table('product_variants')
                        ->where('id', $item->product_variant_id)
                        ->increment('stock', (int) $item->quantity);
                }
            }

            if ($order->transaction) {
                $transaction_action->updateStatus($order->transaction->id, 'cancel');
            }

            $order->status = OrderStatus::FAILED;
            $order->save();
        });

        return redirect()->route('order-list')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function submitReview(Request $request, RateAction $rate_action, AuthAction $auth_action, OrderAction $order_action)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string'],
            'rate' => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Ulasan hanya boleh dibuat oleh pemilik pesanan, untuk pesanan yang
        // sudah selesai, dan hanya sekali per pesanan.
        $order = $order_action->getByIdForUser($validated['order_id'], Auth::id());

        abort_if(! $order, 404);

        if ($order->status !== OrderStatus::COMPLETED) {
            return redirect()->route('order-list')
                ->with('error', 'Ulasan hanya bisa diberikan untuk pesanan yang sudah selesai.');
        }

        if ($rate_action->existsForOrder($order->id)) {
            return redirect()->route('order-list')
                ->with('error', 'Pesanan ini sudah pernah diulas.');
        }

        $rate_action->create([
            'user_id' => Auth::id(),
            'rate' => $validated['rate'],
            'message' => $validated['message'] ?? '',
            'order_id' => $validated['order_id'],
        ]);

        return redirect()->route('order-list')->with('success', 'Terima kasih atas ulasan Anda.');
    }

}
