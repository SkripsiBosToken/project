<?php

namespace App\Http\Controllers;

use App\Actions\CategoryAction;
use App\Actions\MidtransAction;
use App\Actions\Order_ItemAction;
use App\Actions\OrderAction;
use App\Actions\Product_VariantAction;
use App\Actions\ProductAction;
use App\Actions\RateAction;
use App\Actions\SystemAction;
use App\Actions\UserAction;
use App\Support\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard(OrderAction $order_action, UserAction $user_action, ProductAction $product_action)
    {
        $product = $product_action->get()->count();
        $user = $user_action->get()->count();
        $orders = $order_action->get();

        $progressOrders = $orders->whereIn('status', OrderStatus::IN_PROGRESS)->count();
        $successfulOrders = $orders->whereIn('status', OrderStatus::SUCCESSFUL);
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

    /** Jumlah slot produk spesial yang ditampilkan di halaman pengaturan. */
    private const SPECIAL_PRODUCT_SLOTS = 4;

    public function specialProduct(SystemAction $system_action, ProductAction $product_action)
    {
        $products = $product_action->get();

        // json_decode bisa mengembalikan null (kolom kosong/JSON rusak), dan
        // isinya bisa kurang dari 4 entri. View menampilkan 4 slot tetap, jadi
        // array-nya selalu dinormalisasi ke panjang itu — sebelumnya halaman
        // ini error "Undefined array key 0" pada instalasi baru yang kolom
        // special_product-nya masih kosong.
        $datas = json_decode($system_action->get()['special_product'] ?? '[]', true) ?: [];

        $specialProduct = [];
        for ($slot = 0; $slot < self::SPECIAL_PRODUCT_SLOTS; $slot++) {
            $productId = $datas[$slot] ?? null;

            $specialProduct[] = [
                'product_id' => $productId,
                'product' => $productId ? $product_action->getById($productId) : null,
            ];
        }

        return view('admin.setting.special-product', compact('specialProduct', 'products'));
    }

    // Ketiga method di bawah memakai `?: []` agar kolom JSON yang kosong atau
    // rusak menghasilkan array kosong, bukan null. Tanpa itu, view-nya
    // melakukan foreach atas null dan halaman pengaturan error.
    public function ourCustomer(SystemAction $system_action)
    {
        $customers = json_decode($system_action->get()['our_customer'] ?? '[]', true) ?: [];
        return view('admin.setting.customer', compact('customers'));
    }

    public function socialMedia(SystemAction $system_action)
    {
        $medias = json_decode($system_action->get()['social_media'] ?? '[]', true) ?: [];
        return view('admin.setting.social-media', compact('medias'));
    }

    /**
     * Lebar kartu layanan pada grid halaman Tentang Kami.
     * `otomatis` mengikuti pola mosaik berselang-seling.
     */
    public const SERVICE_SIZES = ['otomatis', 'kecil', 'sedang', 'besar', 'penuh'];

    public function service(SystemAction $system_action)
    {
        $services = json_decode($system_action->get()['our_service'] ?? '[]', true) ?: [];

        return view('admin.setting.service', compact('services'));
    }

    /**
     * Menyimpan daftar "Layanan Kami": ubah judul/foto, tambah layanan baru,
     * dan hapus yang tidak dipakai. Foto lama ikut dihapus dari disk supaya
     * tidak menumpuk menjadi berkas yatim.
     */
    public function updateService(Request $request, SystemAction $system_action)
    {
        $sizes = implode(',', self::SERVICE_SIZES);

        $request->validate([
            'services.*.label' => ['required', 'string', 'max:80'],
            'services.*.size' => ['required', 'in:' . $sizes],
            // Foto divalidasi tipe & ukurannya: tanpa ini file apa pun bisa
            // diunggah ke folder publik.
            'services.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'new_services.*.label' => ['required', 'string', 'max:80'],
            'new_services.*.size' => ['required', 'in:' . $sizes],
            'new_services.*.image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [], [
            'services.*.label' => 'judul layanan',
            'new_services.*.label' => 'judul layanan baru',
            'services.*.image' => 'foto layanan',
            'new_services.*.image' => 'foto layanan baru',
        ]);

        $services = json_decode($system_action->get()['our_service'] ?? '[]', true) ?: [];

        $deleted = json_decode($request->input('deleted_service_indexes'), true);
        $deleted = is_array($deleted) ? $deleted : [];

        foreach ($deleted as $index) {
            if (isset($services[$index])) {
                $this->deleteUploadedFile($services[$index]['image'] ?? null);
                unset($services[$index]);
            }
        }

        foreach ($request->input('services', []) as $index => $item) {
            if (! isset($services[$index])) {
                continue;
            }

            $services[$index]['label'] = $item['label'];
            $services[$index]['size'] = $item['size'];

            if ($file = $request->file("services.$index.image")) {
                $this->deleteUploadedFile($services[$index]['image'] ?? null);
                $services[$index]['image'] = '/storage/' . $file->store('uploads/services', 'public');
            }
        }

        foreach ($request->file('new_services', []) as $index => $files) {
            $label = $request->input("new_services.$index.label");
            $size = $request->input("new_services.$index.size");

            if (! isset($files['image'])) {
                continue;
            }

            $services[] = [
                'label' => $label,
                'image' => '/storage/' . $files['image']->store('uploads/services', 'public'),
                'size' => $size,
            ];
        }

        // array_values: indeks dirapikan setelah penghapusan agar tetap
        // menjadi array JSON, bukan objek dengan kunci melompat.
        $system_action->update(['our_service' => json_encode(array_values($services))]);

        return redirect()->back()->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Menghapus berkas hasil unggahan. Hanya menyentuh berkas di dalam
     * folder uploads agar aset bawaan (/assets/images/...) tidak ikut hilang.
     */
    private function deleteUploadedFile(?string $url): void
    {
        if (! $url || ! Str::startsWith($url, '/storage/uploads/')) {
            return;
        }

        $path = public_path(ltrim(str_replace('/storage/', 'storage/', $url), '/'));

        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function event(SystemAction $system_action)
    {
        $events = json_decode($system_action->get()['promo_event'] ?? '[]', true) ?: [];
        return view('admin.setting.event', compact('events'));
    }

    public function updateSetting(Request $request, SystemAction $system_action)
    {
        // $oldData = $system_action->get();

        // $office_address = [
        //     'address' => $request->input('address'),
        //     'postal_code' => $request->input('postal_code'),
        //     'latitude' => $request->input('latitude'),
        //     'longitude' => $request->input('longitude'),
        // ];

        // $data = [
        //     'name' => $request->input('name'),
        //     'phone_number' => $request->input('phone_number'),
        //     'visi' => $request->input('visi'),
        //     'misi' => $request->input('misi'),
        //     'office_address' => json_encode($office_address)
        // ];

        // if ($request->hasFile('logo')) {
        //     if ($oldData && $oldData->logo) {
        //         $oldLogoPath = str_replace('/storage/', '', $oldData->logo);
        //         if (\Storage::disk('public')->exists($oldLogoPath)) {
        //             \Storage::disk('public')->delete($oldLogoPath);
        //         }
        //     }

        //     $path = $request->file('logo')->store('uploads/logo', 'public');
        //     $data['logo'] = '/storage/' . $path;
        // }

        // $system_action->update($data);

        // return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
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
            'office_address' => json_encode($office_address),
            'our_coverage' => json_encode(json_decode($request->input('our_coverage'), true)), // tambahan
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
        $request->validate([
            'product_01' => ['nullable', 'string', 'exists:products,id'],
            'product_02' => ['nullable', 'string', 'exists:products,id'],
            'product_03' => ['nullable', 'string', 'exists:products,id'],
            'product_04' => ['nullable', 'string', 'exists:products,id'],
        ]);

        // Slot kosong dibuang, bukan disimpan sebagai string kosong — nilai ""
        // akan membuat halaman depan mencari produk dengan id kosong.
        $specialProduct = array_values(array_filter([
            $request['product_01'],
            $request['product_02'],
            $request['product_03'],
            $request['product_04'],
        ]));

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

    /**
     * Mengubah status pesanan dari panel admin.
     *
     * Perbaikan dari versi sebelumnya:
     *  - Jalur refund dulu langsung mengembalikan array mentah (tampil sebagai
     *    JSON di browser) dan TIDAK PERNAH mengubah status pesanan, sehingga
     *    pesanan yang sudah direfund tetap tampil "Menunggu Konfirmasi".
     *  - Jalur cancel juga tidak mengubah status dan tidak mengembalikan stok.
     *  - Hasil dari Midtrans tidak pernah diperiksa, jadi refund/cancel yang
     *    ditolak gateway tetap dianggap berhasil.
     */
    public function update_status($id, $status, OrderAction $order_action, MidtransAction $midtrans_action)
    {
        if (! in_array($status, OrderStatus::all(), true)) {
            abort(400, 'Status pesanan tidak dikenal.');
        }

        $order = $order_action->getById($id);

        abort_if(! $order, 404);

        // Membatalkan pesanan yang sudah dibayar => refund ke Midtrans.
        if ($order['status'] === OrderStatus::WAITING_CONFIRMATION && $status === OrderStatus::FAILED) {
            $response = $midtrans_action->refundTransaction($order['transaction_id'], [
                'amount' => (int) $order['total_price'],
                'reason' => 'Admin Refund',
            ]);

            if (! $midtrans_action->isSuccessful($response)) {
                return redirect()->route('data.pesanan')->with(
                    'error',
                    'Refund gagal: ' . ($response['status_message'] ?? 'gateway menolak permintaan refund.')
                );
            }

            $this->failOrderAndRestoreStock($order, OrderStatus::REFUNDED);

            return redirect()->route('data.pesanan')->with('success', 'Refund berhasil diproses.');
        }

        // Membatalkan pesanan yang belum dibayar => cancel di Midtrans.
        if ($order['status'] === OrderStatus::UNPAID && $status === OrderStatus::FAILED) {
            $response = $midtrans_action->cancelTransaction($order['transaction_id']);

            if (! $midtrans_action->isSuccessful($response)) {
                return redirect()->route('data.pesanan')->with(
                    'error',
                    'Pembatalan gagal: ' . ($response['status_message'] ?? 'gateway menolak pembatalan.')
                );
            }

            $this->failOrderAndRestoreStock($order, OrderStatus::FAILED);

            return redirect()->route('data.pesanan')->with('success', 'Pesanan dibatalkan.');
        }

        $order_action->updateStatus($id, $status);

        return redirect()->route('data.pesanan')->with('success', 'Status pesanan diperbarui.');
    }

    /**
     * Menandai pesanan gagal/refund sekaligus mengembalikan stok varian.
     */
    private function failOrderAndRestoreStock($order, string $newStatus): void
    {
        DB::transaction(function () use ($order, $newStatus) {
            foreach ($order->order_items as $item) {
                if ($item->product_variant_id) {
                    DB::table('product_variants')
                        ->where('id', $item->product_variant_id)
                        ->increment('stock', (int) $item->quantity);
                }
            }

            $order->status = $newStatus;
            $order->save();
        });
    }

    public function generateInvoice($order_id, OrderAction $order_action, SystemAction $system_action)
    {
        $system = $system_action->get();
        $order = $order_action->getById($order_id);

        abort_if(! $order, 404);

        // Method ini juga terdaftar di route pelanggan (`order/receipt/{id}`)
        // yang hanya dilindungi middleware auth, jadi tanpa pengecekan ini
        // pelanggan mana pun bisa mengunduh nota pesanan milik orang lain.
        $viewer = Auth::user();
        abort_if(! $viewer->isAdmin() && $order->user_id !== $viewer->id, 403);
        $pdf = Pdf::loadView('admin.order.receipt', compact('order', 'system'))
            ->setPaper('A4');
        return $pdf->download('nota_' . $order->id . '.pdf');
    }

    public function historyOrder(OrderAction $order_action)
    {
        $datas = $order_action->getByStatus(OrderStatus::COMPLETED);
        return view('admin.order.history', compact('datas'));
    }

    public function reportHistory(Request $request, OrderAction $order_action)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start_date = $validated['start_date'];
        $end_date = $validated['end_date'];

        // endOfDay() penting: tanggal tanpa jam dianggap pukul 00:00, sehingga
        // pesanan pada hari terakhir rentang justru tidak ikut terhitung.
        $orders = $order_action->getByStatus(OrderStatus::COMPLETED)
            ->whereBetween('created_at', [
                Carbon::parse($start_date)->startOfDay(),
                Carbon::parse($end_date)->endOfDay(),
            ]);

        $reportData = [];

        foreach ($orders as $order) {
            foreach ($order->order_items as $item) {
                if (! $item->product_variant) {
                    continue;
                }

                $productName = $item->product_variant->product->name ?? 'Produk dihapus';
                $fullProductName = "{$productName} - {$item->product_variant->name_type}";

                if (! isset($reportData[$fullProductName])) {
                    $reportData[$fullProductName] = [
                        'sold' => 0,
                        'subtotal' => 0,
                        // Stok varian sudah dipotong saat checkout, jadi nilai
                        // ini langsung dipakai apa adanya. Versi sebelumnya
                        // menguranginya lagi dengan qty terjual sehingga sisa
                        // stok pada laporan selalu lebih kecil dari kenyataan.
                        'remaining_stock' => (int) $item->product_variant->stock,
                    ];
                }

                $reportData[$fullProductName]['sold'] += $item->quantity;
                $reportData[$fullProductName]['subtotal'] += $item->quantity * $item->price;
            }
        }

        $pdf = Pdf::loadView('admin.order.report', compact('reportData', 'start_date', 'end_date'));
        return $pdf->download('Laporan_' . $start_date . '_sd_' . $end_date . '.pdf');
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
            // Input ini berasal dari klien; JSON yang tidak valid akan membuat
            // json_decode mengembalikan null dan foreach-nya error.
            $deletedIds = json_decode($request->input('deletedVariantIds'), true);

            foreach (is_array($deletedIds) ? $deletedIds : [] as $variant_id) {
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

    public function categories(CategoryAction $category_action, ProductAction $product_action)
    {
        $datas = $category_action->get();
        return view('admin.category.category', compact('datas'));
    }

    public function detailCategory($id, CategoryAction $category_action)
    {
        $data = $category_action->getById($id);
        return view('admin.category.detail', compact('data'));
    }

    public function updateCategory(Request $request, $id, CategoryAction $category_action)
    {
        $data = [
            'name' => $request['name'],
            'description' => $request['description']
        ];
        $category_action->update($data, $id);
        return redirect()->route('data.kategori');
    }

    public function addCategory()
    {
        return view('admin.category.add');
    }

    public function storeCategory(Request $request, CategoryAction $category_action)
    {
        $data = [
            'name' => $request['name'],
            'description' => $request['description']
        ];
        $category_action->create($data);
        return redirect()->route('data.kategori');
    }

    public function deleteCategory($id, CategoryAction $category_action)
    {
        $category_action->delete($id);
        return redirect()->route('data.kategori');
    }

    public function rates(RateAction $rate_action)
    {
        $datas = $rate_action->get();
        return view('admin.user.rate', compact('datas'));
    }
}
