<x-layout.admin-v2>
    <div>
        <div class="content my-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Pelanggan Detail</strong>
                            </div>
                            <div class="card-body">
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Name</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false" value="{{ $data['name'] }}" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Username</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false" value="{{ $data['username'] }}" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Email</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false" value="{{ $data['email'] }}" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Birth Date</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false"
                                                value="{{ \Carbon\Carbon::parse($data['birth_date'])->locale('id')->translatedFormat('d F Y') }}"
                                                disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Phone Number</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false" value="{{ $data['phone_number'] }}" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Point</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false" value="{{ $data['point'] }}" disabled>
                                        </div>

                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Address</label>
                                            <input type="text" class="form-control" aria-required="true"
                                                aria-invalid="false"
                                                value="{{ json_decode($data['address'] ?? '{}', true)['address'] ?? '-' }}" disabled>
                                        </div>


                                        <div class="form-group">
                                            <label for="cc-payment" class="control-label mb-1">Address Point</label>
                                            @php
                                                // Sebelumnya blok ini menimpa variabel $data (model user)
                                                // dengan array koordinat, dan mengakses latitude/longitude
                                                // tanpa guard sehingga error bila alamat belum lengkap.
                                                $address = json_decode($data['address'] ?? '{}', true) ?: [];

                                                $hasCoordinates = isset($address['latitude'], $address['longitude'])
                                                    && $address['latitude'] !== ''
                                                    && $address['longitude'] !== '';

                                                $pinArea = $hasCoordinates
                                                    ? [[
                                                        'lat' => (float) $address['latitude'],
                                                        'lng' => (float) $address['longitude'],
                                                        'label' => 'Destinasi',
                                                    ]]
                                                    : [];
                                            @endphp

                                            @if ($hasCoordinates)
                                                <x-map.custom :pinArea="$pinArea" />
                                                <p class="text-primary-danger text-sm">*Pencet untuk melihat pada google
                                                    map</p>
                                            @else
                                                <p class="text-sm text-gray-500">Pelanggan ini belum menandai titik lokasi
                                                    pada alamatnya.</p>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Pembelian</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Harga</th>
                                            <th>Tanggal Pesanan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
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
                                                <td>{{ 'Rp ' . number_format($order['total_price'], 0, ',', '.') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($order['created_at'])->locale('id')->translatedFormat('d F Y, H:i') }}
                                                </td>
                                                <td class="flex flex-row gap-x-2">
                                                    <a href="{{ route('detail.pesanan', ['id' => $order['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            <i class="fa fa-eye mr-1"></i> Detail
                                                        </button></a>
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
