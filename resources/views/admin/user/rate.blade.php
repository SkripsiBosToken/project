<x-layout.admin-v2>
    <div>
        <div class="content my-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Rating Pelanggan</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Pelanggan</th>
                                            <th>Rating </th>
                                            <th>Pesan</th>
                                            <th>Tanggal Review</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas as $item)
                                            <tr>
                                                <td>{{ $item['user']['name'] }}</td>
                                                <td>{{ $item['rate'] }}</td>
                                                <td>{{ $item['message'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item['created_at'])->translatedFormat('d F Y, H:i:s') }}
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
