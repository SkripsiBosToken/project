<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Product_Variant;
use Illuminate\Support\Str;

class ProductTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    protected function setUp(): void
    {
        parent::setUp();

        $product_id = Str::uuid()->toString();
        $category_id = Str::uuid()->toString();
        $product_variant_id = Str::uuid()->toString();

        // Persiapan data category
        $category = new Category();
        $category->id = $category_id;
        $category->name = 'Dummy Name';
        $category->description = 'Dummy Description';

        $category->save();

        // Persiapan data product
        $product = new Product();
        $product->id = $product_id;
        $product->name = 'Dummy Name';
        $product->category_id = $category_id;

        $product->save();

        // Persiapan data varian produk
        $product_variant = new Product_Variant();
        $product_variant->id = $product_variant_id;
        $product_variant->name_type = 'Dummy Name';
        $product_variant->photo = '["/assets/images/image-3.png", "/assets/images/image-3.png"]';
        $product_variant->description = 'Dummy Description';
        $product_variant->price = 10000;
        $product_variant->stock = 99;
        $product_variant->visibility = true;
        $product_variant->product_id = $product_id;

        $product_variant->save();
    }

    /** @test */
    public function get_catalogue_product(): void
    {
        // Melakukan Aksi
        $action = Product::with('product_variants', 'category')->first();
        $actionJson = json_encode([
            'id' => $action->id,
            'name' => 'Dummy Name',
            'category_id' => $action->category_id,
            'created_at' => $action->created_at,
            'updated_at' => $action->updated_at,
            'product_variants' => $action->product_variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name_type' => 'Dummy Name',
                    'photo' => ["assets/images/image-3.png", "assets/images/image-3.png"],
                    'description' => 'Dummy Description',
                    'price' => (int) $variant->price,
                    'stock' => (int) $variant->stock,
                    'visibility' => (bool) $variant->visibility,
                    'product_id' => $variant->product_id,
                    'created_at' => $variant->created_at,
                    'updated_at' => $variant->updated_at,
                ];
            }),
            'category' => [
                'id' => $action->category->id,
                'name' => 'Dummy Name',
                'description' => 'Dummy Description',
                'created_at' => $action->category->created_at,
                'updated_at' => $action->category->updated_at,
            ]
        ], JSON_PRETTY_PRINT);
        $result = json_encode(json_decode($actionJson, true), JSON_PRETTY_PRINT);

        // Membandingkan Hasil 
        $this->assertEquals($result, $actionJson);
    }

    /** @test */
    public function admin_can_create_product()
    {
        $admin = User::whereHas('role', function ($query) {
            $query->where('name', 'Admin');
        })->first();

        $this->assertNotNull($admin, 'Admin user tidak ditemukan. Pastikan user dengan role Admin tersedia.');

        $category = Category::first();
        $this->assertNotNull($category, 'Category tidak ditemukan.');

        $response = $this->actingAs($admin)->put(route('data.katalog.store'), [
            'name' => 'Nasi Kuning',
            'category_id' => $category->id,
            'description' => 'test',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Nasi Kuning']);
    }

    /** @test */
    public function can_view_top_selling_products()
    {
        $datas = Product_Variant::with('order_items')->get();
        foreach ($datas as $key => $data) {
            $qty = 0;
            foreach ($data['order_items'] as $key => $order) {
                $qty += $order['quantity'];
            }
            $datas[$key]['qty'] = $qty;
        }

        $this->assertEquals($datas[0]['qty'], 0);
    }
}
