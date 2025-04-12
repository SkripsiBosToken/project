<x-layout.admin-v2>
    <div>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Pesanan</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="btn-group" role="group" aria-label="Filter Status">
                                        @php
                                            $statuses = [
                                                'Belum Dibayar',
                                                'Menunggu Konfirmasi',
                                                'Diproses',
                                                'Dikirim',
                                                'Berhasil',
                                                'Gagal',
                                            ];
                                        @endphp
                                        <button type="button" class="btn btn-secondary active"
                                            data-status="">Semua</button>
                                        @foreach ($statuses as $status)
                                            <button type="button" class="btn btn-secondary"
                                                data-status="{{ $status }}">{{ $status }}</button>
                                        @endforeach
                                    </div>
                                </div>

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
                                            <tr data-status="{{ $order['status'] }}">
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
                                                <td id="created-at-{{ $order['id'] }}">{{ \Carbon\Carbon::parse($order['created_at'])->translatedFormat('d F Y, H:i:s') }}</td>
                                                <td class="flex flex-row gap-x-2">
                                                    <a href="{{ route('detail.pesanan', ['id' => $order['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            <i class="fa fa-eye mr-1"></i> Detail
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
                                                                <i class="fa fa-print mr-1"></i> Cetak Nota
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
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("[id^='created-at-']").forEach(element => {
            element.textContent = formatDateTime(element.textContent);
        });

        const buttons = document.querySelectorAll('[data-status]');
        let activeStatus = "";

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const status = this.getAttribute('data-status');

                // Toggle button active
                buttons.forEach(btn => btn.classList.remove('active'));
                if (activeStatus !== status) {
                    this.classList.add('active');
                    activeStatus = status;
                } else {
                    activeStatus = "";
                    document.querySelector('[data-status=""]').classList.add('active');
                }

                document.querySelectorAll('tbody tr').forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (activeStatus === "" || rowStatus === activeStatus) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        });
    });

    function formatDateTime(dateString) {
        const date = new Date(dateString);
        if (isNaN(date)) return "Invalid Date";

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
</script>
