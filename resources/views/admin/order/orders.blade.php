@php
    use App\Support\OrderStatus;

    // Transisi status yang diizinkan per status saat ini. Sebelumnya semua
    // opsi selalu tampil, sehingga pesanan yang BELUM DIBAYAR pun bisa
    // ditandai "Berhasil" — melewati proses pembayaran sepenuhnya.
    $allowedTransitions = [
        OrderStatus::UNPAID => [OrderStatus::FAILED],
        OrderStatus::WAITING_CONFIRMATION => [OrderStatus::PROCESSING, OrderStatus::FAILED],
        OrderStatus::PROCESSING => [OrderStatus::SHIPPED, OrderStatus::FAILED],
        OrderStatus::SHIPPED => [OrderStatus::COMPLETED],
        OrderStatus::COMPLETED => [],
        OrderStatus::FAILED => [],
        OrderStatus::REFUNDED => [],
    ];

    $orders = collect($datas)->sortByDesc('created_at');
@endphp

<x-layout.admin-v2 title="Data Pesanan" subtitle="Kelola dan perbarui status pesanan pelanggan">

    <div x-data="{ status: '' }">

        {{-- Filter status --}}
        <div class="no-scrollbar mb-4 flex gap-2 overflow-x-auto pb-1">
            <button type="button" @click="status = ''"
                class="whitespace-nowrap rounded-lg border px-3.5 py-2 text-sm font-medium transition"
                :class="status === '' ? 'border-primary bg-primary text-white' : 'border-gray-300 bg-white text-gray-600 hover:border-primary hover:text-primary'">
                Semua ({{ $orders->count() }})
            </button>

            @foreach (OrderStatus::all() as $statusOption)
                @php $count = $orders->where('status', $statusOption)->count(); @endphp
                @if ($count > 0)
                    <button type="button" @click="status = status === '{{ $statusOption }}' ? '' : '{{ $statusOption }}'"
                        class="whitespace-nowrap rounded-lg border px-3.5 py-2 text-sm font-medium transition"
                        :class="status === '{{ $statusOption }}' ? 'border-primary bg-primary text-white' : 'border-gray-300 bg-white text-gray-600 hover:border-primary hover:text-primary'">
                        {{ $statusOption }} ({{ $count }})
                    </button>
                @endif
            @endforeach
        </div>

        <x-ui.table placeholder="Cari pesanan, produk, atau pelanggan…" emptyTitle="Belum ada pesanan"
            emptyMessage="Pesanan yang masuk akan tampil di sini.">

            <x-slot:head>
                <th class="px-4 py-3 font-semibold">Pesanan</th>
                <th class="px-4 py-3 font-semibold">Pelanggan</th>
                <th class="px-4 py-3 font-semibold">Status</th>
                <th class="px-4 py-3 text-right font-semibold">Total</th>
                <th class="px-4 py-3 font-semibold">Tanggal</th>
                <th class="px-4 py-3 text-right font-semibold">Aksi</th>
            </x-slot:head>

            @foreach ($orders as $order)
                <tr data-row x-show="status === '' || status === '{{ $order['status'] }}'"
                    class="align-top transition-colors hover:bg-gray-50/70">

                    <td class="px-4 py-3">
                        <p class="font-mono text-xs text-gray-400">
                            #{{ Str::upper(Str::limit($order['id'], 8, '')) }}
                        </p>
                        <ul class="mt-1 space-y-0.5">
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

                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $order['user']['name'] ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $order['user']['phone_number'] ?? '' }}</p>
                    </td>

                    <td class="px-4 py-3">
                        <x-ui.status-badge :status="$order['status']" size="sm" />
                    </td>

                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                        Rp {{ number_format($order['total_price'], 0, ',', '.') }}
                    </td>

                    {{-- Tanggal diformat sepenuhnya di server. Versi lama
                         memformat di server LALU menjalankan new Date() atas
                         hasilnya di browser, yang selalu menghasilkan
                         "Invalid Date". --}}
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($order['created_at'])->locale('id')->isoFormat('D MMM YYYY') }}
                        <span class="block text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($order['created_at'])->format('H:i') }}
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center justify-end gap-1.5">
                            <a href="{{ route('detail.pesanan', ['id' => $order['id']]) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900">
                                <i class="fa-solid fa-eye"></i>Detail
                            </a>

                            @php $transitions = $allowedTransitions[$order['status']] ?? []; @endphp
                            @if ($transitions)
                                <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                                    <button @click="open = !open" :aria-expanded="open"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-primary hover:text-primary">
                                        Ubah Status <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                    </button>

                                    <div x-show="open" x-cloak x-transition @click.away="open = false"
                                        class="absolute right-0 z-10 mt-1 w-44 overflow-hidden rounded-lg border border-gray-200 bg-white py-1 shadow-card-hover">
                                        @foreach ($transitions as $target)
                                            <a href="{{ route('ubah-status.pesanan', ['id' => $order['id'], 'status' => $target]) }}"
                                                onclick="return confirm('Ubah status pesanan ini menjadi {{ $target }}?')"
                                                class="block px-3 py-2 text-xs transition-colors
                                                    {{ $target === OrderStatus::FAILED ? 'text-primary-danger hover:bg-red-50' : 'text-gray-700 hover:bg-primary-50 hover:text-primary' }}">
                                                @if ($target === OrderStatus::FAILED)
                                                    {{ $order['status'] === OrderStatus::UNPAID ? 'Batalkan Pesanan' : 'Batalkan & Refund' }}
                                                @else
                                                    Tandai {{ $target }}
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (! in_array($order['status'], [OrderStatus::UNPAID, OrderStatus::FAILED], true))
                                <a href="{{ route('nota.pesanan', ['id' => $order['id']]) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-primary hover:text-primary">
                                    <i class="fa-solid fa-print"></i>Nota
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>
    </div>

</x-layout.admin-v2>
