@php
    $shipping = json_decode($data['shipping_address'], true) ?: [];

    // Subtotal memakai harga & subtotal yang tersimpan di order_items, bukan
    // harga varian saat ini — supaya angka tetap cocok dengan yang dibayar
    // pelanggan meski harga produk berubah setelahnya.
    $itemsTotal = collect($data['order_items'])->sum('subtotal');
    $shippingCost = max(0, (int) $data['total_price'] - (int) $itemsTotal);
@endphp

<x-layout.admin-v2 title="Detail Pesanan" subtitle="#{{ Str::upper(Str::limit($data['id'], 13, '')) }}">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <x-ui.button href="{{ route('data.pesanan') }}" variant="muted" size="sm" icon="fa-arrow-left">
            Kembali
        </x-ui.button>

        <div class="flex items-center gap-2">
            <x-ui.status-badge :status="$data['status']" />
            @if (! in_array($data['status'], ['Belum Dibayar', 'Gagal'], true))
                <x-ui.button href="{{ route('nota.pesanan', ['id' => $data['id']]) }}" variant="outline" size="sm"
                    icon="fa-print">Cetak Nota</x-ui.button>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        <div class="space-y-6 lg:col-span-2">

            {{-- Rincian produk --}}
            <x-ui.card title="Rincian Produk" icon="fa-bowl-food" padding="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50/70 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Produk</th>
                                <th class="px-4 py-3 font-semibold">Kategori</th>
                                <th class="px-4 py-3 text-right font-semibold">Harga</th>
                                <th class="px-4 py-3 text-right font-semibold">Qty</th>
                                <th class="px-4 py-3 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($data['order_items'] as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $item['product_variant']['product']['name'] ?? 'Produk dihapus' }}
                                        @if (! empty($item['product_variant']['name_type']))
                                            <span class="text-gray-500">— {{ $item['product_variant']['name_type'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $item['product_variant']['product']['category']['name'] ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-gray-600">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ $item['quantity'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm italic text-gray-400">
                                        Pesanan ini tidak memiliki rincian item.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-900">Ongkos Kirim</td>
                                <td class="px-4 py-3 text-gray-500">Pengiriman</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-gray-600">
                                    Rp {{ number_format($shippingCost, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600">1</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($shippingCost, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-900">Total</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-base font-bold text-primary">
                                    Rp {{ number_format($data['total_price'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-ui.card>

            {{-- Alamat --}}
            <x-ui.card title="Alamat Pengiriman" icon="fa-location-dot">
                <p class="text-sm leading-relaxed text-gray-600">
                    {{ $shipping['address'] ?? 'Alamat tidak tercatat.' }}
                </p>
            </x-ui.card>
        </div>

        {{-- Info pesanan & pelanggan --}}
        <aside class="space-y-4 lg:col-span-1">
            <x-ui.card title="Informasi Pesanan" icon="fa-circle-info">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">ID Pesanan</dt>
                        <dd class="mt-0.5 break-all font-mono text-xs text-gray-900">{{ $data['id'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tanggal Pemesanan</dt>
                        <dd class="mt-0.5 font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($data['created_at'])->locale('id')->isoFormat('D MMMM YYYY, HH.mm') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="mt-1"><x-ui.status-badge :status="$data['status']" size="sm" /></dd>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card title="Data Pelanggan" icon="fa-user">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
                        {{ Str::upper(Str::substr($user['name'], 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-gray-900">{{ $user['name'] }}</p>
                        <p class="truncate text-xs text-gray-500">{{ '@' . $user['username'] }}</p>
                    </div>
                </div>

                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="mt-0.5 break-all font-medium text-gray-900">{{ $user['email'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Nomor Telepon</dt>
                        <dd class="mt-0.5 font-medium text-gray-900">{{ $user['phone_number'] }}</dd>
                    </div>
                </dl>

                <x-ui.button href="{{ route('detail.pelanggan', ['id' => $user['id']]) }}" variant="outline"
                    size="sm" block class="mt-4" iconRight="fa-arrow-right">
                    Lihat Profil Pelanggan
                </x-ui.button>
            </x-ui.card>
        </aside>
    </div>

</x-layout.admin-v2>
