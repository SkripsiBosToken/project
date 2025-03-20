<x-layout.customer>
    <div class="my-10 md:my-14 px-4 md:px-8">
        <div class="mb-6">
            <h2 class="font-bold text-2xl text-primary mb-4">Detail Transaksi</h2>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-lg font-semibold text-gray-800">Pesanan {{ $order->status }}</p>
                    <span
                        class="bg-green-100 text-primary text-sm font-semibold px-3 py-1 rounded-full">{{ $order->status }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">No. Pesanan</p>
                        <p class="font-semibold text-gray-800">{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tanggal Pembelian</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($order->created_at)->locale('id_ID')->isoFormat('D MMMM YYYY, HH.mm.ss') }}
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-gray-600">Alamat Pengiriman</p>
                    <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                    <p class="text-gray-600">{{ json_decode($order->shipping_address, true)['address'] }}</p>
                    <p class="text-gray-600">{{ $user->phone_number }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="font-bold text-2xl text-primary mb-4">Detail Produk</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                @php
                    $subtotal = 0;
                @endphp

                @foreach ($order['order_items'] as $item)
                    <div class="flex items-start justify-between py-4 border-b border-gray-200">
                        <div class="flex items-start">
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $item['product_variant']['product']['name'] }} -
                                    {{ $item['product_variant']['name_type'] }}
                                </p>
                                <p class="text-gray-600 mt-1">
                                    Rp {{ number_format($item['product_variant']['price'], 0, ',', '.') }}
                                </p>
                                <p class="text-gray-600 mt-1">
                                    {{ $item['quantity'] }} x
                                </p>
                            </div>
                        </div>
                    </div>
                    @php
                        $subtotal += $item['product_variant']['price'] * $item['quantity'];
                    @endphp
                @endforeach

                {{-- Ongkir --}}
                <div class="flex items-start justify-between py-4 border-b border-gray-200">
                    <div class="flex items-start">
                        <div>
                            <p class="font-semibold text-gray-800">Shipping Payment</p>
                            <p class="text-gray-600 mt-1">
                                Rp {{ number_format($order['total_price'] - $subtotal, 0, ',', '.') }}
                            </p>
                            <p class="text-gray-600 mt-1">1 x</p>
                        </div>
                    </div>
                </div>
                {{-- Akhir --}}

            </div>
        </div>

        <div class="mb-6">
            <h2 class="font-bold text-2xl text-primary mb-4">Rincian Pembayaran</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Metode Pembayaran</p>
                        <p class="font-semibold text-gray-800">{{ strtoupper($transaction['va_numbers'][0]['bank']) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Nomor Virtual Akun</p>
                        <p class="font-semibold text-gray-800">{{ $transaction['va_numbers'][0]['va_number'] }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total</p>
                        <p class="font-bold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.customer>
