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
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function cart(CartAction $cart_action)
    {
        $cart = $cart_action->getByUser(Auth::user()->id)[0];
        return view('customer.cart', compact('cart'));
    }

    public function addToCart(Request $request, CartAction $cart_action, Cart_ItemAction $cart_item_action, AuthAction $auth_action)
    {
        $cart_id = $cart_action->getByUser($auth_action->getuser()['id'])[0]['id'];
        $matchData = $cart_item_action->matchCart($cart_id, $request['product_variant_id']);
        if ($matchData) {
            $cart_item_action->updateStock($matchData['id'], $matchData['qty'] + (int)$request['qty']);
        } else {
            $request['qty'] = (int)$request['qty'];
            $request['cart_id'] = $cart_id;
            $cart_item_action->create($request);
        }
        return redirect()->route('cart');
    }

    public function deleteCart(Cart_ItemAction $cart_item_action, $id)
    {
        $cart_item_action->delete($id);
        return redirect()->route('cart');
    }

    public function checkout(Request $request, Product_VariantAction $product_variant_action, Cart_ItemAction $cart_item_action, CartAction $cart_action, AuthAction $auth_action, SystemAction $system_action)
    {
        $user_address = json_decode($auth_action->getuser()['address'], true);
        $items = [];
        $datas = [];
        $type = $request['type'];
        if ($request['type'] === 'buy-directly') {
            array_push(
                $datas,
                [
                    'product' => $product_variant_action->getById($request['product_variant_id']),
                    'qty' => (int)$request['qty'],
                ]
            );
        }

        if ($request['type'] === 'buy-cart') {
            $items = json_decode($request['items']);
            foreach ($items as $item) {
                $data = [
                    'product' => $product_variant_action->getById($item->product_variant_id),
                    'qty' => (int)$item->qty
                ];
                array_push($datas, $data);
            }
        }

        $shippingCost = $this->calculateShippingCost((float)$user_address['latitude'], (float)$user_address['longitude']);

        return view('customer.checkout', compact('datas', 'type', 'shippingCost'));
    }

    public function logout(AuthAction $auth_action)
    {
        $auth_action->logout();
        return redirect()->route('login');
    }

    public function checkout_order(Request $request, MidtransAction $midtrans_action, AuthAction $auth_action, Product_VariantAction $product_variant_action, OrderAction $order_action, Order_ItemAction $order_item_action, CartAction $cart_action, Cart_ItemAction $cart_item_action, TransactionAction $transaction_action)
    {

        $shippingCost = [
            'id' => 'shipping-cost',
            'name' => 'Ongkir / Shipping Cost',
            'quantity' => 1,
            'price' => $this->calculateShippingCost((float)$request['latitude'], (float)$request['longitude'])
        ];

        $shippingCost_v2 = [
            'item_id' => 'shipping-cost',
            'description' => 'Ongkir / Shipping Cost',
            'quantity' => 1,
            'price' => $this->calculateShippingCost((float)$request['latitude'], (float)$request['longitude'])
        ];

        $user = $auth_action->getuser();
        $gross_amount = $shippingCost['price'] * $shippingCost['quantity'];
        $quantity = 0;
        $item_details = [];
        $items_details_v2 = [];
        $items = json_decode($request['item_details']);


        foreach ($items as $item) {
            $product = $product_variant_action->getById($item->id);
            $data = [
                'id' => $item->id,
                'name' => $product['product']['name'] . ' - ' . $product['name_type'],
                'quantity' => (int)$item->quantity,
                'price' => $product['price']
            ];

            $data2 = [
                'item_id' => Str::random(30),
                'description' => $product['product']['name'] . ' - ' . $product['name_type'],
                'quantity' => (int)$item->quantity,
                'price' => $product['price']
            ];
            $gross_amount = $gross_amount + ($data['quantity'] * $data['price']);
            $quantity = $quantity + $data['quantity'];

            array_push($item_details, $data);
            array_push($items_details_v2, $data2);
        }
        array_push($item_details, $shippingCost);
        array_push($items_details_v2, $shippingCost_v2);

        $transaction_details = [
            'order_id' => 'KC_' . $user['id'] . '_' . time(),
            'gross_amount' => $gross_amount
        ];

        $customer_details = [
            'first_name' => '',
            'last_name' => '',
            'email' => $user['email'],
            'phone' => $user['phone_number']
        ];

        $customer_details_v2 = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => ltrim($user['phone_number'], '0')
        ];

        $fullName = $user['name'];
        $nameParts = explode(' ', trim($fullName));

        if (!empty($nameParts)) {
            $customer_details['first_name'] = array_shift($nameParts);
            $customer_details['last_name'] = implode(' ', $nameParts);
        }

        $paymentType = $request['payment_type'];

        $checkout = [
            'payment_type' => ($paymentType !== 'qris') ? 'bank_transfer' : $paymentType,
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
        ];

        if ($paymentType !== 'qris') {
            $checkout['bank_transfer'] = [
                'bank' => $paymentType,
                $paymentType . '_va' => [
                    // 'va_number' => $midtrans_action->vaNumber,
                    'recipient_name' => 'Kusuka Catering'
                ]
            ];
        }

        $response = $midtrans_action->chargeTransaction($checkout);

        $timezone = new \DateTimeZone('Asia/Jakarta');
        $due_date = (new \DateTime('now', $timezone))->modify('+1 hour')->format("Y-m-d H:i:s P");
        $invoice_date = (new \DateTime('now', $timezone))->format("Y-m-d H:i:s P");
        $due_date = preg_replace('/\+([0-9]{2}):([0-9]{2})$/', '+$1$2', $due_date);
        $invoice_date = preg_replace('/\+([0-9]{2}):([0-9]{2})$/', '+$1$2', $invoice_date);

        $invoice = [
            'order_id' => Str::random(30),
            'invoice_number' => Str::uuid()->toString(),
            'due_date' => $due_date,
            'invoice_date' => $invoice_date,
            'customer_details' => $customer_details_v2,
            'payment_type' => 'virtual_account',
            'reference' => 'reference',
            'item_details' => $items_details_v2,
            'virtual_accounts' => [
                ['bank' => ($paymentType !== 'qris') ? $paymentType . '_va' : 'bca_va']
            ]
        ];
        // $invoice_request = $midtrans_action->createInvoice($invoice);

        $order = [
            'status' => 'Belum Dibayar',
            'total_price' => $response['gross_amount'],
            'shipping_address' => json_encode([
                'address' => $request['shipping_address'],
                'latitude' => (float)$request['latitude'],
                'longitude' => (float)$request['longitude'],
            ]),
            'transaction_id' => $response['transaction_id'],
            'user_id' => $user['id']
        ];

        $order_id = $order_action->create($order);

        $transaction = [
            'status' => $response['transaction_status'],
            'transaction_id' => $response['transaction_id'],
            // 'invoice_id' => $invoice_request['id'],
            'invoice_id' => "tset",
            'order_id' => $order_id
        ];

        $transaction_action->create($transaction);

        if (in_array($request['type'], ['buy-cart', 'buy-directly'])) {
            $cartId = ($request['type'] === 'buy-cart')
                ? $cart_action->getByUser($auth_action->getuser()['id'])[0]['id']
                : null;

            foreach ($item_details as $item) {
                if ($item['id'] !== 'shipping-cost') {
                    $order_item_action->create([
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                        'order_id' => $order_id,
                        'cart_id' => $cartId,
                        'product_variant_id' => $item['id']
                    ]);
                    $getToUpdateStock = $product_variant_action->getById($item['id']);
                    $product_variant_action->updateStock($item['id'], ($getToUpdateStock['stock'] - $item['quantity']));
                }
            }

            if ($request['type'] === 'buy-cart') {
                $cart_item_action->deleteByCartId($cartId);
            }
        }

        return redirect()->route('payment', ['id' => $order_id]);
    }

    public function payment($id, OrderAction $order_action, MidtransAction $midtrans_action)
    {
        $url = '';
        $order = $order_action->getById($id);
        $data = $midtrans_action->getTransaction($order['transaction_id']);
        if (!($data['expiry_time'])) {
            return redirect()->route('payment', ['id' => $id]);
        }
        if ($data['payment_type'] === 'qris') {
            $url = $midtrans_action->endpoint . 'v2/qris/' . $data['transaction_id'] . '/qr-code';
        }

        return view('customer.payment', compact('data', 'url'));
    }

    public function callback(Request $request, MidtransAction $midtrans_action)
    {
        return $midtrans_action->callback($request);
    }

    public function calculateShippingCost($latitude, $longitude)
    {
        $earthRadius = 6371;
        $ratePekKm = 3000;
        $system_action = new SystemAction();

        $officeAddress = json_decode($system_action->get()['office_address'], true);

        $lat1 = deg2rad($latitude);
        $lng1 = deg2rad($longitude);
        $lat2 = deg2rad((float)$officeAddress['latitude']);
        $lng2 = deg2rad((float)$officeAddress['longitude']);

        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $c * $earthRadius;
        $shippingCost = ceil(($distance * $ratePekKm) / 100) * 100;

        return $shippingCost;
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
        $invoiceData = $transaction_action->getByOrderId($id);
        $transaction = $midtrans_action->getTransaction($invoiceData['transaction_id']);
        $order = $order_action->getById($id);
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
        $user = $auth_action->getuser();

        $data = [
            'name' => $request['name'],
            'address' => json_encode([
                "address" => $request['address'],
                "latitude" => $request['latitude'],
                "longitude" => $request['longitude'],
                "postal_code" => $request['postal_code'],
            ]),
            'phone_number' => $request['phone'],
            'birth_date' => $request['birth_date']
        ];

        $user_action->update($user['id'], $data);
        return redirect()->route('profile');
    }

    public function cancelPayment($id, AuthAction $auth_action, OrderAction $order_action, Order_ItemAction $order_item_action, MidtransAction $midtrans_action, Product_VariantAction $product_variant_action, TransactionAction $transaction_action)
    {
        $orders = $auth_action->getuser()['orders'];

        foreach ($orders as $value) {
            if ($id == $value['id'] && $value['status'] == 'Belum Dibayar') {
                $midtrans_action->cancelTransaction($value['transaction_id']);

                foreach ($value['order_items'] as $variant) {
                    $product_variant_action->updateStock($variant['product_variant_id'], ($variant['quantity'] + $variant['product_variant']['stock']));
                    $order_item_action->delete($variant['id']);
                }
                $transaction_id = $order_action->getById($value['id'])['transaction']['id'];
                $transaction_action->delete($transaction_id);
                $order_action->delete($value['id']);
            }
        }
        return redirect()->route('order-list');
    }

    public function submitReview(Request $request, RateAction $rate_action, AuthAction $auth_action){
        $user_id = $auth_action->getuser()['id'];
        $data = [
            'user_id' => $user_id,
            'rate' => $request['rate'],
            'message' => $request['message'],
            'order_id' => $request['order_id']
        ];
        $rate_action->create($data);
        return redirect()->route('order-list');
    }

}
