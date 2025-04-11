<?php

namespace App\Http\Controllers;

use App\Actions\CategoryAction;
use App\Actions\MidtransAction;
use App\Actions\Order_ItemAction;
use App\Actions\OrderAction;
use App\Actions\Product_VariantAction;
use App\Actions\ProductAction;
use App\Actions\SystemAction;
use App\Actions\UserAction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard(OrderAction $order_action, UserAction $user_action, ProductAction $product_action)
    {
        $product = $product_action->get()->count();
        $user = $user_action->get()->count();
        $orders = $order_action->get();

        $progressOrders = $orders->whereIn('status', ['Menunggu Konfirmasi', 'Diproses', 'Dikirim'])->count();
        $successfulOrders = $orders->whereIn('status', ['Menunggu Konfirmasi', 'Diproses', 'Dikirim', 'Berhasil']);
        $successOrders = $successfulOrders->count();
        $daily = $successfulOrders->filter(function ($order) {
            return $order->created_at >= now()->startOfDay() && $order->created_at <= now()->endOfDay();
        })->count();

        $weekly = $successfulOrders->filter(function ($order) {
            return $order->created_at >= now()->startOfWeek() && $order->created_at <= now()->endOfWeek();
        })->count();

        $monthly = $successfulOrders->filter(function ($order) {
            return $order->created_at >= now()->startOfMonth() && $order->created_at <= now()->endOfMonth();
        })->count();

        $yearly = $successfulOrders->filter(function ($order) {
            return $order->created_at >= now()->startOfYear() && $order->created_at <= now()->endOfYear();
        })->count();

        return view('admin.dashboard', compact('daily', 'weekly', 'monthly', 'yearly', 'user', 'progressOrders', 'successOrders', 'product'));
    }

    public function setting(SystemAction $system_action)
    {
        $system = $system_action->get();
        return view('admin.setting.setting', compact('system'));;
    }

    public function specialProduct(SystemAction $system_action, ProductAction $product_action)
    {
        $specialProduct = [];
        $products = $product_action->get();
        $datas = json_decode($system_action->get()['special_product'], true);
        foreach ($datas as $key => $item) {
            $product = $product_action->getById($item);
            $data = [
                'product_id' => $item,
                'product' => $product
            ];
            array_push($specialProduct, $data);
        }
        return view('admin.setting.special-product', compact('specialProduct', 'products'));;
    }

    public function ourCustomer(SystemAction $system_action)
    {
        $customers = json_decode($system_action->get()['our_customer'], true);
        return view('admin.setting.customer', compact('customers'));;
    }

    public function socialMedia(SystemAction $system_action)
    {
        $medias = json_decode($system_action->get()['social_media'], true);
        return view('admin.setting.social-media', compact('medias'));;
    }

    public function event(SystemAction $system_action)
    {
        $events = json_decode($system_action->get()['promo_event'], true);
        return view('admin.setting.event', compact('events'));;
    }

    public function updateSetting(Request $request, SystemAction $system_action)
    {
        $oldData = $system_action->get();

        $office_address = [
            'address' => $request->input('address'),
            'postal_code' => $request->input('postal_code'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ];

        $data = [
            'name' => $request->input('name'),
            'phone_number' => $request->input('phone_number'),
            'visi' => $request->input('visi'),
            'misi' => $request->input('misi'),
            'office_address' => json_encode($office_address)
        ];

        if ($request->hasFile('logo')) {
            if ($oldData && $oldData->logo) {
                $oldLogoPath = str_replace('/storage/', '', $oldData->logo);
                if (\Storage::disk('public')->exists($oldLogoPath)) {
                    \Storage::disk('public')->delete($oldLogoPath);
                }
            }

            $path = $request->file('logo')->store('uploads/logo', 'public');
            $data['logo'] = '/storage/' . $path;
        }

        $system_action->update($data);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function updateSpecialSetting(Request $request, SystemAction $system_action)
    {
        $specialProduct = array($request['product_01'], $request['product_02'], $request['product_03'], $request['product_04']);
        $system_action->update(['special_product' => json_encode($specialProduct)]);
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function updateOurCustomer(Request $request, SystemAction $system_action)
    {
        $system = $system_action->get();
        $customers = json_decode($system->our_customer, true) ?? [];
        $deletedIndexes = json_decode($request->input('deleted_customer_indexes'), true) ?? [];

        foreach ($deletedIndexes as $index) {
            if (isset($customers[$index]['logo'])) {
                $logoPath = public_path(str_replace('/storage/', 'storage/', $customers[$index]['logo']));
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }
            unset($customers[$index]);
        }
        if ($request->has('customers')) {
            foreach ($request->customers as $index => $customerData) {
                $customers[$index]['name'] = $customerData['name'] ?? $customers[$index]['name'];
                $customers[$index]['href'] = $customerData['href'] ?? '';

                if (isset($customerData['logo']) && $request->file("customers.$index.logo")) {
                    // Hapus logo lama
                    if (isset($customers[$index]['logo'])) {
                        $oldLogo = public_path(str_replace('/storage/', 'storage/', $customers[$index]['logo']));
                        if (file_exists($oldLogo)) unlink($oldLogo);
                    }

                    $path = $request->file("customers.$index.logo")->store('uploads/customers', 'public');
                    $customers[$index]['logo'] = '/storage/' . $path;
                }
            }
        }
        if ($request->has('new_customers')) {
            foreach ($request->new_customers as $new) {
                $path = null;
                if (isset($new['logo']) && $new['logo'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = '/storage/' . $new['logo']->store('uploads/customers', 'public');
                }

                $customers[] = [
                    'name' => $new['name'] ?? '',
                    'href' => $new['href'] ?? '',
                    'logo' => $path,
                ];
            }
        }
        $system->our_customer = json_encode(array_values($customers)); // Reindex
        $system->save();

        return redirect()->back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function updateSocialMedia(Request $request, SystemAction $system_action)
    {
        $system = $system_action->get();
        $customers = json_decode($system->social_media, true) ?? [];
        $deletedIndexes = json_decode($request->input('deleted_customer_indexes'), true) ?? [];

        foreach ($deletedIndexes as $index) {
            if (isset($customers[$index]['logo'])) {
                $logoPath = public_path(str_replace('/storage/', 'storage/', $customers[$index]['logo']));
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }
            unset($customers[$index]);
        }
        if ($request->has('customers')) {
            foreach ($request->customers as $index => $customerData) {
                $customers[$index]['name'] = $customerData['name'] ?? $customers[$index]['name'];
                $customers[$index]['href'] = $customerData['href'] ?? '';

                if (isset($customerData['logo']) && $request->file("customers.$index.logo")) {
                    if (isset($customers[$index]['logo'])) {
                        $oldLogo = public_path(str_replace('/storage/', 'storage/', $customers[$index]['logo']));
                        if (file_exists($oldLogo)) unlink($oldLogo);
                    }

                    $path = $request->file("customers.$index.logo")->store('uploads/customers', 'public');
                    $customers[$index]['logo'] = '/storage/' . $path;
                }
            }
        }
        if ($request->has('new_customers')) {
            foreach ($request->new_customers as $new) {
                $path = null;
                if (isset($new['logo']) && $new['logo'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = '/storage/' . $new['logo']->store('uploads/customers', 'public');
                }

                $customers[] = [
                    'name' => $new['name'] ?? '',
                    'href' => $new['href'] ?? '',
                    'logo' => $path,
                ];
            }
        }
        $system->social_media = json_encode(array_values($customers)); // Reindex
        $system->save();

        return redirect()->back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function updateEvent(Request $request, SystemAction $system_action)
    {
        $system = $system_action->get();
        $events = json_decode($system->promo_event, true) ?? [];
        $deletedIndexes = json_decode($request->input('deleted_customer_indexes'), true) ?? [];
        foreach ($deletedIndexes as $index) {
            if (isset($events[$index]['banner'])) {
                $oldPath = public_path(str_replace('/storage/', 'storage/', $events[$index]['banner']));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            unset($events[$index]);
        }

        if ($request->has('customers')) {
            foreach ($request->customers as $index => $item) {
                $events[$index]['name'] = $item['name'] ?? $events[$index]['name'];
                $events[$index]['href'] = $item['href'] ?? $events[$index]['href'];

                if ($request->hasFile("customers.$index.banner")) {
                    if (isset($events[$index]['banner'])) {
                        $oldPath = public_path(str_replace('/storage/', 'storage/', $events[$index]['banner']));
                        if (file_exists($oldPath)) unlink($oldPath);
                    }

                    $newPath = $request->file("customers.$index.banner")->store('uploads/events', 'public');
                    $events[$index]['banner'] = '/storage/' . $newPath;
                }
            }
        }

        if ($request->has('new_customers')) {
            foreach ($request->new_customers as $new) {
                $path = null;
                if (isset($new['banner']) && $new['banner'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = '/storage/' . $new['banner']->store('uploads/events', 'public');
                }

                $events[] = [
                    'name' => $new['name'] ?? '',
                    'href' => $new['href'] ?? '',
                    'banner' => $path,
                ];
            }
        }

        $system->promo_event = json_encode(array_values($events));
        $system->save();

        return redirect()->back()->with('success', 'Data sosial media berhasil diperbarui.');
    }


    public function users(UserAction $user_action)
    {
        $datas = $user_action->get();
        return view('admin.user.user', compact('datas'));
    }

    public function users_detail($id, UserAction $user_action)
    {
        $data = $user_action->getById($id);
        $orders = $data['orders'];
        return view('admin.user.detail', compact('data', 'orders'));
    }

    public function orders(OrderAction $order_action)
    {
        $datas = $order_action->get();
        return view('admin.order.orders', compact('datas'));
    }

    public function order_detail($id, OrderAction $order_action, UserAction $user_action)
    {
        $data = $order_action->getById($id);
        $user = $user_action->getById($data['user_id']);
        return view('admin.order.detail', compact('data', 'user'));
    }

    public function update_status($id, $status, OrderAction $order_action, MidtransAction $midtrans_action)
    {
        $data = $order_action->getById($id);
        if ($data['status'] === 'Menunggu Konfirmasi' && $status === "Gagal") {
            //Refund
            $request = [
                'amount' => (int) $data['total_price'],
                'reason' => 'Admin Refund'
            ];
            return $midtrans_action->refundTransaction($data['transaction_id'], $request);
        }
        if ($data['status'] === 'Belum Dibayar' && $status === "Gagal") {
            //Cancel
            $midtrans_action->cancelTransaction($data['transaction_id']);
            return redirect()->route('data.pesanan');
        }
        $order_action->updateStatus($id, $status);
        return redirect()->route('data.pesanan');
    }

    public function generateInvoice($order_id, OrderAction $order_action, SystemAction $system_action)
    {
        $system = $system_action->get();
        $order = $order_action->getById($order_id);
        $pdf = Pdf::loadView('admin.order.receipt', compact('order', 'system'))
            ->setPaper('A4');
        return $pdf->download('nota_' . $order->id . '.pdf');
    }

    public function historyOrder(OrderAction $order_action)
    {
        $datas = $order_action->getByStatus('Berhasil');
        return view('admin.order.history', compact('datas'));
    }

    public function reportHistory(Request $request, OrderAction $order_action)
    {

        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $orders = $order_action->getByStatus('Berhasil')->whereBetween('created_at', [$start_date, $end_date]);
        $reportData = [];

        foreach ($orders as $order) {
            foreach ($order->order_items as $item) {
                $productName = $item->product_variant->product->name;
                $variantName = $item->product_variant->name_type;
                $fullProductName = "{$productName} - {$variantName}";

                if (!isset($reportData[$fullProductName])) {
                    $reportData[$fullProductName] = [
                        'sold' => 0,
                        'subtotal' => 0,
                        'remaining_stock' => $item->product_variant->stock,
                    ];
                }

                $reportData[$fullProductName]['sold'] += $item->quantity;
                $reportData[$fullProductName]['subtotal'] += $item->quantity * $item->price;
                $reportData[$fullProductName]['remaining_stock'] -= $item->quantity;
            }
        }

        $pdf = Pdf::loadView('admin.order.report', compact('reportData', 'start_date', 'end_date'));
        return $pdf->download('Laporan_Harian_' . $start_date . '-' . $end_date . '.pdf');
    }

    public function catalogues(ProductAction $product_action, Order_ItemAction $order_item_action)
    {
        $datas = $product_action->get();

        foreach ($datas as $key => $item) {
            $qty = 0;
            if (count($item['product_variants']) > 0) {
                foreach ($item['product_variants'] as $variant) {
                    foreach ($order_item_action->getByVariant($variant['id']) as $order) {
                        $qty += $order['quantity'];
                    }
                }
            }

            $datas[$key]['qty'] = $qty;
        }

        return view('admin.catalogue.catalogue', compact('datas'));
    }

    public function addCatalogue(CategoryAction $category_action)
    {
        $categories = $category_action->get();
        return view('admin.catalogue.add', compact('categories'));
    }

    public function detailCatalogue($id, ProductAction $product_action, CategoryAction $category_action)
    {
        $data = $product_action->getById($id);
        $categories = $category_action->get();
        return view('admin.catalogue.detail', compact('data', 'categories'));
    }

    public function storeCatalogue(Request $request, ProductAction $product_action, Product_VariantAction $product_variant_action)
    {
        $data = [
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id')
        ];
        $product_id = $product_action->create($data);

        if ($request->filled('new_variants')) {
            foreach ($request->file('new_variants', []) as $key => $files) {
                $variant = $request->input("new_variants.$key");

                $photos = [];
                if (isset($files['photos'])) {
                    foreach ($files['photos'] as $photo) {
                        $photos[] = '/storage/' . $photo->store('uploads/products', 'public');
                    }
                }

                $variant_data = [
                    'product_id'   => $product_id,
                    'name_type'    => $variant['name_type'],
                    'description'  => $variant['description'],
                    'price'        => $variant['price'],
                    'stock'        => $variant['stock'],
                    'photos'       => json_encode($photos),
                ];

                $product_variant_action->create($variant_data);
            }
        }

        return redirect()->route('data.katalog');
    }

    public function updateCatalogue(Request $request, $id, ProductAction $product_action, Product_VariantAction $product_variant_action)
    {
        $data = [
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id')
        ];
        $product_action->update($data, $id);

        if ($request->filled('deletedVariantIds')) {
            foreach (json_decode($request->input('deletedVariantIds')) as $variant_id) {
                $product_variant_action->delete($variant_id);
            }
        }

        foreach ($request->input('variants', []) as $key => $variant) {
            $variant_model = \App\Models\Product_Variant::find($variant['id']);
            $existing_photos = json_decode($variant_model->photo ?? '[]', true);

            $deleted_photos = json_decode($variant['deletedPhotos'] ?? '[]', true);

            foreach ($deleted_photos as $photo) {
                $path = str_replace('/storage/', '', $photo);
                \Storage::disk('public')->delete($path);
            }

            $remaining_photos = array_filter($existing_photos, function ($photo) use ($deleted_photos) {
                return !in_array($photo, $deleted_photos);
            });

            $uploaded_photos = [];
            if ($request->hasFile("variants.$key.photos")) {
                foreach ($request->file("variants.$key.photos") as $photo) {
                    $uploaded_photos[] = '/storage/' . $photo->store('uploads/products', 'public');
                }
            }

            $final_photos = array_merge($remaining_photos, $uploaded_photos);

            $variant_data = [
                'name_type'   => $variant['name_type'],
                'description' => $variant['description'],
                'price'       => $variant['price'],
                'stock'       => $variant['stock'],
                'photos'      => json_encode($final_photos),
            ];

            $product_variant_action->update($variant_data, $variant['id']);
        }

        // Tambahkan variant baru
        if ($request->filled('new_variants')) {
            foreach ($request->file('new_variants', []) as $key => $files) {
                $variant = $request->input("new_variants.$key");

                $photos = [];
                if (isset($files['photos'])) {
                    foreach ($files['photos'] as $photo) {
                        $photos[] = '/storage/' . $photo->store('uploads/products', 'public');
                    }
                }

                $variant_data = [
                    'product_id'   => $id,
                    'name_type'    => $variant['name_type'],
                    'description'  => $variant['description'],
                    'price'        => $variant['price'],
                    'stock'        => $variant['stock'],
                    'photos'       => json_encode($photos),
                ];

                $product_variant_action->create($variant_data);
            }
        }

        return redirect()->back()->with('success', 'Data produk berhasil diperbarui.');
    }

    public function deleteCatalogue($id, ProductAction $product_action)
    {
        $product_action->delete($id);
        return redirect()->route('data.katalog');
    }
}
