<x-layout.admin-v2>
    <div>
        <div class="content my-3">
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
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Point</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($datas as $item)
                                            @if ($item['role']['name'] !== 'Admin')
                                                <tr>
                                                    <td>{{ $item['name'] }}</td>
                                                    <td>{{ $item['email'] }}</td>
                                                    <td>{{ $item['phone_number'] }}</td>
                                                    <td>{{ $item['point'] }}</td>
                                                    <td class="flex flex-row gap-x-2">
                                                        <a
                                                            href="{{ route('detail.pelanggan', ['id' => $item['id']]) }}">
                                                            <button class="btn btn-primary mt-2">
                                                                <i class="fa fa-eye mr-1"></i> Detail
                                                            </button></a>
                                                    </td>

                                                </tr>
                                            @endif
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
