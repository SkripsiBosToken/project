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
        <div class="content my-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-6">
                        <x-form.custom action="{{ route('data.kategori.update', ['id' => $data['id']]) }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <strong class="card-title">Kategori Detail</strong>
                                </div>
                                <div class="card-body">
                                    <div id="pay-invoice">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Name</label>
                                                <input name="name" type="text" class="form-control" aria-required="true"
                                                    aria-invalid="false" value="{{ $data['name'] }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Deskripsi</label>
                                                <textarea class="form-control" name="description" required>{{ $data['description'] }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </x-form.custom>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Data Produk</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['products'] as $item)
                                            <tr>
                                                <td>{{ $item['name'] }}</td>
                                                <td class="flex flex-row gap-x-2">
                                                    <a href="{{ route('data.katalog.detail', ['id' => $item['id']]) }}">
                                                        <button class="btn btn-primary mt-2">
                                                            Detail
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
