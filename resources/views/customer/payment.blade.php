@php
    use App\Support\OrderStatus;

    $isQris = ($data['payment_type'] ?? null) === 'qris';
    $va = $data['va_numbers'][0] ?? null;
    $bank = strtoupper($va['bank'] ?? ($data['permata_va_number'] ?? null ? 'permata' : ''));
    $vaNumber = $va['va_number'] ?? ($data['permata_va_number'] ?? null);

    $status = $data['transaction_status'] ?? 'pending';
    $isPaid = in_array($status, ['settlement', 'capture'], true);
    $isExpired = in_array($status, ['expire', 'cancel', 'deny', 'failure'], true);

    // expiry_time dari Midtrans berformat "Y-m-d H:i:s" dalam zona WIB.
    $expiresAt = null;
    if (! empty($data['expiry_time'])) {
        try {
            $expiresAt = \Carbon\Carbon::parse($data['expiry_time'], 'Asia/Jakarta');
        } catch (\Throwable $e) {
            $expiresAt = null;
        }
    }

    $instructions = [
        'ATM' => [
            'Masukkan kartu ATM dan PIN Anda.',
            'Pilih menu <b>Transaksi Lain</b> → <b>Transfer</b> → <b>Virtual Account</b>.',
            'Masukkan nomor Virtual Account di atas.',
            'Periksa nama penerima dan nominal tagihan pada layar konfirmasi.',
            'Konfirmasi pembayaran, lalu simpan struk sebagai bukti.',
        ],
        'Mobile Banking' => [
            'Login ke aplikasi mobile banking Anda.',
            'Pilih menu <b>Transfer</b> → <b>Virtual Account</b>.',
            'Masukkan nomor Virtual Account di atas.',
            'Pastikan nominal tagihan sudah sesuai.',
            'Masukkan PIN untuk menyelesaikan pembayaran.',
        ],
        'Internet Banking' => [
            'Login ke internet banking Anda.',
            'Pilih menu <b>Pembayaran</b> → <b>Virtual Account</b>.',
            'Masukkan nomor Virtual Account di atas.',
            'Konfirmasi dengan token/OTP yang dikirim bank Anda.',
            'Simpan bukti pembayaran.',
        ],
    ];

    $qrisInstructions = [
        'Buka aplikasi e-wallet atau mobile banking yang mendukung QRIS (GoPay, OVO, DANA, ShopeePay, LinkAja).',
        'Pilih menu <b>Bayar</b> atau <b>Scan QR</b>.',
        'Arahkan kamera ke kode QR di atas, atau unggah gambar QR yang sudah diunduh.',
        'Periksa nama merchant dan nominal pembayaran.',
        'Masukkan PIN untuk menyelesaikan pembayaran.',
    ];
@endphp

