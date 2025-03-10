<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class MidtransAction
{
   /**
    * @param \Illuminate\Http\Request
    * @return false|string $token
    */

   private $serverKey;
   private $clientkey;
   private $endpoint;
   private $merchantId;
   public $vaNumber;

   public function __construct()
   {
      $this->serverKey = env('MIDTRANS_SERVER_KEY');
      $this->clientkey = env('MIDTRANS_CLIENT_KEY');
      $this->merchantId = env('MIDTRANS_ID_MERCHANT');
      $this->endpoint = env('MIDTRANS_ENDPOINT');
      $this->vaNumber = env('MIDTRANS_VA_NUMBER');
   }

   public function getTransaction($transaction_id)
   {
      $url = $this->endpoint . 'v2/' . $transaction_id . '/status';
      $headers = [
         'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
      ];
      $fetch = Http::withHeaders($headers)->get($url);
      $response = $fetch->json();
      return $response;
   }

   public function chargeTransaction($request)
   {
      $url = $this->endpoint . 'v2/charge';
      $headers = [
         'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':')
      ];
      $fetch = Http::withHeaders($headers)->post($url, $request);
      $response = $fetch->json();
      return $response;
   }

   public function callback($request)
   {
      $hashed = hash('sha512', $request['order_id'] . $request['status_code'] . $request['gross_amount'] . $this->serverKey);
      $order_action = new OrderAction();
      if ($hashed === $request->signature_key) {
         $orderId = $order_action->getByTransactionId($request['transaction_id'])->id;
         switch ($request->transaction_status) {
            case 'capture':
               $order_action->updateStatus($orderId, "capture");
               $response = [
                  'success' => 1,
                  'message' => 'success request capture'
               ];
               return $response;
               break;
            case 'settlement':
               $order_action->updateStatus($orderId, "lunas");
               $response = [
                  'success' => 1,
                  'message' => 'success request settlement'
               ];
               return $response;
               break;
            case 'pending':
               $order_action->updateStatus($orderId, "pending");
               $response = [
                  'success' => 1,
                  'message' => 'success request pending'
               ];
               return $response;
               break;
            case 'deny':
               $order_action->updateStatus($orderId, "deny");
               $response = [
                  'success' => 1,
                  'message' => 'success request deny'
               ];
               return $response;
               break;
            case 'cancel':
               $order_action->updateStatus($orderId, "cancel");
               $response = [
                  'success' => 1,
                  'message' => 'success request cancel'
               ];
               break;
            case 'expire':
               $order_action->updateStatus($orderId, "expire");
               $response = [
                  'success' => 1,
                  'message' => 'success request expire'
               ];
               break;
            case 'failure':
               $order_action->updateStatus($orderId, "failure");
               $response = [
                  'success' => 1,
                  'message' => 'success request failure'
               ];
               break;
            case 'refund':
               $response = [
                  'success' => 1,
                  'message' => 'success request refund'
               ];
               break;
            default:
               $response = [
                  'success' => 1,
                  'message' => 'nothing change'
               ];
               break;
         }
      } else {
         $response = [
            'success' => 0,
            'message' => 'signature invalid'
         ];
      }

      return $response;
   }
}
