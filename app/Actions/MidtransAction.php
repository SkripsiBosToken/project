<?php

namespace App\Actions;

use App\Models\Order;
use App\Support\OrderStatus;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransAction
{
    /** Status transaksi Midtrans yang berarti pembayaran gagal / batal. */
    private const FAILED_STATUSES = ['deny', 'cancel', 'expire', 'failure'];

    private string $serverKey;
    private string $clientKey;
    private string $merchantId;
    public string $endpoint;

    public function __construct()
    {
        // config() dipakai (bukan env()) supaya nilainya tetap terbaca
        // setelah `php artisan config:cache` dijalankan di produksi.
        $this->serverKey = (string) config('midtrans.server_key');
        $this->clientKey = (string) config('midtrans.client_key');
        $this->merchantId = (string) config('midtrans.merchant_id');
        $this->endpoint = (string) config('midtrans.endpoint');
    }

    /**
     * HTTP client dengan autentikasi, timeout, dan retry standar.
     */
    private function client(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            'Accept' => 'application/json',
        ])->timeout((int) config('midtrans.timeout', 15));
    }

    /**
     * Midtrans membalas status_code sebagai string ("200", "201", "407", ...).
     * 2xx berarti permintaan diterima; 407 = expired; selain itu gagal.
     */
    public function isSuccessful(?array $response): bool
    {
        if (! is_array($response) || ! isset($response['status_code'])) {
            return false;
        }

        return in_array((string) $response['status_code'], ['200', '201'], true);
    }

    public function getTransaction($transaction_id): array
    {
        $response = $this->client()
            ->get($this->endpoint . 'v2/' . $transaction_id . '/status')
            ->json();

        return is_array($response) ? $response : [];
    }

    public function chargeTransaction($request): array
    {
        $response = $this->client()
            ->post($this->endpoint . 'v2/charge', $request)
            ->json();

        $response = is_array($response) ? $response : [];

        if (! $this->isSuccessful($response)) {
            Log::error('Midtrans charge gagal', [
                'order_id' => $request['transaction_details']['order_id'] ?? null,
                'status_code' => $response['status_code'] ?? null,
                'status_message' => $response['status_message'] ?? null,
            ]);
        }

        return $response;
    }

    public function getInvoice($invoice_id): array
    {
        $response = $this->client()
            ->get($this->endpoint . 'v1/invoices/' . $invoice_id)
            ->json();

        return is_array($response) ? $response : [];
    }

    public function createInvoice($request): array
    {
        $response = $this->client()
            ->post($this->endpoint . 'v1/invoices', $request)
            ->json();

        return is_array($response) ? $response : [];
    }

    public function cancelTransaction($transaction_id): array
    {
        $response = $this->client()
            ->asForm()
            ->post($this->endpoint . 'v2/' . $transaction_id . '/cancel')
            ->json();

        $response = is_array($response) ? $response : [];

        if (! $this->isSuccessful($response)) {
            Log::warning('Midtrans cancel gagal', [
                'transaction_id' => $transaction_id,
                'status_code' => $response['status_code'] ?? null,
                'status_message' => $response['status_message'] ?? null,
            ]);
        }

        return $response;
    }

    public function refundTransaction($transaction_id, $request): array
    {
        $response = $this->client()
            ->asJson()
            ->post($this->endpoint . 'v2/' . $transaction_id . '/refund', $request)
            ->json();

        $response = is_array($response) ? $response : [];

        if (! $this->isSuccessful($response)) {
            Log::warning('Midtrans refund gagal', [
                'transaction_id' => $transaction_id,
                'status_code' => $response['status_code'] ?? null,
                'status_message' => $response['status_message'] ?? null,
            ]);
        }

        return $response;
    }

    /**
     * Memetakan status transaksi Midtrans ke status pesanan internal.
     *
     * `capture` hanya dianggap lunas bila fraud_status-nya `accept`; bila
     * `challenge` transaksi masih menunggu review sehingga tetap belum dibayar.
     */
    private function mapOrderStatus(string $transactionStatus, ?string $fraudStatus): ?string
    {
        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge'
                ? OrderStatus::UNPAID
                : OrderStatus::WAITING_CONFIRMATION,
            'settlement' => OrderStatus::WAITING_CONFIRMATION,
            'pending' => OrderStatus::UNPAID,
            'deny', 'cancel', 'expire', 'failure' => OrderStatus::FAILED,
            'refund', 'partial_refund' => OrderStatus::REFUNDED,
            default => null,
        };
    }

    /**
     * Menangani notifikasi (webhook) dari Midtrans.
     *
     * Midtrans mengirim ulang notifikasi yang sama bila endpoint tidak membalas
     * 200, jadi handler ini dibuat idempoten: perubahan status, pengembalian
     * stok, dan pemberian poin hanya dijalankan sekali per transisi status.
     */
    public function callback($request): array
    {
        $orderId = $request['order_id'] ?? null;
        $statusCode = $request['status_code'] ?? null;
        $grossAmount = $request['gross_amount'] ?? null;
        $signature = $request['signature_key'] ?? null;
        $transactionStatus = $request['transaction_status'] ?? null;
        $transactionId = $request['transaction_id'] ?? null;

        if (! $orderId || ! $signature || ! $transactionStatus || ! $transactionId) {
            return ['success' => 0, 'message' => 'payload tidak lengkap'];
        }

        // hash_equals mencegah timing attack saat membandingkan signature.
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        if (! hash_equals($expected, (string) $signature)) {
            Log::warning('Midtrans callback: signature tidak valid', ['order_id' => $orderId]);

            return ['success' => 0, 'message' => 'signature invalid'];
        }

        $order = Order::with('transaction', 'order_items')
            ->where('transaction_id', $transactionId)
            ->first();

        if (! $order) {
            Log::warning('Midtrans callback: pesanan tidak ditemukan', [
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
            ]);

            // Dibalas sukses supaya Midtrans berhenti mengirim ulang notifikasi
            // untuk transaksi yang memang tidak ada di sistem ini.
            return ['success' => 1, 'message' => 'order tidak ditemukan'];
        }

        $this->applyTransactionStatus(
            $order,
            $transactionStatus,
            $request['fraud_status'] ?? null,
            (int) $grossAmount
        );

        return ['success' => 1, 'message' => 'success request ' . $transactionStatus];
    }

    /**
     * Menerapkan status transaksi Midtrans ke pesanan secara idempoten.
     *
     * Dipakai oleh webhook maupun polling status dari halaman pembayaran,
     * sehingga pesanan tetap tersinkron walaupun notifikasi Midtrans gagal
     * terkirim (mis. saat pengembangan di localhost).
     *
     * @return bool true bila status pesanan benar-benar berubah.
     */
    public function applyTransactionStatus(Order $order, string $transactionStatus, ?string $fraudStatus, int $grossAmount): bool
    {
        $newOrderStatus = $this->mapOrderStatus($transactionStatus, $fraudStatus);

        if ($newOrderStatus === null) {
            return false;
        }

        return (bool) DB::transaction(function () use ($order, $transactionStatus, $newOrderStatus, $grossAmount) {
            // Kunci baris pesanan agar dua notifikasi yang datang bersamaan
            // tidak sama-sama lolos pengecekan idempotensi di bawah.
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! $locked) {
                return false;
            }

            $previousStatus = $locked->status;

            if ($order->transaction) {
                $order->transaction->update(['status' => $transactionStatus]);
            }

            if ($previousStatus === $newOrderStatus) {
                return false; // sudah diproses sebelumnya
            }

            $locked->status = $newOrderStatus;
            $locked->save();

            // Poin hanya diberikan pada transisi pertama menjadi lunas.
            if (! OrderStatus::isPaid($previousStatus) && OrderStatus::isPaid($newOrderStatus)) {
                $this->awardLoyaltyPoints($locked, $grossAmount);
            }

            // Stok dikembalikan sekali saja, dan hanya bila pesanan belum lunas.
            if (in_array($transactionStatus, self::FAILED_STATUSES, true)
                && ! OrderStatus::isFailed($previousStatus)
                && ! OrderStatus::isPaid($previousStatus)) {
                $this->restoreStock($locked);
            }

            return true;
        });
    }

    /**
     * Mengembalikan stok varian produk milik pesanan yang batal / gagal.
     */
    private function restoreStock(Order $order): void
    {
        // Relasi dimuat eksplisit: instance yang dikunci lockForUpdate()
        // belum tentu membawa order_items.
        $order->loadMissing('order_items');

        foreach ($order->order_items as $item) {
            if (! $item->product_variant_id) {
                continue;
            }

            DB::table('product_variants')
                ->where('id', $item->product_variant_id)
                ->increment('stock', (int) $item->quantity);
        }

        Log::info('Stok dikembalikan untuk pesanan gagal', ['order_id' => $order->id]);
    }

    /**
     * 1 poin untuk setiap kelipatan Rp100.000 dari nilai transaksi.
     */
    private function awardLoyaltyPoints(Order $order, int $grossAmount): void
    {
        $points = intdiv($grossAmount, 100000);

        if ($points > 0 && $order->user_id) {
            DB::table('users')->where('id', $order->user_id)->increment('point', $points);
        }
    }
}
