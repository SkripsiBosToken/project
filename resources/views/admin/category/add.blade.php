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
                        <x-form.custom action="{{ route('data.kategori.store') }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <strong class="card-title">Tambah Kategori</strong>
                                </div>
                                <div class="card-body">
                                    <div id="pay-invoice">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="cc-payment" class="control-label mb-1">Name</label>
                                                <input name="name" type="text" class="form-control" aria-required="true"
                                                    aria-invalid="false" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Deskripsi</label>
                                                <textarea class="form-control" name="description" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <button type="submit" class="btn btn-success">Tambah</button>
                    </x-form.custom>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin-v2>
