<x-layout.customer>
    <div class="my-10 md:my-14" x-data="{ cartItems: {{ json_encode($cart['cart_items']) }} }">
        <h1 class="text-2xl font-semibold mb-4">Carts / Wishlist</h1>

        <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
                <template x-for="item in cartItems" x-bind:key="item.id">
                    <div class="mb-4 p-4 border rounded-lg shadow-md">
                        <div class="flex items-center">
                            <img x-bind:src="JSON.parse(item.product_variant.photo)[0]"
                                x-bind:alt="item.product_variant.product.name"
                                class="w-16 h-16 object-cover rounded-lg mr-4">
                            <div>
                                <h2 class="font-semibold"
                                    x-text="item.product_variant.product.name + ' - ' + item.product_variant.name_type">
                                </h2>
                                <p class="text-primary-gray"
                                    x-text="'Rp. ' + (item.product_variant.price).toLocaleString('id-ID')"></p>
                            </div>

                            <div class="ml-auto flex items-center gap-x-2">
                                <x-button.custom class="p-0.5 text-sm md:text-sm bg-primary-danger"
                                    x-bind:href="'/cart/delete/' + item.id">X</x-button.custom>
                                <div class="border border-gray-300 rounded-md">
                                    <button @click="if (item.qty > 1) item.qty--"
                                        class="px-3 py-1 text-primary border-r border-primary-gray">−</button>
                                    <span class="px-4 py-1 text-lg font-semibold" x-text="item.qty"></span>
                                    <button @click="if (item.qty < item.product_variant.stock) item.qty++"
                                        class="px-3 py-1 text-primary border-l border-primary-gray">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="md:w-1/4 md:pl-4">
                <div class="p-4 border rounded-lg shadow-md">
                    <h2 class="font-semibold mb-4">Subtotal</h2>

                    <template x-for="item in cartItems" x-bind:key="item.id">
                        <div class="flex justify-between mb-2">
                            <span
                                x-text="item.product_variant.product.name + ' - ' + item.product_variant.name_type"></span>
                            <span>Rp <span
                                    x-text="(item.product_variant.price * item.qty).toLocaleString('id-ID')"></span></span>
                        </div>
                    </template>

                    <x-form.custom action="{{ route('checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="buy-cart">
                        <input type="hidden" name="items" x-bind:value="getItems(cartItems)">
                        <div class="flex justify-between mt-4 font-semibold">
                            <span>Total payment bill</span>
                            <span>Rp <span x-text="(getSubtotal(cartItems)).toLocaleString('id-ID')"></span></span>
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg mt-4">Check
                            Out</button>
                    </x-form.custom>
                </div>
            </div>
        </div>
    </div>
    <script>
        function getSubtotal(items) {
            return items.map(item => item.product_variant.price * item.qty).reduce((previous, after) => previous + after,
                0);
        }

        function getItems(items) {
            return JSON.stringify(items.map(item => {
                return {
                    product_variant_id: item.product_variant.id,
                    qty: item.qty
                }
            }))
        }
    </script>
</x-layout.customer>
