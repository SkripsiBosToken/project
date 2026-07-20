<x-layout.admin-v2 title="Dashboard" subtitle="Ringkasan aktivitas toko Anda">

    {{-- Ringkasan pesanan per periode --}}
    <section>
        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-500">Pesanan Berhasil</h2>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat label="Hari Ini" :value="number_format($daily, 0, ',', '.')" icon="fa-calendar-day" tone="primary" />
            <x-ui.stat label="Minggu Ini" :value="number_format($weekly, 0, ',', '.')" icon="fa-calendar-week" tone="info" />
            <x-ui.stat label="Bulan Ini" :value="number_format($monthly, 0, ',', '.')" icon="fa-calendar-days" tone="success" />
            <x-ui.stat label="Tahun Ini" :value="number_format($yearly, 0, ',', '.')" icon="fa-calendar" tone="warning" />
        </div>
    </section>

    {{-- Ringkasan keseluruhan --}}
    <section class="mt-8">
        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-500">Ringkasan Toko</h2>
        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat label="Pesanan Berjalan" :value="number_format($progressOrders, 0, ',', '.')"
                icon="fa-truck-fast" tone="warning" hint="Menunggu konfirmasi, diproses, atau dikirim" />
            <x-ui.stat label="Total Pesanan Sukses" :value="number_format($successOrders, 0, ',', '.')"
                icon="fa-circle-check" tone="success" />
            <x-ui.stat label="Jumlah Produk" :value="number_format($product, 0, ',', '.')"
                icon="fa-bowl-food" tone="primary" />
        </div>
    </section>

    {{-- Pintasan --}}
    <section class="mt-8">
        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-500">Pintasan</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
        ['data.pesanan', 'fa-list-check', 'Kelola Pesanan', 'Lihat & ubah status pesanan'],
        ['data.katalog.tambah', 'fa-plus', 'Tambah Produk', 'Tambahkan menu baru'],
        ['data.riwayat.pesanan', 'fa-file-lines', 'Laporan Penjualan', 'Unduh laporan periode'],
        ['setting', 'fa-gear', 'Pengaturan', 'Profil & konten situs'],
    ] as [$route, $icon, $label, $desc])
                <a href="{{ route($route) }}"
                    class="group flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-card transition-all hover:-translate-y-0.5 hover:shadow-card-hover">
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
                        <i class="fa-solid {{ $icon }}"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold text-gray-900">{{ $label }}</span>
                        <span class="mt-0.5 block text-xs text-gray-500">{{ $desc }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Total pelanggan --}}
    <section class="mt-8">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-card">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-info-50 text-info-600">
                        <i class="fa-solid fa-users text-lg"></i>
                    </span>
                    <div>
                        <p class="text-sm text-gray-500">Total Pengguna Terdaftar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($user, 0, ',', '.') }}</p>
                    </div>
                </div>
                <x-ui.button href="{{ route('data.pelanggan') }}" variant="outline" size="sm" iconRight="fa-arrow-right">
                    Lihat Pelanggan
                </x-ui.button>
            </div>
        </div>
    </section>

</x-layout.admin-v2>
