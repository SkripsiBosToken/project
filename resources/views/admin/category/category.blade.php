<x-layout.admin-v2 title="Daftar Kategori" subtitle="Kelompokkan produk agar mudah ditemukan pelanggan">

    <x-ui.table placeholder="Cari kategori…" emptyTitle="Belum ada kategori"
        emptyMessage="Buat kategori untuk mengelompokkan produk Anda.">

        <x-slot:actions>
            <x-ui.button href="{{ route('data.kategori.tambah') }}" size="sm" icon="fa-plus">Tambah Kategori</x-ui.button>
        </x-slot:actions>

        <x-slot:head>
            <th class="px-4 py-3 font-semibold">Nama Kategori</th>
            <th class="px-4 py-3 text-right font-semibold">Jumlah Produk</th>
            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
        </x-slot:head>

        @foreach ($datas as $item)
            <tr data-row class="transition-colors hover:bg-gray-50/70">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>

                <td class="px-4 py-3 text-right">
                    @php $count = count($item['products']); @endphp
                    <span class="inline-block rounded-full px-2.5 py-1 text-xs font-medium
                        {{ $count > 0 ? 'bg-primary-50 text-primary' : 'bg-gray-100 text-gray-500' }}">
                        {{ $count }} produk
                    </span>
                </td>

                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('data.kategori.detail', ['id' => $item['id']]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900">
                            <i class="fa-solid fa-eye"></i>Detail
                        </a>
                        <a href="{{ route('data.kategori.hapus', ['id' => $item['id']]) }}"
                            onclick="return confirm('Hapus kategori {{ addslashes($item['name']) }}?{{ $count > 0 ? ' Kategori ini masih memiliki ' . $count . ' produk.' : '' }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-primary-danger transition hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i>Hapus
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

</x-layout.admin-v2>
