<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;
use App\Models\Product_Variant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OrderTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    private $serverKey;
    private $endpoint;
    public $vaNumber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serverKey = env('MIDTRANS_SERVER_KEY');
        $this->endpoint = env('MIDTRANS_ENDPOINT');
        $this->vaNumber = env('MIDTRANS_VA_NUMBER');
    }

    /** @test */
    public function make_transaction()
    {
        $product = Product_Variant::with('product.category')->first();
        $amount = 5;

        $request = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => Str::uuid()->toString(),
                'gross_amount' => $amount * $product['price']
            ],
            'item_details' => [
                [
                    'id' => Str::uuid()->toString(),
                    'name' => $product['product']['name_type'] . ' - ' . $product['name'],
                    'quantity' => $amount,
                    'price' => $product['price']
                ],
            ],
            'customer_details' => [
                'first_name' => 'Faiz',
                'last_name' => 'Diandra Maulana',
                'email' => 'user@user.com',
                'phone' => '081232857502'
            ],
            'bank_transfer' => [
                'bank' => 'bni',
                'bni_va' => [
                    'va_number' => '1234567890',
                    'recipient_name' => 'Kusuka Catering'
                ]
            ]
        ];
        $url = $this->endpoint . 'v2/charge';
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
        ];
        $fetch = Http::withHeaders($headers)->post($url, $request);
        $result = $fetch->json();

        $this->assertEquals('201', $result['status_code']);
    }

    /** @test */
    public function get_transaction()
    {
        $transaction_id = 'c0e26557-6387-4a26-9595-bf6dd365bb7e';
        $url = $this->endpoint . 'v2/' . $transaction_id . '/status';
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
        ];
        $fetch = Http::withHeaders($headers)->get($url);
        $result = $fetch->json();

        $this->assertEquals('407', $result['status_code']);
    }

    /** @test */
    public function calculation_order()
    {
        // Melakukan Aksi
        $distace = 12;
        $costPerKM = 3000;
        $order = [
            'price' => 10000,
            'qty' => 5
        ];
        $calculate = ($order['price'] * $order['qty']) + ($distace * $costPerKM);

        // Membandingkan Hasil 
        $this->assertEquals(86000, $calculate);
    }

    /** @test */
    public function generate_invoice_with_valid_transaction()
    {
        // Melakukan Aksi
        $transaction = [
            'id' => 123,
            'items' => [
                ['name' => 'Produk A', 'price' => 50000, 'quantity' => 2],
            ],
            'total' => 100000,
            'shipping' => 20000
        ];
        $invoice = "Nota untuk transaksi #{$transaction['id']} \n Total: Rp " . ($transaction['total'] + $transaction['shipping']);

        // Membandingkan Hasil 
        $this->assertStringContainsString("Total: Rp 120000", $invoice);
    }

    /** @test */
    public function get_daily_sales_report()
    {
        $transactions = [
            ['date' => '2025-03-17', 'total' => 100000],
            ['date' => '2025-03-17', 'total' => 50000],
            ['date' => '2025-03-16', 'total' => 75000],
        ];

        $dailyReport = collect($transactions)->where('date', '2025-03-17');

        $this->assertEquals(2, $dailyReport->count());
    }

    /** @test */
    public function get_weekly_sales_report()
    {
        $transactions = [
            ['date' => '2025-03-11', 'total' => 100000],
            ['date' => '2025-03-12', 'total' => 50000],
            ['date' => '2025-03-17', 'total' => 75000],
        ];

        $weeklyReport = collect($transactions)->filter(function ($item) {
            return $item['date'] >= '2025-03-11' && $item['date'] <= '2025-03-17';
        });

        $this->assertEquals(3, $weeklyReport->count());
    }
}
