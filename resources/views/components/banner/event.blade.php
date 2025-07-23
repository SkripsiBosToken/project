@php
    $setting = app(\App\Http\Controllers\GuestController::class)->setting();
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<style>
    .swiper-button-next,
    .swiper-button-prev {
        color: #588157 !important;
    }

    .swiper-pagination-bullet {
        background-color: #588157 !important;
    }

    .swiper-pagination-bullet-active {
        background-color: #6F6F6F !important;
        opacity: 1 !important;
    }
</style>

@php
    $banners = $setting->promo_event ? json_decode($setting->promo_event, true) : [];
@endphp

@if (!empty($banners))
    <div class="relative w-full mx-auto" x-data x-init="
        new Swiper('.swiper-container', {
            loop: true,
            autoplay: { delay: 3000 },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            pagination: { el: '.swiper-pagination', clickable: true },
        });
    ">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach ($banners as $banner)
                    <div class="swiper-slide">
                        <a href="{{ $banner['href'] }}" href="">
                            <img src="{{ asset($banner['banner']) }}" 
                                class="w-full h-32 md:h-96 rounded-lg md:rounded-xl shadow-md object-cover">
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="swiper-button-next text-primary"></div>
            <div class="swiper-button-prev text-primary"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
@endif
