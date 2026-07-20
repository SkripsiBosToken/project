{{--
    Menampilkan pesan flash (success/error) dan error validasi.
    Sebelumnya layout tidak punya tempat untuk ini, sehingga semua
    redirect()->with('error', ...) hilang tanpa jejak di layar.
--}}

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-4 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
        <i class="fa-solid fa-circle-check mt-0.5 text-green-600"></i>
        <p class="flex-1 text-sm">{{ session('success') }}</p>
        <button type="button" @click="show = false" class="text-green-600 hover:text-green-800" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
        <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-600"></i>
        <p class="flex-1 text-sm">{{ session('error') }}</p>
        <button type="button" @click="show = false" class="text-red-600 hover:text-red-800" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

{{-- $errors hanya di-share oleh middleware group "web"; isset() menjaga
     komponen ini tetap aman bila layout dirender di luar siklus request
     (mis. saat DomPDF merender view lewat Pdf::loadView). --}}
@if (isset($errors) && $errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
        <div class="flex items-center gap-2 font-semibold">
            <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
            <span class="text-sm">Periksa kembali isian berikut:</span>
        </div>
        <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
