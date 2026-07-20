@php
    use App\Support\OrderStatus;

    $shipping = json_decode($order->shipping_address, true) ?: [];

    // Subtotal dihitung dari harga yang TERSIMPAN di order_items, bukan harga
    // varian saat ini. Kalau harga produk berubah setelah pesanan dibuat,
    // versi lama menampilkan angka yang tidak cocok dengan yang dibayar.
    $itemsTotal = $order->order_items->sum('subtotal');
    $shippingCost = max(0, (int) $order->total_price - (int) $itemsTotal);

    // Transaksi bisa kosong (penagihan gagal) atau tanpa VA (QRIS), jadi
    // semua akses di bawah harus aman.
    $va = $transaction['va_numbers'][0] ?? null;
    $paymentType = $transaction['payment_type'] ?? null;
    $methodLabel = match (true) {
        $paymentType === 'qris' => 'QRIS',
        $va !== null => strtoupper($va['bank']) . ' Virtual Account',
        ! empty($transaction['permata_va_number']) => 'PERMATA Virtual Account',
        default => 'Belum ditentukan',
    };

    $timeline = [
        OrderStatus::UNPAID => 0,
        OrderStatus::WAITING_CONFIRMATION => 1,
        OrderStatus::PROCESSING => 2,
        OrderStatus::SHIPPED => 3,
        OrderStatus::COMPLETED => 4,
    ];
    $currentStep = $timeline[$order->status] ?? null;
@endphp

<x-layout.customer title="Detail Pesanan | Kusuka Catering" :noindex="true">

    <div class="my-8 md:my-12">

        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('order-list') }}" class="transition-colors hover:text-primary">Pesanan Saya</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="font-medium text-gray-900">Detail</span>
        </nav>

        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Detail Pesanan</h1>
                <p class="mt-1 font-mono text-sm text-gray-500">#{{ Str::upper(Str::limit($order->id, 13, '')) }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-ui.status-badge :status="$order->status" />

                @if ($order->status !== OrderStatus::UNPAID)
                    <x-ui.button href="{{ route('getReceipt', ['id' => $order->id]) }}" size="sm" variant="outline"
                        icon="fa-print">
                        Cetak Nota
                    </x-ui.button>
                @else
                    <x-ui.button href="{{ route('payment', ['id' => $order->id]) }}" size="sm" icon="fa-credit-card">
                        Bayar Sekarang
                    </x-ui.button>
                @endif
            </div>
        </div>

        {{-- Lini masa status --}}
        @if ($currentStep !== null)
            <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-card">
                <ol class="flex items-center">
                    @foreach (['Dipesan', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai'] as $index => $label)
                        @php $done = $index <= $currentStep; @endphp
                        <li class="flex flex-1 items-center {{ $loop->last ? 'flex-none' : '' }}">
                            <div class="flex flex-col items-center gap-1.5">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold
                                    {{ $done ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400' }}">
                                    @if ($done)
                                        <i class="fa-solid fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </span>
                                <span class="whitespace-nowrap text-[11px] font-medium {{ $done ? 'text-primary' : 'text-gray-400' }}">
                                    {{ $label }}
                                </span>
                            </div>
                            @if (! $loop->last)
                                <div class="mx-1 mb-5 h-0.5 flex-1 rounded {{ $index < $currentStep ? 'bg-primary' : 'bg-gray-200' }}"></div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @elseif (OrderStatus::isFailed($order->status))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <p class="text-sm">
                    Pesanan ini berstatus <strong>{{ $order->status }}</strong>.
                    Stok yang sempat dipesan sudah kami kembalikan.
                </p>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">

                {{-- Item pesanan --}}
                <x-ui.card title="Rincian Produk" icon="fa-bowl-food" padding="p-0">
                    @if ($order->order_items->isEmpty())
                        <x-ui.empty icon="fa-box-open" title="Tidak ada rincian item"
                            message="Pesanan ini tidak memiliki rincian produk." />
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($order->order_items as $item)
                                <li class="flex items-center gap-4 p-5">
                                    @php
                                        $photo = json_decode($item->product_variant->photo ?? '[]', true)[0] ?? '/placeholder.jpg';
                                    @endphp
                                    <img src="{{ $photo }}" alt="" loading="lazy"
                                        class="h-16 w-16 flex-shrink-0 rounded-lg border border-gray-100 object-cover">

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-semibold text-gray-900">
                                            {{ $item->product_variant->product->name ?? 'Produk tidak tersedia' }}
                                            @if ($item->product_variant?->name_type)
                                                — {{ $item->product_variant->name_type }}
                                            @endif
                                        </p>
                                        <p class="mt-0.5 text-sm text-gray-500">
                                            Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                                        </p>
                                    </div>

                                    <p class="whitespace-nowrap font-semibold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                {{-- Alamat --}}
                <x-ui.card title="Alamat Pengiriman" icon="fa-location-dot">
                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                    <p class="mt-0.5 text-sm text-gray-500">{{ $user->phone_number }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">
                        {{ $shipping['address'] ?? 'Alamat tidak tercatat.' }}
                    </p>
                </x-ui.card>
            </div>

            {{-- Ringkasan pembayaran --}}
            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-4">
                    <x-ui.card title="Rincian Pembayaran" icon="fa-receipt">
                        <dl class="space-y-2.5 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Metode</dt>
                                <dd class="text-right font-medium text-gray-900">{{ $methodLabel }}</dd>
                            </div>

                            @if ($va)
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">No. Virtual Account</dt>
                                    <dd class="text-right font-mono text-xs font-medium text-gray-900">
                                        {{ $va['va_number'] }}
                                    </dd>
                                </div>
                            @endif

                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Tanggal Pesan</dt>
                                <dd class="text-right font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->isoFormat('D MMM YYYY, HH.mm') }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-3 border-t border-gray-100 pt-2.5">
                                <dt class="text-gray-500">Subtotal Produk</dt>
                                <dd class="text-right font-medium text-gray-900">
                                    Rp {{ number_format($itemsTotal, 0, ',', '.') }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-3">
                                <dt class="text-gray-500">Ongkos Kirim</dt>
                                <dd class="text-right font-medium text-gray-900">
                                    Rp {{ number_format($shippingCost, 0, ',', '.') }}
                                </dd>
                            </div>

                            <div class="flex justify-between gap-3 border-t border-gray-100 pt-2.5 text-base font-bold">
                                <dt>Total</dt>
                                <dd class="text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </x-ui.card>

                    <x-ui.button href="{{ route('order-list') }}" variant="muted" block icon="fa-arrow-left">
                        Kembali ke Daftar Pesanan
                    </x-ui.button>
                </div>
            </aside>
        </div>
    </div>

</x-layout.customer>
