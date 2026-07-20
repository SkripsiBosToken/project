@php
    $orders = collect($datas)->sortByDesc('created_at');
    $revenue = $orders->sum('total_price');
@endphp

<x-layout.admin-v2 title="Riwayat & Laporan" subtitle="Pesanan yang sudah berhasil diselesaikan">

    {{-- Ringkasan --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <x-ui.stat label="Pesanan Berhasil" :value="number_format($orders->count(), 0, ',', '.')"
            icon="fa-circle-check" tone="success" />
        <x-ui.stat label="Total Pendapatan" value="Rp {{ number_format($revenue, 0, ',', '.') }}"
            icon="fa-sack-dollar" tone="primary" />
    </div>

    {{-- Unduh laporan --}}
    <x-ui.card title="Unduh Laporan Penjualan" icon="fa-file-arrow-down"
        subtitle="Pilih rentang tanggal, lalu unduh laporan dalam format PDF." class="mb-6">

        <form method="GET" action="{{ route('data.riwayat.laporan.pesanan') }}"
            class="flex flex-wrap items-end gap-4">
            <div class="min-w-[160px] flex-1">
                <label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" required max="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>

            <div class="min-w-[160px] flex-1">
                <label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" required max="{{ now()->format('Y-m-d') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>

            <x-ui.button type="submit" icon="fa-download">Unduh Laporan</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.table placeholder="Cari pesanan atau produk…" emptyTitle="Belum ada pesanan selesai"
        emptyMessage="Pesanan yang berstatus Berhasil akan tampil di sini.">

        <x-slot:head>
            <th class="px-4 py-3 font-semibold">Pesanan</th>
            <th class="px-4 py-3 font-semibold">Produk</th>
            <th class="px-4 py-3 text-right font-semibold">Total</th>
            <th class="px-4 py-3 font-semibold">Tanggal</th>
            <th class="px-4 py-3 text-right font-semibold">Aksi</th>
        </x-slot:head>

        @foreach ($orders as $order)
            <tr data-row class="align-top transition-colors hover:bg-gray-50/70">
                <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-gray-500">
                    #{{ Str::upper(Str::limit($order['id'], 8, '')) }}
                </td>

                <td class="px-4 py-3">
                    <ul class="space-y-0.5">
                        @forelse ($order['order_items'] as $item)
                            <li class="text-sm text-gray-900">
                                {{ $item['product_variant']['product']['name'] ?? 'Produk dihapus' }}
                                @if (! empty($item['product_variant']['name_type']))
                                    <span class="text-gray-500">— {{ $item['product_variant']['name_type'] }}</span>
                                @endif
                                <span class="font-semibold text-primary">×{{ $item['quantity'] }}</span>
                            </li>
                        @empty
                            <li class="text-sm italic text-gray-400">Tidak ada rincian item</li>
                        @endforelse
                    </ul>
                </td>

                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                    Rp {{ number_format($order['total_price'], 0, ',', '.') }}
                </td>

                {{-- Diformat di server; skrip lama memformat ulang di browser. --}}
                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($order['created_at'])->locale('id')->isoFormat('D MMM YYYY') }}
                    <span class="block text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($order['created_at'])->format('H:i') }}
                    </span>
                </td>

                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-1.5">
                        <a href="{{ route('detail.pesanan', ['id' => $order['id']]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900">
                            <i class="fa-solid fa-eye"></i>Detail
                        </a>
                        <a href="{{ route('nota.pesanan', ['id' => $order['id']]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-primary hover:text-primary">
                            <i class="fa-solid fa-print"></i>Nota
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

</x-layout.admin-v2>
