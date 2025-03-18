<x-layout.customer>
    <div class="my-10 md:my-14" x-data="{ items: {{ json_encode($datas) }}, shippingCost: {{ json_encode($shippingCost) }} }">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">

                <h2 class="font-bold text-lg">Alamat Pengiriman</h2>
                <div class="bg-white p-4 border rounded-lg mt-2 flex justify-between items-center shadow-md">
                    <div>
                        <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-primary-gray">{{ json_decode(Auth::user()->address, true)['address'] }}
                        </p>
                        <p class="text-sm text-primary-gray">{{ Auth::user()->phone_number }}</p>
                    </div>
                </div>

                <div class=" mt-4">
                    <h2 class="font-bold text-lg">List Pesanan</h2>
                    <template x-for="item in items" x-bind:key="item.product.id">
                        <div class="bg-white p-4 rounded-lg shadow-md divide-y divide-gray-200 mt-4">
                            <div class="flex items-center py-3">
                                <img x-bind:src="JSON.parse(item.product.photo)[0]" class="w-16 h-16 rounded-md"
                                    alt="Makanan">
                                <div class="ml-4 flex-grow">
                                    <p x-text="item.product.product.name + ' - ' + item.product.name_type"
                                        class="font-semibold">Nama Makanan</p>
                                    <p x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(item.product.price)"
                                        class="text-sm text-primary-gray">Harga</p>
                                </div>
                                <div class="flex items-center">
                                    <p x-text="item.qty + ' x'"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="md:w-1/4 md:pl-4">
                <x-form.custom action="{{ route('checkout.payment') }}" method="POST">
                    <input name="shipping_address" class="hidden" type="text"
                        value="{{ json_decode(Auth::user()->address, true)['address'] }}">
                    <input name="item_details" class="hidden" type="text" x-bind:value="getItems(items)">
                    <input name="type" class="hidden" type="text" value={{ $type }}>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="font-bold text-lg">Metode Pembayaran</h2>
                        <div class="mt-2 space-y-2">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="payment_type" value="bni" checked>
                                <span>BNI Virtual Account</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="payment_type" value="bri">
                                <span>BRI Virtual Account</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="payment_type" value="bca">
                                <span>BCA Virtual Account</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow-md mt-4">
                        <h2 class="font-bold text-lg">Subtotal</h2>
                        <div class="mt-2 space-y-1">
                            <template x-for="item in items" x-bind:key="item.product.id">
                                <p
                                    x-text="item.product.product.name + ' - ' + item.product.name_type + ' (' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(item.product.price * item.qty) +')'">
                                </p>
                            </template>
                            <p
                                x-text="'Shipping Cost - (' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(shippingCost) + ')'">
                            </p>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total payment bill</span>
                            <span
                                x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(getSubtotal(items, shippingCost))"></span>
                        </div>
                        <button type="submit" class="w-full mt-4 bg-primary text-white py-2 rounded-lg">Check
                            Out</button>
                    </div>
                </x-form.custom>
            </div>
        </div>
    </div>
    <script>
        function getSubtotal(items, shippingCost) {
            return items.map(item => item.product.price * item.qty).reduce((previous, after) => previous + after,
                0) + shippingCost;
        }

        function getItems(items) {
            return JSON.stringify(items.map(item => {
                return {
                    id: item.product.id,
                    quantity: item.qty,
                }
            }))
        }
    </script>
</x-layout.customer>
