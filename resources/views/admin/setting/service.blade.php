@php
    $sizeOptions = [
        'otomatis' => 'Otomatis (mosaik acak)',
        'kecil' => 'Kecil — 1/3 baris',
        'sedang' => 'Sedang — 1/2 baris',
        'besar' => 'Besar — 2/3 baris',
        'penuh' => 'Penuh — 1 baris',
    ];
@endphp

<x-layout.admin-v2 title="Layanan Kami" subtitle="Kelola daftar layanan yang tampil di halaman Tentang Kami">

    <form method="POST" action="{{ route('system.service.update') }}" enctype="multipart/form-data"
        x-data="serviceManager()" @submit="submitting = true">
        @csrf
        @method('PUT')

        {{-- Indeks layanan yang dihapus dikirim sebagai JSON. --}}
        <input type="hidden" name="deleted_service_indexes" :value="JSON.stringify(deleted)">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-500">
                Foto sebaiknya berorientasi lanskap (mis. 1200×800px), maksimal 4&nbsp;MB.
            </p>
            <x-ui.button type="button" @click="addService()" variant="outline" size="sm" icon="fa-plus">
                Tambah Layanan
            </x-ui.button>
        </div>

        {{-- Layanan yang sudah ada --}}
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($services as $index => $service)
                <div x-show="!deleted.includes({{ $index }})" x-cloak
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card">

                    <div class="relative aspect-[3/2] bg-gray-100">
                        <img src="{{ $service['image'] ?? '' }}" alt="{{ $service['label'] ?? '' }}"
                            class="h-full w-full object-cover"
                            x-ref="preview{{ $index }}" onerror="this.src='/placeholder.jpg'">

                        <label
                            class="absolute inset-0 flex cursor-pointer items-center justify-center bg-black/0 opacity-0 transition hover:bg-black/50 hover:opacity-100">
                            <span class="rounded-lg bg-white px-3 py-2 text-xs font-semibold text-gray-900">
                                <i class="fa-solid fa-camera mr-1.5"></i>Ganti Foto
                            </span>
                            {{-- Preview langsung: admin bisa melihat hasilnya
                                 sebelum menyimpan. --}}
                            <input type="file" name="services[{{ $index }}][image]" accept="image/*" class="hidden"
                                @change="previewImage($event, $refs.preview{{ $index }})">
                        </label>
                    </div>

                    <div class="space-y-3 p-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Judul Layanan</label>
                            <input type="text" name="services[{{ $index }}][label]"
                                value="{{ old("services.$index.label", $service['label'] ?? '') }}" required maxlength="80"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error("services.$index.label")
                                <p class="mt-1.5 text-xs text-primary-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Ukuran Kartu</label>
                            <select name="services[{{ $index }}][size]"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                @foreach ($sizeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($service['size'] ?? 'kecil') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error("services.$index.image")
                                <p class="mt-1.5 text-xs text-primary-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="button" @click="removeService({{ $index }})"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-primary-danger transition hover:bg-red-50">
                            <i class="fa-solid fa-trash-can"></i>Hapus Layanan
                        </button>
                    </div>
                </div>
            @endforeach

            {{-- Layanan baru --}}
            <template x-for="(item, i) in newServices" :key="item.key">
                <div class="overflow-hidden rounded-xl border-2 border-dashed border-primary-200 bg-white shadow-card">
                    <div class="relative aspect-[3/2] bg-primary-50">
                        <img :src="item.preview" alt="" class="h-full w-full object-cover"
                            x-show="item.preview">

                        <label
                            class="absolute inset-0 flex cursor-pointer flex-col items-center justify-center gap-2 text-primary transition hover:bg-primary-50/70">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl" x-show="!item.preview"></i>
                            <span class="rounded-lg bg-white px-3 py-2 text-xs font-semibold shadow"
                                x-text="item.preview ? 'Ganti Foto' : 'Pilih Foto'"></span>
                            <input type="file" :name="`new_services[${i}][image]`" accept="image/*" class="hidden"
                                required @change="previewNew($event, i)">
                        </label>
                    </div>

                    <div class="space-y-3 p-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Judul Layanan</label>
                            <input type="text" :name="`new_services[${i}][label]`" x-model="item.label" required
                                maxlength="80" placeholder="mis. Prasmanan Ulang Tahun"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Ukuran Kartu</label>
                            <select :name="`new_services[${i}][size]`" x-model="item.size"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                @foreach ($sizeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" @click="newServices.splice(i, 1)"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                            <i class="fa-solid fa-xmark"></i>Batalkan
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Kosong --}}
        <div x-show="visibleCount === 0 && newServices.length === 0" x-cloak
            class="rounded-xl border border-dashed border-gray-300 bg-white">
            <x-ui.empty icon="fa-concierge-bell" title="Belum ada layanan"
                message="Tambahkan layanan agar tampil di halaman Tentang Kami." />
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-500">
                <span x-text="visibleCount + newServices.length"></span> layanan akan ditampilkan.
            </p>
            <div class="flex gap-2">
                <x-ui.button href="{{ route('about') }}" target="_blank" variant="muted" size="sm"
                    icon="fa-arrow-up-right-from-square">
                    Lihat Halaman
                </x-ui.button>
                <button type="submit" :disabled="submitting"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
                    <i class="fa-solid" :class="submitting ? 'fa-circle-notch fa-spin' : 'fa-floppy-disk'"></i>
                    <span x-text="submitting ? 'Menyimpan…' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            function serviceManager() {
                return {
                    deleted: [],
                    newServices: [],
                    submitting: false,
                    total: {{ count($services) }},

                    get visibleCount() {
                        return this.total - this.deleted.length;
                    },

                    addService() {
                        // `key` unik menjaga identitas baris saat ada yang
                        // dihapus, supaya input file tidak tertukar.
                        this.newServices.push({
                            key: Date.now() + Math.random(),
                            label: '',
                            size: 'kecil',
                            preview: null,
                        });
                    },

                    removeService(index) {
                        if (!confirm('Hapus layanan ini? Foto lama juga akan dihapus saat disimpan.')) return;
                        this.deleted.push(index);
                    },

                    previewImage(event, img) {
                        const file = event.target.files[0];
                        if (file) img.src = URL.createObjectURL(file);
                    },

                    previewNew(event, index) {
                        const file = event.target.files[0];
                        if (file) this.newServices[index].preview = URL.createObjectURL(file);
                    },
                };
            }
        </script>
    @endpush

</x-layout.admin-v2>