<x-layout.customer title="Pembayaran | Kusuka Catering" :noindex="true">
    <div class="my-8 md:my-12" x-data="paymentPage({
        statusUrl: '{{ route('payment.status', ['id' => $order->id]) }}',
        orderUrl: '{{ route('order-detail', ['id' => $order->id]) }}',
        expiresAt: {{ $expiresAt ? $expiresAt->timestamp * 1000 : 'null' }},
        initialStatus: '{{ $status }}',
    })">

        {{-- Langkah pemesanan --}}
        <nav class="mb-8 flex items-center justify-center gap-2 text-xs md:text-sm" aria-label="Progres pemesanan">
            @foreach (['Keranjang', 'Checkout', 'Pembayaran'] as $i => $step)
                <div class="flex items-center gap-2">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold
                        {{ $i === 2 ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }}">
                        {{ $i + 1 }}
                    </span>
                    <span class="{{ $i === 2 ? 'font-semibold text-primary' : 'text-primary-gray' }}">{{ $step }}</span>
                </div>
                @if (! $loop->last)
                    <span class="h-px w-6 bg-gray-300 md:w-12"></span>
                @endif
            @endforeach
        </nav>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Kolom utama --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Status & hitung mundur --}}
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gray-50/70 px-6 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-primary-gray">Nomor Pesanan</p>
                                <p class="font-mono text-sm font-semibold">{{ Str::upper(Str::limit($order->id, 13, '')) }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="badgeClass"
                                x-text="statusLabel">{{ Str::upper($status) }}</span>
                        </div>
                    </div>

                    <div class="px-6 py-8 text-center">
                        {{-- Menunggu pembayaran --}}
                        <template x-if="state === 'pending'">
                            <div>
                                <p class="text-sm text-primary-gray">Selesaikan pembayaran dalam</p>
                                <div class="mt-3 flex items-center justify-center gap-2" x-show="expiresAt">
                                    <template x-for="(unit, i) in countdownUnits" :key="i">
                                        <div class="flex items-center gap-2">
                                            <div class="min-w-[64px] rounded-lg bg-primary/5 px-3 py-2">
                                                <div class="text-2xl font-bold tabular-nums text-primary"
                                                    x-text="unit.value"></div>
                                                <div class="text-[10px] uppercase tracking-wide text-primary-gray"
                                                    x-text="unit.label"></div>
                                            </div>
                                            <span x-show="i < countdownUnits.length - 1"
                                                class="text-xl font-bold text-primary/30">:</span>
                                        </div>
                                    </template>
                                </div>
                                @if ($expiresAt)
                                    <p class="mt-3 text-xs text-primary-gray">
                                        Batas waktu {{ $expiresAt->translatedFormat('d M Y, H:i') }} WIB
                                    </p>
                                @endif
                            </div>
                        </template>

                        {{-- Lunas --}}
                        <template x-if="state === 'paid'">
                            <div>
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                                    <i class="fa-solid fa-check text-2xl text-green-600"></i>
                                </div>
                                <h2 class="mt-4 text-xl font-bold text-green-700">Pembayaran Berhasil</h2>
                                <p class="mt-1 text-sm text-primary-gray">
                                    Terima kasih! Pesanan Anda akan segera kami proses.
                                </p>
                                <a href="{{ route('order-detail', ['id' => $order->id]) }}"
                                    class="mt-5 inline-block rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-secondary">
                                    Lihat Detail Pesanan
                                </a>
                            </div>
                        </template>

                        {{-- Gagal / kadaluarsa --}}
                        <template x-if="state === 'failed'">
                            <div>
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                                    <i class="fa-solid fa-xmark text-2xl text-red-600"></i>
                                </div>
                                <h2 class="mt-4 text-xl font-bold text-red-700">Pembayaran Tidak Selesai</h2>
                                <p class="mt-1 text-sm text-primary-gray">
                                    Batas waktu pembayaran telah habis atau transaksi dibatalkan.
                                    Stok pesanan sudah kami kembalikan.
                                </p>
                                <a href="{{ route('catalogue') }}"
                                    class="mt-5 inline-block rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-secondary">
                                    Pesan Ulang
                                </a>
                            </div>
                        </template>

                        <div class="mt-6 border-t border-gray-100 pt-5">
                            <p class="text-xs uppercase tracking-wide text-primary-gray">Total Pembayaran</p>
                            <p class="mt-1 text-3xl font-bold text-primary">
                                Rp {{ number_format((int) ($data['gross_amount'] ?? $order->total_price), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Detail metode pembayaran --}}
                <template x-if="state === 'pending'">
                    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        @if ($isQris)
                            <h2 class="text-base font-bold">Scan QRIS</h2>
                            <p class="mt-1 text-sm text-primary-gray">
                                Berlaku untuk semua aplikasi pembayaran berlogo QRIS.
                            </p>
                            <div class="mt-5 flex flex-col items-center">
                                <div class="rounded-xl border-2 border-dashed border-gray-200 p-4">
                                    <img src="{{ $url }}" alt="Kode QRIS pembayaran"
                                        class="h-56 w-56 object-contain">
                                </div>
                                <a href="{{ $url }}" download="qris-{{ $order->id }}.png"
                                    class="mt-4 inline-flex items-center gap-2 rounded-lg border border-primary px-4 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white">
                                    <i class="fa-solid fa-download"></i> Unduh Kode QR
                                </a>
                            </div>
                        @elseif ($vaNumber)
                            <h2 class="text-base font-bold">{{ $bank }} Virtual Account</h2>
                            <p class="mt-1 text-sm text-primary-gray">
                                Transfer tepat sesuai nominal agar pembayaran terverifikasi otomatis.
                            </p>

                            <div class="mt-5 rounded-xl bg-primary/5 p-5">
                                <p class="text-xs uppercase tracking-wide text-primary-gray">Nomor Virtual Account</p>
                                <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                                    <p class="font-mono text-2xl font-bold tracking-wider text-primary">
                                        {{ $vaNumber }}
                                    </p>
                                    <button type="button" @click="copy('{{ $vaNumber }}')"
                                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-secondary">
                                        <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                        <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                                    </button>
                                </div>
                                <p class="mt-3 text-xs text-primary-gray">
                                    a.n. {{ config('midtrans.recipient_name') }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                <i class="fa-solid fa-circle-info mr-2"></i>
                                Detail pembayaran belum tersedia. Muat ulang halaman ini beberapa saat lagi.
                            </div>
                        @endif

                        {{-- Panduan pembayaran --}}
                        <div class="mt-6" x-data="{ open: '{{ $isQris ? 'QRIS' : 'ATM' }}' }">
                            <h3 class="text-sm font-bold">Cara Pembayaran</h3>
                            <div class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-200">
                                @foreach (($isQris ? ['QRIS' => $qrisInstructions] : $instructions) as $channel => $steps)
                                    <div>
                                        <button type="button" @click="open = open === '{{ $channel }}' ? '' : '{{ $channel }}'"
                                            class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-medium transition hover:bg-gray-50">
                                            <span>{{ $channel }}</span>
                                            <i class="fa-solid fa-chevron-down text-xs text-primary-gray transition-transform"
                                                :class="open === '{{ $channel }}' && 'rotate-180'"></i>
                                        </button>
                                        <div x-show="open === '{{ $channel }}'" x-collapse>
                                            <ol class="list-decimal space-y-2 px-8 pb-4 text-sm text-primary-gray">
                                                @foreach ($steps as $step)
                                                    <li>{!! $step !!}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </template>
            </div>

            {{-- Ringkasan pesanan --}}
            <aside class="lg:col-span-1">
                <div class="sticky top-6 space-y-4">
                    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-base font-bold">Ringkasan Pesanan</h2>

                        <ul class="mt-4 space-y-3">
                            @foreach ($order->order_items as $item)
                                <li class="flex justify-between gap-3 text-sm">
                                    <span class="text-primary-gray">
                                        {{ $item->product_variant->product->name ?? 'Produk' }} —
                                        {{ $item->product_variant->name_type ?? '' }}
                                        <span class="text-xs">× {{ $item->quantity }}</span>
                                    </span>
                                    <span class="whitespace-nowrap font-medium">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        @php
                            $itemsTotal = $order->order_items->sum('subtotal');
                            $shipping = max(0, (int) $order->total_price - (int) $itemsTotal);
                            $shippingAddress = json_decode($order->shipping_address, true);
                        @endphp

                        <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm">
                            <div class="flex justify-between text-primary-gray">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($itemsTotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-primary-gray">
                                <span>Ongkos Kirim</span>
                                <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-100 pt-2 text-base font-bold">
                                <span>Total</span>
                                <span class="text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if (! empty($shippingAddress['address']))
                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <p class="text-xs uppercase tracking-wide text-primary-gray">Dikirim ke</p>
                                <p class="mt-1 text-sm">{{ $shippingAddress['address'] }}</p>
                            </div>
                        @endif
                    </section>

                    <template x-if="state === 'pending'">
                        <a href="{{ route('cancel.payment', ['id' => $order->id]) }}"
                            @click="return confirm('Batalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.')"
                            class="block rounded-lg border border-red-200 px-4 py-2.5 text-center text-sm font-semibold text-red-600 transition hover:bg-red-50">
                            Batalkan Pesanan
                        </a>
                    </template>

                    <p class="text-center text-xs text-primary-gray">
                        <i class="fa-solid fa-shield-halved mr-1"></i>
                        Pembayaran diproses aman oleh Midtrans
                    </p>
                </div>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            function paymentPage(config) {
                return {
                    expiresAt: config.expiresAt,
                    state: 'pending',
                    copied: false,
                    countdownUnits: [],
                    timer: null,
                    poller: null,

                    init() {
                        this.applyStatus(config.initialStatus);

                        if (this.state === 'pending') {
                            this.tick();
                            this.timer = setInterval(() => this.tick(), 1000);
                            // Status dipantau berkala agar halaman berubah
                            // sendiri begitu pembayaran masuk.
                            this.poller = setInterval(() => this.check(), 5000);
                        }
                    },

                    destroy() {
                        clearInterval(this.timer);
                        clearInterval(this.poller);
                    },

                    applyStatus(status) {
                        if (['settlement', 'capture'].includes(status)) {
                            this.state = 'paid';
                        } else if (['expire', 'cancel', 'deny', 'failure'].includes(status)) {
                            this.state = 'failed';
                        } else {
                            this.state = 'pending';
                        }

                        if (this.state !== 'pending') {
                            this.destroy();
                        }
                    },

                    async check() {
                        try {
                            const response = await fetch(config.statusUrl, {
                                headers: { 'Accept': 'application/json' },
                            });

                            if (!response.ok) return;

                            const data = await response.json();

                            if (data.is_paid) {
                                this.state = 'paid';
                                this.destroy();
                                // Beri jeda agar pelanggan sempat melihat konfirmasi.
                                setTimeout(() => window.location.href = config.orderUrl, 2500);
                            } else if (data.is_failed) {
                                this.state = 'failed';
                                this.destroy();
                            }
                        } catch (e) {
                            // Gangguan jaringan sesaat diabaikan; percobaan
                            // berikutnya akan berjalan otomatis.
                        }
                    },

                    tick() {
                        if (!this.expiresAt) {
                            this.countdownUnits = [];
                            return;
                        }

                        const remaining = this.expiresAt - Date.now();

                        if (remaining <= 0) {
                            this.countdownUnits = [
                                { label: 'Jam', value: '00' },
                                { label: 'Menit', value: '00' },
                                { label: 'Detik', value: '00' },
                            ];
                            // Waktu habis: konfirmasikan ke server sebelum
                            // menyatakan gagal, karena bisa saja pembayaran
                            // masuk tepat di detik terakhir.
                            this.check();
                            clearInterval(this.timer);
                            return;
                        }

                        const pad = (n) => String(n).padStart(2, '0');
                        const totalSeconds = Math.floor(remaining / 1000);

                        this.countdownUnits = [
                            { label: 'Jam', value: pad(Math.floor(totalSeconds / 3600)) },
                            { label: 'Menit', value: pad(Math.floor((totalSeconds % 3600) / 60)) },
                            { label: 'Detik', value: pad(totalSeconds % 60) },
                        ];
                    },

                    get statusLabel() {
                        return { pending: 'MENUNGGU PEMBAYARAN', paid: 'LUNAS', failed: 'GAGAL' }[this.state];
                    },

                    get badgeClass() {
                        return {
                            pending: 'bg-amber-100 text-amber-700',
                            paid: 'bg-green-100 text-green-700',
                            failed: 'bg-red-100 text-red-700',
                        }[this.state];
                    },

                    copy(text) {
                        const done = () => {
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        };

                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText(text).then(done);
                            return;
                        }

                        // Fallback untuk browser/koneksi tanpa Clipboard API.
                        const field = document.createElement('textarea');
                        field.value = text;
                        field.style.position = 'fixed';
                        field.style.opacity = '0';
                        document.body.appendChild(field);
                        field.select();
                        document.execCommand('copy');
                        document.body.removeChild(field);
                        done();
                    },
                };
            }
        </script>
    @endpush
</x-layout.customer>
