<x-layout.admin-v2 title="Rating Pelanggan" subtitle="Ulasan yang dikirim pelanggan setelah pesanan selesai">

    <x-ui.table placeholder="Cari nama pelanggan atau isi ulasan…" emptyTitle="Belum ada ulasan"
        emptyMessage="Ulasan pelanggan akan tampil di sini setelah pesanan selesai.">

        <x-slot:head>
            <th class="px-4 py-3 font-semibold">Pelanggan</th>
            <th class="px-4 py-3 font-semibold">Rating</th>
            <th class="px-4 py-3 font-semibold">Ulasan</th>
            <th class="px-4 py-3 font-semibold">Tanggal</th>
        </x-slot:head>

        @foreach ($datas as $item)
            <tr data-row class="align-top transition-colors hover:bg-gray-50/70">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                            {{ Str::upper(Str::substr($item['user']['name'] ?? '?', 0, 1)) }}
                        </span>
                        <span class="text-sm font-medium text-gray-900">{{ $item['user']['name'] ?? 'Pelanggan' }}</span>
                    </div>
                </td>

                <td class="whitespace-nowrap px-4 py-3">
                    <div class="flex items-center gap-0.5" aria-label="Nilai {{ $item['rate'] }} dari 5">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star text-xs {{ $i <= $item['rate'] ? 'text-amber-400' : 'text-gray-200' }}"></i>
                        @endfor
                        <span class="ml-1.5 text-xs font-semibold text-gray-600">{{ $item['rate'] }}/5</span>
                    </div>
                </td>

                <td class="max-w-md px-4 py-3 text-sm text-gray-600">
                    {{ $item['message'] ?: '—' }}
                </td>

                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($item['created_at'])->locale('id')->isoFormat('D MMM YYYY') }}
                    <span class="block text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($item['created_at'])->format('H:i') }}
                    </span>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

</x-layout.admin-v2>
