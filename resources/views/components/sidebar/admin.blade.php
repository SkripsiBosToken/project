<nav class="navbar navbar-expand-sm navbar-default">

    <div class="navbar-header">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu"
            aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand" href="{{ route ('dashboard') }}"><h1>{{$setting['name']}}</h1></a>
        
    </div>

    <div class="main-menu">
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="{{ route ('dashboard') }}"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
            </li>
            <h3 class="menu-title">Produk & Katalog</h3>
            <li>
                <a href="{{ route ('data.katalog') }}"> <i class="menu-icon ti-book"></i>Daftar Produk </a>
            </li>
            <li>
                <a href="{{ route ('data.katalog.tambah') }}"> <i class="menu-icon ti-plus"></i>Tambah Produk </a>
            </li>

            <h3 class="menu-title">Pesanan</h3>
            <li>
                <a href="{{ route('data.pesanan') }}"> <i class="menu-icon ti-list"></i>Semua Pesanan</a>
            </li>

            <h3 class="menu-title">Pelanggan & Transaksi</h3>
            <li>
                <a href="{{ route('data.pelanggan') }}"> <i class="menu-icon ti-user"></i>Daftar Pelanggan</a>
            </li>

            <h3 class="menu-title">Laporan Penjualan</h3>
            <li>
                <a href="{{ route('data.riwayat.pesanan') }}"> <i class="menu-icon ti-notepad"></i>Semua Laporan</a>
            </li>

            <h3 class="menu-title">Pengaturan</h3>
            <li>
                <a href="{{ route('setting') }}"> <i class="menu-icon ti-settings"></i>Umum</a>
            </li>
            <li>
                <a href="{{ route('setting.special-product') }}"> <i class="menu-icon ti-star"></i>Spesial Produk</a>
            </li>
            <li>
                <a href="{{ route('setting.customer') }}"> <i class="menu-icon ti-user"></i>Customer Kita</a>
            </li>
            <li>
                <a href="{{ route('setting.social-media') }}"> <i class="menu-icon ti-headphone-alt"></i>Sosial Media</a>
            </li>
            <li>
                <a href="{{ route('setting.event') }}"> <i class="menu-icon ti-calendar"></i>Promo Event</a>
            </li>
        </ul>
    </div>
</nav>
