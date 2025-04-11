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
                                <strong class="card-title">Data Pelanggan</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Pembelian</th>
                                            <th>Varian</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas as $item)
                                            <tr>
                                                <td>{{ $item['name'] }}</td>
                                                <td>{{ $item['category']['name'] }}</td>
                                                <td>{{ $item['qty'] }}</td>
                                                <td>{{ count($item['product_variants']) }}</td>
                                                <td class="flex flex-row gap-x-2">
                                                    <a href="{{ route('data.katalog.detail', ['id' => $item['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            Detail
                                                        </button></a>
                                                    <a href="{{ route('data.katalog.hapus', ['id' => $item['id']]) }}">
                                                        <button class="btn btn-danger mt-2">
                                                            Delete
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
