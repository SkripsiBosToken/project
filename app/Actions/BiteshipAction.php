<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class BiteshipAction
{
   /**
    * @param \Illuminate\Http\Request
    * @return false|string $token
    */

   private $apiKey;
   public $baseUrl;

   public function __construct()
   {
      $this->apiKey = env('BITESHIP_API_KEY');
      $this->baseUrl = env('BITESHIP_BASE_URL');
   }

   public function getLocation($location_id)
   {
      $url = $this->baseUrl . 'v1/locations' . $location_id;
      $headers = [
         'Authorization' => $this->apiKey
      ];
      $fetch = Http::withHeaders($headers)->get($url);
      $response = $fetch->json();
      return $response;
   }

   public function createLocation($request)
   {
      $url = $this->baseUrl . 'v1/locations';
      $headers = [
         'Authorization' => $this->apiKey
      ];
      $fetch = Http::withHeaders($headers)->post($url, $request);
      $response = $fetch->json();
      return $response;
   }

}
