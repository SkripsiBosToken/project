<nav class="navbar navbar-expand-sm navbar-default">

    <div class="navbar-header">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu"
            aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand" href="./"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo"></a>
        <a class="navbar-brand hidden" href="./"><img src="{{ asset('assets/images/logo2.png') }}" alt="Logo"></a>
    </div>

    <div id="main-menu" class="main-menu collapse navbar-collapse">
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="index.html"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
            </li>
            <h3 class="menu-title">Produk & Katalog</h3><!-- /.menu-title -->
            <li>
                <a href="widgets.html"> <i class="menu-icon ti-email"></i>Daftar Produk </a>
            </li>
            <li>
                <a href="widgets.html"> <i class="menu-icon ti-email"></i>Tambah Produk </a>
            </li>

            <h3 class="menu-title">Pesanan</h3>
            <li>
                <a href="{{ route('data.pesanan') }}"> <i class="menu-icon ti-email"></i>Semua Pesanan</a>
            </li>

            <h3 class="menu-title">Pelanggan & Transaksi</h3>
            <li>
                <a href="{{ route('data.pelanggan') }}"> <i class="menu-icon ti-email"></i>Daftar Pelanggan</a>
            </li>

            <h3 class="menu-title">Laporan Penjualan</h3>
            <li>
                <a href="{{ route('data.riwayat.pesanan') }}"> <i class="menu-icon ti-email"></i>Semua Laporan</a>
            </li>
        </ul>
    </div>
</nav>
