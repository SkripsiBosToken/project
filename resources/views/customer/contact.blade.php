<x-layout.customer>
    <div class="my-10 md:my-14">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Our Office</h2>
        <x-map.custom :pinArea="[
            ['lat' => -7.9666, 'lng' => 112.6326, 'label' => 'Pusat Kota Malang'],
            ['lat' => -7.9829, 'lng' => 112.6214, 'label' => 'Universitas Brawijaya'],
        ]" />

    </div>

    <div class="my-10 md:my-36">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Social Media</h2>
        <div class="flex flex-col md:grid md:grid-cols-3 gap-4 md:gap-8">
            <a href="">
                <x-card.custom>
                    <img src="/assets/icons/instagram.png" alt="Instagram"
                        class="w-12 md:w-16 h-12 md:h-16 object-contain">
                    <p class="mt-2 text-sm md:text-lg text-primary-gray">Instagram</p>
                </x-card.custom>
            </a>

            <a href="">
                <x-card.custom>
                    <img src="/assets/icons/tiktok.png" alt="Tiktok" class="w-12 md:w-16 h-12 md:h-16 object-contain">
                    <p class="mt-2 text-sm md:text-lg  text-primary-gray">Tiktok</p>
                </x-card.custom>
            </a>

            <a href="">
                <x-card.custom>
                    <img src="/assets/icons/youtube.png" alt="Youtube"
                        class="w-12 md:w-16 h-12 md:h-16 object-contain">
                    <p class="mt-2 text-sm md:text-lg  text-primary-gray">Youtube</p>
                </x-card.custom>
            </a>
        </div>

    </div>
</x-layout.customer>
