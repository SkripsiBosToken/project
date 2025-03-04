<!-- Swiper CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<style>
    .swiper-button-next, .swiper-button-prev {
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

<div class="relative w-full mx-auto">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <a href="#"><img src="/assets/images/banner.png" class="w-full h-32 md:h-96 rounded-lg md:rounded-xl shadow-md object-cover"></a>
            </div>
            <div class="swiper-slide">
                <a href="#"><img src="/assets/images/banner.png" class="w-full h-32 md:h-96 rounded-lg md:rounded-xl shadow-md object-cover"></a>
            </div>
        </div>

        <div class="swiper-button-next text-primary"></div>
        <div class="swiper-button-prev text-primary"></div>

        <div class="swiper-pagination "></div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        new Swiper(".swiper-container", {
            loop: true,
            autoplay: { delay: 3000 },
            effect: "fade", // Gunakan efek fade agar hanya satu slide yang terlihat
            fadeEffect: { crossFade: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            pagination: { el: ".swiper-pagination", clickable: true },
        });
    });
</script>
