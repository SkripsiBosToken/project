<?php

namespace App\Http\Controllers;

use App\Actions\CategoryAction;
use App\Actions\ProductAction;
use App\Actions\RateAction;
use App\Actions\SystemAction;
use App\Actions\AuthAction;
use App\Actions\BiteshipAction;
use App\Actions\CartAction;
use App\Actions\MailAction;
use App\Actions\PasswordResetAction;
use App\Actions\RoleAction;
use App\Actions\UserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{

    private $appUrl;

    public function __construct()
    {
        $this->appUrl = env('APP_URL');
    }

    public function setting()
    {
        $system_action = new SystemAction();
        return $system_action->get();
    }

    public function getCategories()
    {
        $category_action = new CategoryAction();
        return $category_action->get();
    }

    // View
    public function catalogue(ProductAction $product_action, CategoryAction $category_action)
    {
        $products = $product_action->get();
        $categories = $category_action->get();
        return view('customer.catalogue', compact('products', 'categories'));
    }

    // View
    public function catalogue_detail(SystemAction $system_action, ProductAction $product_action, $id)
    {
        $products = [];
        $product = $product_action->getById($id);
        $setting = $system_action->get();
        $data = json_decode($setting['special_product'], true);
        foreach ($data as $id) {
            $data = $product_action->getById($id);
            array_push($products, $data);
        }
        return view('customer.catalogue-detail', compact('product', 'products'));
    }

    // View 
    public function landing(SystemAction $system_action, ProductAction $product_action)
    {
        $products = [];
        $setting = $system_action->get();
        $data = json_decode($setting['special_product'], true);
        foreach ($data as $id) {
            $data = $product_action->getById($id);
            array_push($products, $data);
        }

        $our_customers = json_decode($setting['our_customer'], true);
        return view('customer.welcome', compact('setting', 'products', 'our_customers'));
    }

    //view 
    public function about(SystemAction $system_action, RateAction $rate_action)
    {
        $setting = $system_action->get();
        $rates = $rate_action->getWithNum(3);
        return view('customer.about', compact('setting', 'rates'));
    }

    //view
    public function contact(SystemAction $system_action)
    {
        $setting = $system_action->get();
        $social_medias = json_decode($setting['social_media'], true);
        return view('customer.contact', compact('setting', 'social_medias'));
    }

    //view
    public function login()
    {
        return view('customer.login');
    }

    public function auth(Request $request, AuthAction $auth_action)
    {
        $auth_action->loginUser($request);
        $data = $auth_action->getuser();

        if ($data) {
            if ($data['role']['name'] == 'Admin') {
                return redirect()->route('dashboard');
            }

            if ($data['role']['name'] == 'Customer') {
                return redirect()->route('home');
            }
        }

        return redirect()->route('login')->with('error', 'Username atau password salah.');
    }

    public function sign_up()
    {
        return view('customer.register');
    }

    public function register(Request $request, RoleAction $role_action, UserAction $user_action, BiteshipAction $biteship_action, CartAction $cart_action)
    {
        if (!$user_action->getByUsername($request['username']) && !$user_action->getByEmail($request['email'])) {
            $role = $role_action->getByName('Customer');
            // $request_data = [
            //     "name" => "Destinasi",
            //     "contact_name" => $request['name'],
            //     "contact_phone" => $request['phone'],
            //     "address" => $request['address'],
            //     "postal_code" => (int) $request['postal_code'],
            //     "latitude" => (float) $request['latitude'],
            //     "longitude" => (float) $request['longitude'],
            //     "type" => "destination"
            // ];
            // $response = $biteship_action->createLocation($request_data);
            $data = [
                "username" => $request['username'],
                "name" => $request['name'],
                "email" => $request['email'],
                "password" => $request['password'],
                "address" => json_encode([
                    "address" => $request['address'],
                    "latitude" => $request['latitude'],
                    "longitude" => $request['longitude'],
                    "postal_code" => $request['postal_code'],
                    // "location_id" => $response['id']
                ]),
                "phone_number" => $request['phone'],
                "point" => 0,
                "birth_date" => $request['birth_date'],
                "role_id" => $role['id']
            ];

            $cart_action->create([
                'user_id' => $user_action->create($data)
            ]);
            return redirect()->route('login');
        }
        return redirect()->route('register')->with('error', 'Username atau email sudah terdaftar.');
    }

    public function forgotPassword()
    {
        return view('customer.forgot-password');
    }

    public function sendResetPassword(Request $request, UserAction $user_action, PasswordResetAction $password_reset_action, MailAction $mail_action)
    {
        $user = $user_action->getByEmail($request['email']);
        if ($user) {
            $findExpired = $password_reset_action->getByEmail($request['email']);
            if (!$findExpired) {
                $request = [
                    'email' => $request['email'],
                    'token' => $user->id . Str::random(64),
                ];
                $password_reset_action->create($request);

                // Send Mail
                $content = [$this->appUrl . '/reset-password/' . $request['token']];
                $mail_action->send($request['email'], $content, 'mail.reset-password', 'Reset Password');
                return back()->with('error', 'Kami telah mengirimkan tautan pengaturan ulang kata sandi Anda melalui email!');
            }
            if ($findExpired['expired'] > time()) {
                return back()->with('error', 'Kami telah mengirimkan tautan pengaturan ulang kata sandi Anda melalui email, pengiriman email akan dilakukan dalam 30 menit ke depan.');
            } else {
                return back()->with('error', 'Terjadi kesalahan saat mengirimkan tautan pengaturan ulang kata sandi.');
            }
        } else {
            return redirect()->back()->with('error', 'Pengguna dengan email ini tidak ditemukan.');
        }
    }

    public function resetPassword($token, PasswordResetAction $password_reset_action){
        $data = $password_reset_action->getByToken($token);
        if (!$data) {
            return back();
        }
        if ($data['expired']  < time()) {
            $password_reset_action->deleteByEmail($data['email']);
        }
        return view('customer.reset-password', compact('data'));
    }

    public function requestResetPassword(Request $request, $token, PasswordResetAction $password_reset_action, UserAction $user_action){
        $data = $password_reset_action->getByToken($token);
        $user_action->updatePassword($data['email'], $request['password']);
        $password_reset_action->deleteByEmail($data['email']);
        return redirect()->route('login');
        
    }
}
