@php
    // Akun admin disembunyikan dari daftar pelanggan.
    $customers = collect($datas)->filter(fn ($item) => ($item['role']['name'] ?? null) !== 'Admin');
@endphp

<x-layout.admin-v2 title="Daftar Pelanggan" subtitle="{{ $customers->count() }} pelanggan terdaftar">

    <x-ui.table placeholder="Cari nama, email, atau nomor telepon…" emptyTitle="Belum ada pelanggan"
        emptyMessage="Pelanggan yang mendaftar akan tampil di sini.">

        <x-slot:head>
            <th class="px-4 py-3 font-semibold">Nama</th>
            <th class="px-4 py-3 font-semibold">Email</th>
            <th class="px-4 py-3 font-semibold">Telepon</th>
            <th class="px-4 py-3 text-right font-semibold">Poin</th>
            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
        </x-slot:head>

        @foreach ($customers as $item)
            <tr data-row class="transition-colors hover:bg-gray-50/70">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                            {{ Str::upper(Str::substr($item['name'], 0, 1)) }}
                        </span>
                        <span class="text-sm font-medium text-gray-900">{{ $item['name'] }}</span>
                    </div>
                </td>

                <td class="px-4 py-3 text-sm text-gray-600">{{ $item['email'] }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $item['phone_number'] }}</td>

                <td class="px-4 py-3 text-right">
                    <span class="inline-block rounded-full bg-warning-50 px-2.5 py-1 text-xs font-semibold text-warning-700">
                        {{ number_format($item['point'] ?? 0, 0, ',', '.') }}
                    </span>
                </td>

                <td class="px-4 py-3 text-right">
                    <a href="{{ route('detail.pelanggan', ['id' => $item['id']]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900">
                        <i class="fa-solid fa-eye"></i>Detail
                    </a>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

</x-layout.admin-v2>
