<x-layout.admin-v2>
    <div>
        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Table</a></li>
                            <li class="active">Data table</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Pilih Rentang Pendataan</strong>
                            </div>
                            <div class="card-body">
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <x-form.custom action="{{ route('data.riwayat.laporan.pesanan') }}"
                                            method="get">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Pilih Rentang
                                                    Tanggal</label>
                                                <div class="grid md:grid-cols-2 gap-4">
                                                    <input type="date" class="form-control" name="start_date"
                                                        aria-required="true" aria-invalid="false" required>
                                                    <input type="date" class="form-control" name="end_date"
                                                        aria-required="true" aria-invalid="false" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-2">Download
                                                    Laporan</button>
                                            </div>
                                        </x-form.custom>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Laporan Pesanan</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Total Harga</th>
                                            <th>Tanggal Pemesanan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas as $order)
                                            <tr>
                                                <td>{{ $order['id'] }}</td>
                                                <td>
                                                    @foreach ($order['order_items'] as $item)
                                                        <li>{{ $item['product_variant']['product']['name'] }} -
                                                            {{ $item['product_variant']['name_type'] }}
                                                            ({{ $item['quantity'] }}X)
                                                        </li>
                                                    @endforeach
                                                </td>
                                                <td>{{ 'Rp ' . number_format($order['total_price'], 0, ',', '.') }}</td>
                                                <td id="created-at-{{ $order['id'] }}">{{ $order['created_at'] }}</td>

                                                <td class="flex flex-row gap-x-2">
                                                    <a href="{{ route('detail.pesanan', ['id' => $order['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            Detail
                                                        </button></a>
                                                    @if ($order['status'] !== 'Belum Dibayar' && $order['status'] !== 'Gagal')
                                                        <a href="{{ route('nota.pesanan', ['id' => $order['id']]) }}">
                                                            <button class="btn btn-info mt-2">
                                                                Cetak Nota
                                                            </button></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin-v2>

<script>
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        const dateOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        const timeOptions = {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: false
        };
        return date.toLocaleDateString('id-ID', dateOptions) + ', ' + date.toLocaleTimeString('id-ID', timeOptions);
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("[id^='created-at-']").forEach(element => {
            element.textContent = formatDateTime(element.textContent);
        });
    });
</script>
