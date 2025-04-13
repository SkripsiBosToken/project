<?php

namespace App\Actions;

use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

    public function get()
    {
        $data = System::first();
        return $data;
    }

    public function update($request)
    {
        $data = System::first();

        $data->name = $request['name'] ?? $data->name;
        $data->logo = $request['logo'] ?? $data->logo;
        $data->visi = $request['visi'] ?? $data->visi;
        $data->misi = $request['misi'] ?? $data->misi;
        $data->special_product = $request['special_product'] ?? $data->special_product;
        $data->our_customer = $request['our_customer'] ?? $data->our_customer;
        $data->our_coverage = $request['our_coverage'] ?? $data->our_coverage;
        $data->social_media = $request['social_media'] ?? $data->social_media;
        $data->office_address = $request['office_address'] ?? $data->office_address;
        $data->promo_event = $request['promo_event'] ?? $data->promo_event;
        $data->phone_number = $request['phone_number'] ?? $data->phone_number;

        $data->save();
    }
}
