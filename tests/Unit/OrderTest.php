<?php

namespace Tests\Unit;

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
        $this->clientkey = env('MIDTRANS_CLIENT_KEY');
        $this->merchantId = env('MIDTRANS_ID_MERCHANT');
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
        
        $this->assertEquals('201', $result['status_code']);
    }
}
