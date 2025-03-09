<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $category_id_1 = Str::uuid()->toString();
        $category_id_2 = Str::uuid()->toString();
        $category_id_3 = Str::uuid()->toString();
        $category_id_4 = Str::uuid()->toString();

        $product_id_1 = Str::uuid()->toString();
        $product_id_2 = Str::uuid()->toString();

        $product_variant_id_1 = Str::uuid()->toString();
        $product_variant_id_2 = Str::uuid()->toString();
        $product_variant_id_3 = Str::uuid()->toString();
        // $product_variant_id_4 = Str::uuid()->toString();

        $role_id_1 = Str::uuid()->toString();
        $role_id_2 = Str::uuid()->toString();

        $user_id_1 = Str::uuid()->toString();
        $user_id_2 = Str::uuid()->toString();

        $cart_id_1 = Str::uuid()->toString();

        DB::table('categories')->insert([
            'id' => $category_id_1,
            'name' => 'Snack Box',
            'description' => '-',
        ]);

        DB::table('categories')->insert([
            'id' => $category_id_2,
            'name' => 'Catering',
            'description' => '-',
        ]);

        DB::table('categories')->insert([
            'id' => $category_id_3,
            'name' => 'Kue Basah',
            'description' => '-',
        ]);

        DB::table('categories')->insert([
            'id' => $category_id_4,
            'name' => 'Takjil',
            'description' => '-',
        ]);

        DB::table('products')->insert([
            'id' => $product_id_1,
            'name' => 'Tumpeng ',
            'category_id' => $category_id_1

        ]);

        DB::table('products')->insert([
            'id' => $product_id_2,
            'name' => 'Katering Pernikahan',
            'category_id' => $category_id_2
        ]);

        DB::table('product_variants')->insert([
            'id' => $product_variant_id_1,
            'name_type' => 'Paket A',
            'photo' => '["assets/images/image-2.png", "assets/images/image-3.png"]',
            'description' => 'Terdapat beberapa menu, yaitu : Nasi Padang, Es Buah, Sate Padang, Nasi Goreng.',
            'price' => 123000,
            'stock' => 99,
            'visibility' => TRUE,
            'product_id' => $product_id_2
        ]);

        DB::table('product_variants')->insert([
            'id' => $product_variant_id_2,
            'name_type' => 'Paket B',
            'photo' => '["assets/images/image-3.png", "assets/images/image-2.png"]',
            'description' => 'Terdapat beberapa menu, yaitu : Nasi Padang, Es Buah, Sate Padang, Nasi Goreng.',
            'price' => 124000,
            'stock' => 99,
            'visibility' => TRUE,
            'product_id' => $product_id_2
        ]);

        DB::table('product_variants')->insert([
            'id' => $product_variant_id_3,
            'name_type' => 'Small',
            'photo' => '["assets/images/image-3.png", "assets/images/image-3.png"]',
            'description' => 'Tumpeng nasi kinung dengan barbagai lauk pauk.',
            'price' => 100000,
            'stock' => 99,
            'visibility' => TRUE,
            'product_id' => $product_id_1
        ]);

        DB::table('system')->insert([
            'id' => Str::uuid()->toString(),
            'name' => 'Kusuka Catering',
            'logo' => 'test',
            'visi' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
            'misi' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
            'special_product' => '["' . $product_id_1 . '", "' . $product_id_2 . '"]',
            'our_customer' => '[{"name" : "Pop Hotel", "logo" : "/assets/images/logo/pop.png", "href" : "https://www.instagram.com/kusukacatering/"}, {"name" : "Fave Hotel", "logo" : "/assets/images/logo/fave.png", "href" : "https://www.instagram.com/kusukacatering/"}, {"name" : "Neo Hotel", "logo" : "/assets/images/logo/neo.png", "href" : "https://www.instagram.com/kusukacatering/"}, {"name" : "Prime Hotel", "logo" : "/assets/images/logo/prime.png", "href" : "https://www.instagram.com/kusukacatering/"}]',
            'our_coverage' => "[[-7.9215, 112.6001],
            [-7.9215, 112.6652],
            [-8.0002, 112.6652],
            [-8.0002, 112.6001],
            [-7.9215, 112.6001]]",
            'social_media' => '[{"name" : "Instagram", "logo" : "test", "href" : "https://www.instagram.com/kusukacatering/"}]',
            'office_address' => '[
    {"lat": -7.9666, "lng": 112.6326, "label": "Pusat Kota Malang"},
    {"lat": -7.9829, "lng": 112.6214, "label": "Universitas Brawijaya"}
]',
            'promo_event' => '[{"name" : "Instagram", "banner" : "/assets/images/banner.png", "href" : "https://www.instagram.com/kusukacatering/"}]',
            'phone_number' => "085892180308",
        ]);

        DB::table('roles')->insert([
            'id' => $role_id_1,
            'name' => 'Admin',
            'description' => 'otoritas tertinggi',
        ]);

        DB::table('roles')->insert([
            'id' => $role_id_2,
            'name' => 'Customer',
            'description' => 'pelanggan',
        ]);

        DB::table('users')->insert([
            'id' => $user_id_1,
            'username' => 'admin',
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'address' => 'test',
            'phone_number' => '081232857502',
            'point' => 0,
            'role_id' => $role_id_1,
        ]);

        DB::table('users')->insert([
            'id' => $user_id_2,
            'username' => 'user',
            'name' => 'Faiz Diandra Maulana',
            'email' => 'user@user.com',
            'password' => 'user',
            'address' => 'test',
            'phone_number' => '081232857502',
            'point' => 0,
            'role_id' => $role_id_2,
        ]);

        DB::table('rates')->insert([
            'id' => Str::uuid()->toString(),
            'rate' => 5,
            'message' => 'Makanannya enak',
            'user_id' => $user_id_2,
        ]);

        DB::table('rates')->insert([
            'id' => Str::uuid()->toString(),
            'rate' => 5,
            'message' => 'Layanan Memuaskan',
            'user_id' => $user_id_2,
        ]);

        DB::table('rates')->insert([
            'id' => Str::uuid()->toString(),
            'rate' => 4,
            'message' => 'Pengiriman Cepat',
            'user_id' => $user_id_2,
        ]);

        DB::table('carts')->insert([
            'id' => $cart_id_1,
            'user_id' => $user_id_2,
        ]);

        DB::table('cart_items')->insert([
            'id' => Str::uuid()->toString(),
            'cart_id' => $cart_id_1,
            'product_variant_id' => $product_variant_id_1,
            'qty' => 30,
        ]);

        DB::table('cart_items')->insert([
            'id' => Str::uuid()->toString(),
            'cart_id' => $cart_id_1,
            'product_variant_id' => $product_variant_id_2,
            'qty' => 10,
        ]);
    }
}
