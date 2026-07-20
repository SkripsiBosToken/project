@php
    use App\Support\OrderStatus;
@endphp

<x-layout.customer title="Pesanan Saya | Kusuka Catering" :noindex="true">

    <div class="my-8 md:my-12" x-data="orderList({
        orders: {{ Illuminate\Support\Js::from($datas) }},
        statuses: {{ Illuminate\Support\Js::from(OrderStatus::all()) }},
    })">

        <h1 class="mb-6 text-2xl font-bold text-gray-900 md:text-3xl">Pesanan Saya</h1>

        {{-- Filter status --}}
        <div class="no-scrollbar mb-6 flex gap-2 overflow-x-auto pb-1">
            <button type="button" @click="setStatus('')"
                class="whitespace-nowrap rounded-lg border px-4 py-2 text-sm font-medium transition"
                :class="activeStatus === '' ? 'border-primary bg-primary text-white' : 'border-gray-300 bg-white text-gray-600 hover:border-primary hover:text-primary'">
                Semua
                <span class="ml-1 text-xs opacity-70" x-text="'(' + orders.length + ')'"></span>
            </button>

            <template x-for="status in statuses" :key="status">
                <button type="button" @click="setStatus(status)" x-show="countByStatus(status) > 0"
                    class="whitespace-nowrap rounded-lg border px-4 py-2 text-sm font-medium transition"
                    :class="activeStatus === status ? 'border-primary bg-primary text-white' : 'border-gray-300 bg-white text-gray-600 hover:border-primary hover:text-primary'">
                    <span x-text="status"></span>
                    <span class="ml-1 text-xs opacity-70" x-text="'(' + countByStatus(status) + ')'"></span>
                </button>
            </template>
        </div>

        {{-- Daftar pesanan --}}
        <div class="space-y-4">
            <template x-for="order in paginated" :key="order.id">
                <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card">

                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 bg-gray-50/70 px-5 py-3">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs text-gray-500" x-text="'#' + order.id.slice(0, 8).toUpperCase()"></span>
                            <span class="text-xs text-gray-400" x-text="formatDate(order.created_at)"></span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="badgeClass(order.status)">
                            <i class="fa-solid" :class="badgeIcon(order.status)"></i>
                            <span x-text="order.status"></span>
                        </span>
                    </div>

                    <div class="flex flex-col gap-4 p-5 sm:flex-row">
                        <img :src="thumbnailOf(order)" alt=""
                            class="h-20 w-20 flex-shrink-0 rounded-lg border border-gray-100 object-cover"
                            onerror="this.src='/placeholder.jpg'">

                        <div class="min-w-0 flex-1">
                            <h2 class="truncate font-semibold text-gray-900" x-text="titleOf(order)"></h2>
                            <p class="mt-0.5 text-sm text-gray-500" x-text="itemSummary(order)"></p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <a :href="'/order-detail/' + order.id"
                                    class="rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900">
                                    Lihat Detail
                                </a>

                                <template x-if="order.status === '{{ OrderStatus::UNPAID }}'">
                                    <a :href="'/payment/' + order.id"
                                        class="rounded-lg border border-primary px-3 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                                        <i class="fa-solid fa-credit-card mr-1"></i>Bayar Sekarang
                                    </a>
                                </template>

                                <template x-if="order.status === '{{ OrderStatus::UNPAID }}'">
                                    <a :href="'/cancel-payment/' + order.id"
                                        @click="return confirm('Batalkan pesanan ini?')"
                                        class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-primary-danger hover:text-primary-danger">
                                        Batalkan
                                    </a>
                                </template>

                                <template x-if="canReview(order)">
                                    <button type="button" @click="openReview(order.id)"
                                        class="rounded-lg border border-amber-300 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-50">
                                        <i class="fa-solid fa-star mr-1"></i>Tulis Ulasan
                                    </button>
                                </template>
                            </div>

                            {{-- Form ulasan --}}
                            <template x-if="reviewingId === order.id">
                                <form method="POST" action="/submit-review"
                                    class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    @csrf
                                    <input type="hidden" name="order_id" :value="order.id">
                                    <input type="hidden" name="rate" :value="rating">

                                    <label class="block text-sm font-semibold text-gray-700">Beri Nilai</label>
                                    <div class="mt-1.5 flex gap-1">
                                        <template x-for="star in 5" :key="star">
                                            <button type="button" @click="rating = star"
                                                class="text-2xl transition-transform hover:scale-110"
                                                :class="rating >= star ? 'text-amber-400' : 'text-gray-300'"
                                                :aria-label="'Beri nilai ' + star">
                                                <i class="fa-solid fa-star"></i>
                                            </button>
                                        </template>
                                    </div>

                                    <textarea name="message" rows="3" placeholder="Bagaimana pengalaman Anda?"
                                        class="mt-3 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"></textarea>

                                    <div class="mt-3 flex justify-end gap-2">
                                        <button type="button" @click="reviewingId = null"
                                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-100">
                                            Batal
                                        </button>
                                        <button type="submit" :disabled="rating === 0"
                                            class="rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
                                            Kirim <i class="fa-solid fa-paper-plane ml-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </template>
                        </div>

                        <div class="flex-shrink-0 border-t border-gray-100 pt-3 text-right sm:border-l sm:border-t-0 sm:pl-5 sm:pt-0">
                            <p class="text-xs text-gray-500">Total Belanja</p>
                            <p class="mt-0.5 text-lg font-bold text-primary" x-text="rupiah(order.total_price)"></p>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        {{-- Empty state --}}
        <div x-show="!paginated.length" x-cloak class="rounded-xl border border-dashed border-gray-300 bg-white">
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50">
                    <i class="fa-solid fa-receipt text-2xl text-primary-300"></i>
                </div>
                <h2 class="mt-4 text-base font-semibold text-gray-900"
                    x-text="activeStatus ? 'Tidak ada pesanan berstatus ' + activeStatus : 'Belum ada pesanan'"></h2>
                <p class="mt-1.5 max-w-sm text-sm text-gray-500">
                    Pesanan yang Anda buat akan tampil di halaman ini.
                </p>
                <a href="{{ route('catalogue') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-900">
                    Mulai Pesan <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Paginasi --}}
        <nav x-show="totalPages > 1" x-cloak class="mt-8 flex items-center justify-center gap-1" aria-label="Paginasi">
            <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>

            <template x-for="page in pageWindow" :key="page">
                <button type="button" @click="page !== '…' && goToPage(page)" :disabled="page === '…'"
                    class="h-9 min-w-[36px] rounded-lg border px-2 text-sm font-medium transition"
                    :class="page === currentPage ? 'border-primary bg-primary text-white' : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
                    x-text="page"></button>
            </template>

            <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </nav>
    </div>

    @push('scripts')
        <script>
            function orderList(config) {
                return {
                    // Urutkan sekali di awal, bukan di setiap render seperti sebelumnya.
                    orders: [...config.orders].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)),
                    statuses: config.statuses,
                    activeStatus: '',
                    currentPage: 1,
                    perPage: 5,
                    reviewingId: null,
                    rating: 0,

                    get filtered() {
                        return this.activeStatus
                            ? this.orders.filter(o => o.status === this.activeStatus)
                            : this.orders;
                    },

                    get totalPages() {
                        return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
                    },

                    get paginated() {
                        const page = Math.min(this.currentPage, this.totalPages);
                        return this.filtered.slice((page - 1) * this.perPage, page * this.perPage);
                    },

                    get pageWindow() {
                        const total = this.totalPages;
                        const current = Math.min(this.currentPage, total);

                        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

                        const pages = [1];
                        const start = Math.max(2, current - 1);
                        const end = Math.min(total - 1, current + 1);

                        if (start > 2) pages.push('…');
                        for (let i = start; i <= end; i++) pages.push(i);
                        if (end < total - 1) pages.push('…');
                        pages.push(total);

                        return pages;
                    },

                    countByStatus(status) {
                        return this.orders.filter(o => o.status === status).length;
                    },

                    setStatus(status) {
                        this.activeStatus = this.activeStatus === status ? '' : status;
                        this.currentPage = 1;
                    },

                    goToPage(page) {
                        if (page >= 1 && page <= this.totalPages) this.currentPage = page;
                    },

                    openReview(orderId) {
                        this.reviewingId = orderId;
                        this.rating = 0;
                    },

                    canReview(order) {
                        // rate diserialisasi sebagai array; kosong = belum diulas.
                        const rated = Array.isArray(order.rate) ? order.rate.length > 0 : !!order.rate;
                        return order.status === '{{ OrderStatus::COMPLETED }}' && !rated;
                    },

                    // Semua helper di bawah memakai optional chaining: pesanan
                    // yang penagihannya gagal bisa tidak punya order_items sama
                    // sekali, dan versi lama langsung error saat mengakses [0].
                    firstItem(order) {
                        return order.order_items?.[0] ?? null;
                    },

                    titleOf(order) {
                        const item = this.firstItem(order);
                        if (!item) return 'Pesanan';

                        const name = item.product_variant?.product?.name ?? 'Produk';
                        const type = item.product_variant?.name_type ?? '';
                        return type ? `${name} — ${type}` : name;
                    },

                    itemSummary(order) {
                        const count = order.order_items?.length ?? 0;
                        if (count === 0) return 'Tidak ada rincian item';
                        return count === 1 ? '1 jenis item' : `${count} jenis item`;
                    },

                    thumbnailOf(order) {
                        try {
                            return JSON.parse(this.firstItem(order).product_variant.photo)[0] ?? '/placeholder.jpg';
                        } catch (e) {
                            return '/placeholder.jpg';
                        }
                    },

                    badgeClass(status) {
                        return {
                            '{{ OrderStatus::UNPAID }}': 'bg-amber-100 text-amber-700',
                            '{{ OrderStatus::WAITING_CONFIRMATION }}': 'bg-blue-100 text-blue-700',
                            '{{ OrderStatus::PROCESSING }}': 'bg-blue-100 text-blue-700',
                            '{{ OrderStatus::SHIPPED }}': 'bg-blue-100 text-blue-700',
                            '{{ OrderStatus::COMPLETED }}': 'bg-green-100 text-green-700',
                            '{{ OrderStatus::FAILED }}': 'bg-red-100 text-red-700',
                            '{{ OrderStatus::REFUNDED }}': 'bg-gray-100 text-gray-700',
                        }[status] ?? 'bg-gray-100 text-gray-700';
                    },

                    badgeIcon(status) {
                        return {
                            '{{ OrderStatus::UNPAID }}': 'fa-clock',
                            '{{ OrderStatus::WAITING_CONFIRMATION }}': 'fa-hourglass-half',
                            '{{ OrderStatus::PROCESSING }}': 'fa-utensils',
                            '{{ OrderStatus::SHIPPED }}': 'fa-truck',
                            '{{ OrderStatus::COMPLETED }}': 'fa-circle-check',
                            '{{ OrderStatus::FAILED }}': 'fa-circle-xmark',
                            '{{ OrderStatus::REFUNDED }}': 'fa-rotate-left',
                        }[status] ?? 'fa-circle-info';
                    },

                    formatDate(value) {
                        return new Date(value).toLocaleDateString('id-ID', {
                            day: 'numeric', month: 'long', year: 'numeric',
                            hour: '2-digit', minute: '2-digit',
                        });
                    },

                    rupiah(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency', currency: 'IDR',
                            minimumFractionDigits: 0, maximumFractionDigits: 0,
                        }).format(value);
                    },
                };
            }
        </script>
    @endpush
</x-layout.customer>
