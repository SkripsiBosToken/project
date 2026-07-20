{{-- Judul kartu sebelumnya tertulis "Data Pelanggan" di halaman produk. --}}
<x-layout.admin-v2 title="Daftar Produk" subtitle="Kelola menu dan varian yang dijual">

    <x-ui.table placeholder="Cari produk atau kategori…" emptyTitle="Belum ada produk"
        emptyMessage="Tambahkan produk pertama Anda untuk mulai berjualan.">

        <x-slot:actions>
            <x-ui.button href="{{ route('data.katalog.tambah') }}" size="sm" icon="fa-plus">Tambah Produk</x-ui.button>
        </x-slot:actions>

        <x-slot:head>
            <th class="px-4 py-3 font-semibold">Nama Produk</th>
            <th class="px-4 py-3 font-semibold">Kategori</th>
            <th class="px-4 py-3 text-right font-semibold">Terjual</th>
            <th class="px-4 py-3 text-right font-semibold">Varian</th>
            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
        </x-slot:head>

        @foreach ($datas as $item)
            <tr data-row class="transition-colors hover:bg-gray-50/70">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>

                <td class="px-4 py-3">
                    <span class="inline-block rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary">
                        {{ $item['category']['name'] ?? 'Tanpa kategori' }}
                    </span>
                </td>

                <td class="px-4 py-3 text-right text-sm text-gray-600">{{ $item['qty'] ?? 0 }}</td>

                <td class="px-4 py-3 text-right text-sm text-gray-600">{{ count($item['product_variants']) }}</td>

                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('data.katalog.detail', ['id' => $item['id']]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900">
                            <i class="fa-solid fa-eye"></i>Detail
                        </a>
                        {{-- Konfirmasi wajib: tautan hapus ini adalah GET biasa
                             dan sebelumnya langsung menghapus tanpa peringatan. --}}
                        <a href="{{ route('data.katalog.hapus', ['id' => $item['id']]) }}"
                            onclick="return confirm('Hapus produk {{ addslashes($item['name']) }}? Tindakan ini tidak bisa dibatalkan.')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-primary-danger transition hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i>Hapus
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

</x-layout.admin-v2>
