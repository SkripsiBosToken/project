<?php

namespace App\Http\Controllers;

use App\Actions\MidtransAction;
use App\Actions\OrderAction;
use App\Actions\ProductAction;
use App\Actions\SystemAction;
use App\Actions\TransactionAction;
use App\Actions\UserAction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
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

    public function order_detail($id, OrderAction $order_action, UserAction $user_action){
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

    public function catalogues(ProductAction $product_action){
        $datas = $product_action->get();
        return view('admin.catalogue.catalogue', compact('datas'));
    }
}
