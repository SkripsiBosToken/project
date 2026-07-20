@props([
    'status' => '',
    'size' => 'md',
])

@php
    use App\Support\OrderStatus;

    // Satu sumber kebenaran untuk warna & ikon status pesanan, menggantikan
    // pewarnaan ad-hoc yang berbeda-beda di tiap view.
    $map = [
        OrderStatus::UNPAID => ['bg-warning-100 text-warning-700', 'fa-clock'],
        OrderStatus::WAITING_CONFIRMATION => ['bg-info-100 text-info-700', 'fa-hourglass-half'],
        OrderStatus::PROCESSING => ['bg-info-100 text-info-700', 'fa-utensils'],
        OrderStatus::SHIPPED => ['bg-info-100 text-info-700', 'fa-truck'],
        OrderStatus::COMPLETED => ['bg-success-100 text-success-700', 'fa-circle-check'],
        OrderStatus::FAILED => ['bg-red-100 text-red-700', 'fa-circle-xmark'],
        OrderStatus::REFUNDED => ['bg-gray-100 text-gray-700', 'fa-rotate-left'],
    ];

    [$colour, $icon] = $map[$status] ?? ['bg-gray-100 text-gray-700', 'fa-circle-info'];

    $sizing = $size === 'sm' ? 'px-2 py-0.5 text-[11px] gap-1' : 'px-2.5 py-1 text-xs gap-1.5';
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center whitespace-nowrap rounded-full font-semibold $colour $sizing",
    ]) }}>
    <i class="fa-solid {{ $icon }}"></i>{{ $status ?: 'Tidak diketahui' }}
</span>
