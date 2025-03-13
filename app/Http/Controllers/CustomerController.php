<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\AuthAction;
use App\Actions\CartAction;
use App\Actions\Cart_ItemAction;
use App\Actions\MidtransAction;
use App\Actions\Order_ItemAction;
use App\Actions\OrderAction;
use App\Actions\Product_VariantAction;
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

    public function checkout(Request $request, Product_VariantAction $product_variant_action, Cart_ItemAction $cart_item_action, CartAction $cart_action)
    {
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

        return view('customer.checkout', compact('datas', 'type'));
    }

    public function logout(AuthAction $auth_action)
    {
        $auth_action->logout();
        return redirect()->route('login');
    }

    public function checkout_order(Request $request, MidtransAction $midtrans_action, AuthAction $auth_action, Product_VariantAction $product_variant_action, OrderAction $order_action, Order_ItemAction $order_item_action, CartAction $cart_action, Cart_ItemAction $cart_item_action)
    {
        $user = $auth_action->getuser();
        $gross_amount = 0;
        $quantity = 0;
        $item_details = [];
        $items = json_decode($request['item_details']);
        foreach ($items as $item) {
            $product = $product_variant_action->getById($item->id);
            $data = [
                'id' => $item->id,
                'name' => $product['product']['name'] . ' - ' . $product['name_type'],
                'quantity' => (int)$item->quantity,
                'price' => $product['price']
            ];
            $gross_amount = $gross_amount + ($data['quantity'] * $data['price']);
            $quantity = $quantity + $data['quantity'];
            array_push($item_details, $data);
        }
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
                    'va_number' => $midtrans_action->vaNumber,
                    'recipient_name' => 'Kusuka Catering'
                ]
            ];
        }

        $response = $midtrans_action->chargeTransaction($checkout);

        $order = [
            'status' => $response['transaction_status'],
            'total_price' => $response['gross_amount'],
            'shipping_address' => $request['shipping_address'],
            'user_id' => $response['transaction_status'],
            'transaction_id' => $response['transaction_id'],
            'user_id' => $user['id']
        ];

        $order_id = $order_action->create($order);

        if ($request['type'] === 'buy-cart') {

            foreach ($item_details as $item) {
                $data = [
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'order_id' => $order_id,
                    'cart_id' => $cart_action->getByUser($auth_action->getuser()['id'])[0]['id'],
                    'product_variant_id' => $item['id']
                ];
                $order_item_action->create($data);
            }

            $cart_item_action->deleteByCartId($cart_action->getByUser($user['id'])[0]['id']);
        }

        if ($request['type'] === 'buy-directly') {
            foreach ($item_details as $item) {
                $data = [
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'order_id' => $order_id,
                    'cart_id' => null,
                    'product_variant_id' => $item['id']
                ];
                $order_item_action->create($data);
            }
        }

        return redirect()->route('payment', ['id' => $order_id]);
    }

    public function payment($id, OrderAction $order_action, MidtransAction $midtrans_action)
    {
        $order = $order_action->getById($id);
        $data = $midtrans_action->getTransaction($order['transaction_id']);
        if (!($data['expiry_time'])) {
            return redirect()->route('payment', ['id' => $id]);
        }

        return view('customer.payment', compact('data'));
    }

    public function callback(Request $request, MidtransAction $midtrans_action)
    {
        return $midtrans_action->callback($request);
    }
}
