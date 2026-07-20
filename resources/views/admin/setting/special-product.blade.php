{{-- Empat blok yang sebelumnya di-copy-paste diganti satu loop, sehingga
     slot kosong tidak lagi menyebabkan "Undefined array key". --}}
<x-layout.admin-v2 title="Produk Spesial" subtitle="Pilih menu yang tampil di bagian unggulan halaman depan">

    <form action="{{ route('system.special-product.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($specialProduct as $index => $slot)
                @php
                    $slotNumber = $index + 1;
                    $fieldName = 'product_0' . $slotNumber;
                    $currentId = $slot['product_id'];
                    $currentProduct = $slot['product'];
                @endphp

                <x-ui.card title="Produk Spesial {{ $slotNumber }}" icon="fa-star">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Produk Saat Ini</label>

                            @if ($currentProduct)
                                <div class="flex items-center gap-3 rounded-lg bg-success-50 px-3 py-2.5">
                                    <i class="fa-solid fa-circle-check text-success-600"></i>
                                    <span class="truncate text-sm font-medium text-gray-900">
                                        {{ $currentProduct['name'] }}
                                    </span>
                                </div>
                            @elseif ($currentId)
                                {{-- Id tersimpan tapi produknya sudah dihapus. --}}
                                <div class="flex items-center gap-3 rounded-lg bg-warning-50 px-3 py-2.5">
                                    <i class="fa-solid fa-triangle-exclamation text-warning-600"></i>
                                    <span class="text-sm text-warning-700">
                                        Produk tidak ditemukan atau sudah dihapus
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2.5">
                                    <i class="fa-solid fa-circle-minus text-gray-400"></i>
                                    <span class="text-sm text-gray-500">Slot ini masih kosong</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="{{ $fieldName }}" class="mb-1.5 block text-sm font-medium text-gray-700">
                                Pilih Produk
                            </label>
                            <select name="{{ $fieldName }}" id="{{ $fieldName }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="">— Kosongkan slot ini —</option>

                                @foreach ($products as $product)
                                    <option value="{{ $product['id'] }}" @selected($currentId === $product['id'])>
                                        {{ $product['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-6 flex justify-end">
            <x-ui.button type="submit" icon="fa-floppy-disk">Simpan Perubahan</x-ui.button>
        </div>
    </form>

</x-layout.admin-v2>
