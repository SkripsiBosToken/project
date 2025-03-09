<x-layout.customer>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="my-10 md:my-14" x-data="cartData()">
        <h1 class="text-2xl font-semibold mb-4">Carts / Wishlist</h1>

        <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
                @foreach ($cart['cart_items'] as $cart_item)
                    @php
                        $price = $cart_item['product_variant']['price'];
                        $stock = $cart_item['product_variant']['stock'];
                        $initialQty = $cart_item['qty'];
                    @endphp
                    <div class="mb-4 p-4 border rounded-lg shadow-md"
                        x-data="{ 
                            id: '{{ $cart_item['id'] }}',
                            name: '{{ $cart_item['product_variant']['product']['name'] }} - {{ $cart_item['product_variant']['name_type'] }}',
                            quantity: {{ $initialQty }},
                            price: {{ $price }},
                            subtotal: {{ $initialQty * $price }}
                        }"
                        x-init="$watch('quantity', value => { subtotal = value * price; $dispatch('cart-updated'); })">
                        
                        <div class="flex items-center">
                            <img src="{{ asset(json_decode($cart_item['product_variant']['photo'], true)[0]) }}"
                                alt="{{ $cart_item['product_variant']['product']['name'] }}"
                                class="w-16 h-16 object-cover rounded-lg mr-4">
                            <div>
                                <h2 class="font-semibold" x-text="name"></h2>
                                <p class="text-primary-gray">Rp {{ number_format($price, 0, ',', '.') }}</p>
                            </div>

                            <!-- Quantity Control -->
                            <div class="ml-auto flex items-center border border-gray-300 rounded-md">
                                <button @click="if (quantity > 1) quantity--"
                                    class="px-3 py-1 text-primary border-r border-primary-gray">−</button>
                                <span class="px-4 py-1 text-lg font-semibold" x-text="quantity"></span>
                                <button @click="if (quantity < {{ $stock }}) quantity++"
                                    class="px-3 py-1 text-primary border-l border-primary-gray">+</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="md:w-1/4 md:pl-4">
                <div class="p-4 border rounded-lg shadow-md">
                    <h2 class="font-semibold mb-4">Subtotal</h2>

                    @foreach ($cart['cart_items'] as $cart_item)
                        <div class="flex justify-between mb-2">
                            <span>{{ $cart_item['product_variant']['product']['name'] }} - {{ $cart_item['product_variant']['name_type'] }}</span>
                            <span>Rp <span x-text="({{ $cart_item['qty'] }} * {{ $cart_item['product_variant']['price'] }}).toLocaleString('id-ID')"></span></span>
                        </div>
                    @endforeach

                    <div class="flex justify-between mt-4 font-semibold">
                        <span>Total payment bill</span>
                        <span>Rp <span x-text="{{ collect($cart['cart_items'])->sum(fn($item) => $item['qty'] * $item['product_variant']['price']) }}.toLocaleString('id-ID')"></span></span>
                    </div>
                    <button class="w-full bg-primary text-white py-2 rounded-lg mt-4">Check Out</button>
                </div>
            </div>
        </div>
    </div>
</x-layout.customer>
