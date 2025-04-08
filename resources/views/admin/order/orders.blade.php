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
                                <strong class="card-title">Data Pesanan</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Status</th>
                                            <th>Total Harga</th>
                                            <th>Tanggal Pemesanan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas as $order)
                                            <tr>
                                                <td>
                                                    @foreach ($order['order_items'] as $item)
                                                        <li>{{ $item['product_variant']['product']['name'] }} -
                                                            {{ $item['product_variant']['name_type'] }}
                                                            ({{ $item['quantity'] }}X)
                                                        </li>
                                                    @endforeach
                                                </td>
                                                <td>{{ $order['status'] }}</td>
                                                <td>{{ 'Rp ' . number_format($order['total_price'], 0, ',', '.') }}</td>
                                                <td id="created-at-{{ $order['id'] }}">{{ $order['created_at'] }}</td>

                                                <td class="flex flex-row gap-x-2">
                                                    <a href="{{ route('detail.pesanan', ['id' => $order['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            Detail
                                                        </button></a>
                                                    <div class="user-area dropdown float-right">
                                                        <button class="btn btn-warning mt-2" href="#"
                                                            class="dropdown-toggle" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            Ubah Status
                                                        </button>

                                                        <div class="user-menu dropdown-menu">
                                                            @if ($order['status'] === 'Belum Dibayar')
                                                                <a class="nav-link"
                                                                    href="{{ route('ubah-status.pesanan', ['id' => $order['id'], 'status' => 'Gagal']) }}">Batalkan</a>
                                                            @endif
                                                            <a class="nav-link"
                                                                href="{{ route('ubah-status.pesanan', ['id' => $order['id'], 'status' => 'Diproses']) }}">Diproses</a>
                                                            <a class="nav-link"
                                                                href="{{ route('ubah-status.pesanan', ['id' => $order['id'], 'status' => 'Dikirim']) }}">Dikirim</a>
                                                                <a class="nav-link"
                                                                    href="{{ route('ubah-status.pesanan', ['id' => $order['id'], 'status' => 'Berhasil']) }}">Berhasil</a>
                                                        </div>
                                                    </div>
                                                    @if ($order['status'] !== 'Belum Dibayar' && $order['status'] !== 'Gagal')
                                                        <a href="{{ route('nota.pesanan', ['id' => $order['id']]) }}"><button
                                                                class="btn btn-info mt-2">
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
