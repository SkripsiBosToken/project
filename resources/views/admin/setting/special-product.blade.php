<x-layout.admin-v2>
    <form action="{{ route('system.special-product.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="content mt-3">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Special Product 1</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                @if ($specialProduct[0]['product'] != null)
                                    <input type="text" class="form-control"
                                        value="{{ $specialProduct[0]['product']['name'] }}" disabled>
                                @else
                                    <input type="text" class="form-control"
                                        value="Produk tidak ditemukan atau sudah dihapus" disabled>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Pilih Produk</label>
                                <select name="product_01" class="form-control">
                                    <option value="{{ $specialProduct[0]['product_id'] }}">
                                        @if ($specialProduct[0]['product'] != null)
                                            {{ $specialProduct[0]['product']['name'] }}
                                        @else
                                            Produk tidak ditemukan atau sudah dihapus
                                        @endif
                                    </option>
                                    @foreach ($products as $product)
                                        @if ($specialProduct[0]['product_id'] != $product['id'])
                                            <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Special Product 2</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                @if ($specialProduct[1]['product'] != null)
                                    <input type="text" class="form-control"
                                        value="{{ $specialProduct[1]['product']['name'] }}" disabled>
                                @else
                                    <input type="text" class="form-control"
                                        value="Produk tidak ditemukan atau sudah dihapus" disabled>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Pilih Produk</label>
                                <select name="product_02" class="form-control">
                                    <option value="{{ $specialProduct[1]['product_id'] }}">
                                        @if ($specialProduct[1]['product'] != null)
                                            {{ $specialProduct[1]['product']['name'] }}
                                        @else
                                            Produk tidak ditemukan atau sudah dihapus
                                        @endif
                                    </option>
                                    @foreach ($products as $product)
                                        @if ($specialProduct[1]['product_id'] != $product['id'])
                                            <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Special Product 3</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                @if ($specialProduct[2]['product'] != null)
                                    <input type="text" class="form-control"
                                        value="{{ $specialProduct[2]['product']['name'] }}" disabled>
                                @else
                                    <input type="text" class="form-control"
                                        value="Produk tidak ditemukan atau sudah dihapus" disabled>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Pilih Produk</label>
                                <select name="product_03" class="form-control">
                                    <option value="{{ $specialProduct[2]['product_id'] }}">
                                        @if ($specialProduct[2]['product'] != null)
                                            {{ $specialProduct[2]['product']['name'] }}
                                        @else
                                            Produk tidak ditemukan atau sudah dihapus
                                        @endif
                                    </option>
                                    @foreach ($products as $product)
                                        @if ($specialProduct[2]['product_id'] != $product['id'])
                                            <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Special Product 4</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                @if ($specialProduct[3]['product'] != null)
                                    <input type="text" class="form-control"
                                        value="{{ $specialProduct[3]['product']['name'] }}" disabled>
                                @else
                                    <input type="text" class="form-control"
                                        value="Produk tidak ditemukan atau sudah dihapus" disabled>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Pilih Produk</label>
                                <select name="product_04" class="form-control">
                                    <option value="{{ $specialProduct[3]['product_id'] }}">
                                        @if ($specialProduct[3]['product'] != null)
                                            {{ $specialProduct[3]['product']['name'] }}
                                        @else
                                            Produk tidak ditemukan atau sudah dihapus
                                        @endif
                                    </option>
                                    @foreach ($products as $product)
                                        @if ($specialProduct[3]['product_id'] != $product['id'])
                                            <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-floppy-o mr-2" aria-hidden="true"></i>
                Simpan</button>
        </div>
    </form>
</x-layout.admin-v2>
