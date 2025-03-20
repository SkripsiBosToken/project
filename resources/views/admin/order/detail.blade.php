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
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Pembelian</strong>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">ID</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ $data['id'] }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Alamat Pengiriman</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ json_decode($data['shipping_address'], true)['address'] }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Status</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ $data['status'] }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Total</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ 'Rp ' . number_format($data['total_price'], 0, ',', '.') }}" disabled>
                                </div>
                                <div class="form-group mb-8">
                                    <label for="cc-payment" class="control-label mb-1">Tanggal Pemesanan</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ \Carbon\Carbon::parse($data['created_at'])->locale('id')->translatedFormat('d F Y, H:i') }}"
                                        disabled>
                                </div>

                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>SubTotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalPriceCatalog = 0;
                                        @endphp
                                        @foreach ($data['order_items'] as $order)
                                            <tr>
                                                <td>{{ $order['product_variant']['product']['name'] }} -
                                                    {{ $order['product_variant']['name_type'] }}</td>
                                                <td>{{ $order['product_variant']['product']['category']['name'] }}</td>
                                                <td>{{ 'Rp ' . number_format($order['product_variant']['price'], 0, ',', '.') }}
                                                </td>
                                                <td>{{ $order['quantity'] }}</td>
                                                <td>{{ 'Rp ' . number_format($order['product_variant']['price'] * $order['quantity'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @php
                                                $totalPriceCatalog =
                                                    $totalPriceCatalog +
                                                    $order['product_variant']['price'] * $order['quantity'];
                                            @endphp
                                        @endforeach

                                        <tr>
                                            <td>Shipping Payment</td>
                                            <td>Ongkir</td>
                                            <td>{{ 'Rp ' . number_format($data['total_price'] - $totalPriceCatalog, 0, ',', '.') }}
                                            </td>
                                            <td>1</td>
                                            <td>{{ 'Rp ' . number_format($data['total_price'] - $totalPriceCatalog, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="form-group mt-8">
                                    <label for="cc-payment" class="control-label mb-1">Destinasi</label>
                                    @php
                                        $officeAddress = [];
                                        $address = json_decode($data['shipping_address'], true);
                                        $data = [
                                            'lat' => (float) $address['latitude'],
                                            'lng' => (float) $address['longitude'],
                                            'label' => 'Destinasi',
                                        ];
                                        array_push($officeAddress, $data);
                                    @endphp

                                    @if (is_array($officeAddress))
                                        <x-map.custom :pinArea="$officeAddress" />
                                    @else
                                        <p>Data lokasi kantor tidak valid atau tidak tersedia.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Pelanggan</strong>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Nama</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ $user['name'] }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Nama Pengguna</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ $user['username'] }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Email</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ $user['email'] }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="cc-payment" class="control-label mb-1">Nomor Telpon</label>
                                    <input type="text" class="form-control" aria-required="true" aria-invalid="false"
                                        value="{{ $user['phone_number'] }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin-v2>
