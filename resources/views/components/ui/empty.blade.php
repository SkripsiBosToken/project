@props([
    'icon' => 'fa-inbox',
    'title' => 'Belum ada data',
    'message' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

{{-- Empty state: sebelumnya daftar kosong hanya menampilkan area putih
     tanpa penjelasan apa pun, sehingga terlihat seperti halaman rusak. --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50">
        <i class="fa-solid {{ $icon }} text-2xl text-primary-300"></i>
    </div>

    <h3 class="mt-4 text-base font-semibold text-gray-900">{{ $title }}</h3>

    @if ($message)
        <p class="mt-1.5 max-w-sm text-sm text-gray-500">{{ $message }}</p>
    @endif

    @if ($actionLabel && $actionHref)
        <x-ui.button href="{{ $actionHref }}" class="mt-5" icon="fa-arrow-right">{{ $actionLabel }}</x-ui.button>
    @endif

    {{ $slot }}
</div>
