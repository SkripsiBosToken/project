<x-layout.admin-v2>
    <div>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Kategori</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Jumlah Produk</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas as $item)
                                            <tr>
                                                <td>{{ $item['name'] }}</td>
                                                <td>{{ count($item['products']) }}</td>
                                                <td class="flex flex-row gap-x-2">
                                                    <a
                                                        href="{{ route('data.kategori.detail', ['id' => $item['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            <i class="fa fa-eye mr-1"></i> Detail
                                                        </button></a>
                                                    <a href="{{ route('data.kategori.hapus', ['id' => $item['id']]) }}">
                                                        <button class="btn btn-danger mt-2">
                                                            <i class="fa fa-trash mr-1"></i> Delete
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
