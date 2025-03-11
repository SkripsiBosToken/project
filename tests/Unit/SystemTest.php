<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\System;
use Illuminate\Support\Str;

class SystemTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    private $system_data;

    protected function setUp(): void
    {
        parent::setUp();

        // Persiapan data
        $data = new System();

        $data->id = Str::uuid()->toString();
        $data->name = 'Kusuka Catering';
        $data->logo = 'test';
        $data->visi = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.';
        $data->misi = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scramblaed it to make a type specimen book.';
        $data->special_product = '[]';
        $data->our_customer = '[{"name" : "Pop Hotel", "logo" : "/assets/images/logo/pop.png", "href" : "https://www.instagram.com/kusukacatering/"}, {"name" : "Fave Hotel", "logo" : "/assets/images/logo/fave.png", "href" : "https://www.instagram.com/kusukacatering/"}, {"name" : "Neo Hotel", "logo" : "/assets/images/logo/neo.png", "href" : "https://www.instagram.com/kusukacatering/"}, {"name" : "Prime Hotel", "logo" : "/assets/images/logo/prime.png", "href" : "https://www.instagram.com/kusukacatering/"}]';
        $data->our_coverage = "[[-7.9215, 112.6001],
            [-7.9215, 112.6652],
            [-8.0002, 112.6652],
            [-8.0002, 112.6001],
            [-7.9215, 112.6001]]";
        $data->social_media = '[{"name" : "Instagram", "logo" : "test", "href" : "https://www.instagram.com/kusukacatering/"}]';
        $data->office_address = '[
    {"lat": -7.9666, "lng": 112.6326, "label": "Pusat Kota Malang"},
    {"lat": -7.9829, "lng": 112.6214, "label": "Universitas Brawijaya"}
]';
        $data->promo_event = '[{"name" : "Instagram", "banner" : "/assets/images/banner.png", "href" : "https://www.instagram.com/kusukacatering/"}]';
        $data->phone_number = "08123445678";

        $data->save();

        $this->system_data = System::first();
    }

    /** @test */
    public function area_coverage(): void
    {
        // Melakukan Aksi
        $action = $this->system_data['our_coverage'];
        $result = "[[-7.9215, 112.6001],
            [-7.9215, 112.6652],
            [-8.0002, 112.6652],
            [-8.0002, 112.6001],
            [-7.9215, 112.6001]]";

        // Membandingkan Hasil 
        $this->assertEquals($result, $action);
    }

    /** @test */
    public function our_services(): void
    {

        // Melakukan Aksi
        $action = json_decode($this->system_data['our_customer'], true);
        $result = [["name" => "Pop Hotel", "logo" => "/assets/images/logo/pop.png", "href" => "https://www.instagram.com/kusukacatering/"], ["name" => "Fave Hotel", "logo" => "/assets/images/logo/fave.png", "href" => "https://www.instagram.com/kusukacatering/"], ["name" => "Neo Hotel", "logo" => "/assets/images/logo/neo.png", "href" => "https://www.instagram.com/kusukacatering/"], ["name" => "Prime Hotel", "logo" => "/assets/images/logo/prime.png", "href" => "https://www.instagram.com/kusukacatering/"]];

        // Membandingkan Hasil 
        $this->assertEquals($result, $action);
    }
}
