@php
    $paymentMethods = [
        ['value' => 'bca', 'label' => 'BCA Virtual Account', 'desc' => 'Transfer via ATM, m-BCA, atau KlikBCA'],
        ['value' => 'bni', 'label' => 'BNI Virtual Account', 'desc' => 'Transfer via ATM atau BNI Mobile'],
        ['value' => 'bri', 'label' => 'BRI Virtual Account', 'desc' => 'Transfer via ATM atau BRImo'],
        ['value' => 'permata', 'label' => 'Permata Virtual Account', 'desc' => 'Transfer via ATM atau PermataMobile'],
        ['value' => 'qris', 'label' => 'QRIS', 'desc' => 'GoPay, OVO, DANA, ShopeePay, LinkAja'],
    ];
@endphp

<x-layout.customer title="Checkout | Kusuka Catering" :noindex="true">
    <div class="my-8 md:my-12"
        x-data="checkoutPage({
            items: {{ Illuminate\Support\Js::from($datas) }},
            shippingCost: {{ (int) $shippingCost }},
        })">

        {{-- Langkah pemesanan --}}
        <nav class="mb-8 flex items-center justify-center gap-2 text-xs md:text-sm" aria-label="Progres pemesanan">
            @foreach (['Keranjang', 'Checkout', 'Pembayaran'] as $i => $step)
                <div class="flex items-center gap-2">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold
                        {{ $i <= 1 ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }}">
                        {{ $i + 1 }}
                    </span>
                    <span class="{{ $i === 1 ? 'font-semibold text-primary' : 'text-primary-gray' }}">{{ $step }}</span>
                </div>
                @if (! $loop->last)
                    <span class="h-px w-6 bg-gray-300 md:w-12"></span>
                @endif
            @endforeach
        </nav>

        @if ($outOfRange)
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800">
                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                <div class="text-sm">
                    <p class="font-semibold">Alamat di luar jangkauan pengiriman</p>
                    <p class="mt-1">
                        Jarak alamat Anda melebihi {{ (int) config('shipping.max_distance_km') }} km dari dapur kami.
                        Silakan <a href="{{ route('profile') }}" class="font-semibold underline">perbarui alamat</a>
                        atau hubungi kami untuk pengaturan khusus.
                    </p>
                </div>
            </div>
        @endif

        <h1 class="mb-6 text-2xl font-bold">Checkout</h1>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Kolom kiri --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Alamat pengiriman --}}
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-base font-bold">
                            <i class="fa-solid fa-location-dot text-primary"></i> Alamat Pengiriman
                        </h2>
                        <a href="{{ route('profile') }}"
                            class="text-sm font-semibold text-primary hover:underline">Ubah</a>
                    </div>

                    <div class="mt-4 rounded-lg bg-gray-50 p-4">
                        <p class="font-semibold">{{ Auth::user()->name }}</p>
                        <p class="mt-1 text-sm text-primary-gray">{{ Auth::user()->phone_number }}</p>
                        <p class="mt-2 text-sm text-primary-gray">{{ $address['address'] }}</p>
                    </div>
                </section>

                {{-- Daftar pesanan --}}
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <i class="fa-solid fa-bowl-food text-primary"></i> Pesanan Anda
                        <span class="text-sm font-normal text-primary-gray" x-text="'(' + items.length + ' item)'"></span>
                    </h2>

                    <ul class="mt-4 divide-y divide-gray-100">
                        <template x-for="item in items" :key="item.product.id">
                            <li class="flex items-center gap-4 py-4">
                                <img :src="photoOf(item)" :alt="item.product.product.name"
                                    class="h-16 w-16 flex-shrink-0 rounded-lg border border-gray-100 object-cover"
                                    onerror="this.src='/placeholder.jpg'">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold"
                                        x-text="item.product.product.name + ' — ' + item.product.name_type"></p>
                                    <p class="mt-0.5 text-sm text-primary-gray"
                                        x-text="rupiah(item.product.price) + ' × ' + item.qty"></p>
                                    <p class="mt-1 text-xs" :class="stockClass(item)" x-text="stockLabel(item)"></p>
                                </div>
                                <p class="whitespace-nowrap font-semibold"
                                    x-text="rupiah(item.product.price * item.qty)"></p>
                            </li>
                        </template>
                    </ul>
                </section>

                {{-- Metode pembayaran --}}
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <i class="fa-solid fa-credit-card text-primary"></i> Metode Pembayaran
                    </h2>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($paymentMethods as $method)
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition"
                                :class="paymentType === '{{ $method['value'] }}'
                                    ? 'border-primary bg-primary/5 ring-1 ring-primary'
                                    : 'border-gray-200 hover:border-primary/40'">
                                <input type="radio" name="payment_type" value="{{ $method['value'] }}"
                                    x-model="paymentType" form="checkout-form" class="mt-1 accent-[#860000]">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold">{{ $method['label'] }}</span>
                                    <span class="mt-0.5 block text-xs text-primary-gray">{{ $method['desc'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            </div>

            {{-- Ringkasan --}}
            <aside class="lg:col-span-1">
                <form id="checkout-form" method="POST" action="{{ route('checkout.payment') }}"
                    @submit="submitting = true" class="sticky top-6">
                    @csrf
                    {{--
                        Hanya jenis pembelian dan daftar item yang dikirim.
                        Alamat, koordinat, dan ongkir dihitung ulang di server
                        dari data profil user, sehingga tidak bisa dimanipulasi
                        lewat hidden input seperti pada versi sebelumnya.
                    --}}
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="item_details" :value="serializedItems">

                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-base font-bold">Ringkasan Pembayaran</h2>

                        <ul class="mt-4 space-y-2 text-sm">
                            <template x-for="item in items" :key="item.product.id">
                                <li class="flex justify-between gap-3">
                                    <span class="truncate text-primary-gray"
                                        x-text="item.product.product.name + ' × ' + item.qty"></span>
                                    <span class="whitespace-nowrap"
                                        x-text="rupiah(item.product.price * item.qty)"></span>
                                </li>
                            </template>
                        </ul>

                        <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm">
                            <div class="flex justify-between text-primary-gray">
                                <span>Subtotal</span>
                                <span x-text="rupiah(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-primary-gray">
                                <span class="flex items-center gap-1">
                                    Ongkos Kirim
                                    <i class="fa-solid fa-circle-info text-xs"
                                        title="Dihitung otomatis dari jarak alamat Anda ke dapur kami"></i>
                                </span>
                                <span x-text="rupiah(shippingCost)"></span>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between border-t border-gray-100 pt-4 text-lg font-bold">
                            <span>Total</span>
                            <span class="text-primary" x-text="rupiah(total)"></span>
                        </div>

                        <button type="submit" :disabled="!canSubmit"
                            class="mt-5 flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 text-sm font-semibold text-white transition hover:bg-primary-secondary disabled:cursor-not-allowed disabled:opacity-50">
                            <template x-if="submitting">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                            </template>
                            <template x-if="!submitting">
                                <i class="fa-solid fa-lock"></i>
                            </template>
                            <span x-text="submitting ? 'Memproses…' : 'Bayar Sekarang'"></span>
                        </button>

                        <p class="mt-3 text-center text-xs text-primary-gray">
                            <i class="fa-solid fa-shield-halved mr-1"></i>
                            Transaksi aman dan terenkripsi
                        </p>
                    </div>
                </form>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            function checkoutPage(config) {
                return {
                    items: config.items,
                    shippingCost: config.shippingCost,
                    paymentType: 'bca',
                    submitting: false,

                    get subtotal() {
                        return this.items.reduce((sum, item) => sum + item.product.price * item.qty, 0);
                    },

                    get total() {
                        return this.subtotal + this.shippingCost;
                    },

                    get outOfStock() {
                        return this.items.some(item => item.qty > item.product.stock);
                    },

                    get canSubmit() {
                        return !this.submitting && this.items.length > 0 && !this.outOfStock;
                    },

                    get serializedItems() {
                        return JSON.stringify(this.items.map(item => ({
                            id: item.product.id,
                            quantity: item.qty,
                        })));
                    },

                    photoOf(item) {
                        try {
                            return JSON.parse(item.product.photo)[0];
                        } catch (e) {
                            return '/placeholder.jpg';
                        }
                    },

                    stockLabel(item) {
                        if (item.qty > item.product.stock) {
                            return `Stok tidak mencukupi (tersisa ${item.product.stock})`;
                        }
                        if (item.product.stock <= 5) {
                            return `Stok terbatas — tersisa ${item.product.stock}`;
                        }
                        return 'Stok tersedia';
                    },

                    stockClass(item) {
                        if (item.qty > item.product.stock) return 'text-red-600 font-semibold';
                        if (item.product.stock <= 5) return 'text-amber-600';
                        return 'text-green-600';
                    },

                    rupiah(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        }).format(value);
                    },
                };
            }
        </script>
    @endpush
</x-layout.customer>
