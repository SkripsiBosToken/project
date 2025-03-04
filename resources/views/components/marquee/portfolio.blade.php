@props(['images' => []])

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<h2 class="text-center text-green-800 font-bold text-lg md:text-2xl mb-4">Our Customer</h2>

<div class="swiper mySwiper w-full overflow-hidden">
    <div class="swiper-wrapper flex items-center">
        @foreach ($images as $image)
            <div class="swiper-slide w-auto flex items-center !ml-0 !mr-0">
                <img src="{{ $image }}" class="h-12 md:h-16 object-contain" alt="Customer Logo">
            </div>
        @endforeach
    </div>
</div>

<!-- Swiper JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: "auto",
        spaceBetween: 0, // Hapus jarak antar slide
        loop: true,
        autoplay: {
            delay: 0, // Supaya autoplay berjalan terus tanpa jeda
            disableOnInteraction: false,
        },
        speed: 3000, // Supaya smooth
        freeMode: true,
        allowTouchMove: false, // Hindari user swipe manual
    });
});
</script>
