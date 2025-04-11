<x-layout.customer>
    <div class="my-10 md:my-14 font-poppins px-4 md:px-8" x-data="{
        items: {{ json_encode($datas) }},
        activeStatus: '',
        setStatus(status) {
            this.activeStatus = this.activeStatus === status ? '' : status;
        }
    }">

        <div class="flex flex-wrap gap-2 md:gap-4 mb-4">
            <h1 class="text-2xl font-bold w-full md:w-auto">Status</h1>

            <template
                x-for="status in ['Belum Dibayar', 'Menunggu Konfirmasi', 'Diproses', 'Dikirim', 'Berhasil', 'Gagal']">
                <button @click="setStatus(status)"
                    x-bind:class="activeStatus === status ? 'border border-primary font-bold text-lg text-primary' :
                        'text-primary-gray'"
                    class="px-4 py-2 rounded-md text-sm md:text-base border">
                    <span x-text="status"></span>
                </button>
            </template>
        </div>

        <div class="space-y-4">
            <template x-for="item in items.filter(i => activeStatus === '' || i.status === activeStatus)">
                <div class="border p-4 rounded-md flex flex-col md:flex-row gap-4 items-center">
                    <img x-bind:src="JSON.parse(item.order_items[0].product_variant.photo)[0]" alt="Makanan"
                        class="w-16 h-16 rounded-md flex-shrink-0">

                    <div class="flex-1 text-center md:text-left">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs md:text-sm font-normal md:font-semibold" x-text="item.status"></span>
                            <p class="text-sm text-primary-gray"
                                x-text="'Waktu Pemesanan : ' + formatDateTime(item.created_at)"></p>
                        </div>
                        <h2 class="font-bold text-lg"
                            x-text='item.order_items[0].product_variant.product.name + " - " + item.order_items[0].product_variant.name_type'>
                        </h2>
                        <p class="text-gray-500 text-sm"
                            x-text="formatPrice(item.order_items[0].product_variant.price)"></p>
                        <a x-bind:href="'/order-detail/' + item.id">
                            <button class="bg-primary text-white px-2 py-1 rounded-md">Lihat Detail</button>
                        </a>
                        <template x-if="item.status === 'Belum Dibayar'">
                            <a x-bind:href="'/payment/' + item.id">
                                <button class="border border-primary text-primary px-2 py-1 rounded-md">Lihat Cara
                                    Bayar</button>
                            </a>
                        </template>
                        <template x-if="item.status === 'Belum Dibayar'">
                            <a x-bind:href="'/cancel-payment/' + item.id">
                                <button class="border border-primary text-primary px-2 py-1 rounded-md">Batalkan
                                    Pesanan</button>
                            </a>
                        </template>
                    </div>

                    <div class="text-center md:ml-auto mr-auto">
                        <p class="text-sm text-gray-500">Total Belanja</p>
                        <p class="font-bold" x-text="formatPrice(item.total_price)"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-layout.customer>

<script>
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        const dateOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        const timeOptions = {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: false
        };
        return date.toLocaleDateString('id-ID', dateOptions) + ', ' + date.toLocaleTimeString('id-ID', timeOptions);
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(price);
    }
</script>
