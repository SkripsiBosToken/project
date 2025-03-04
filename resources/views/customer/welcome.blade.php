<x-layout.customer>
    <section class="flex flex-col md:flex-row items-center justify-between my-10 md:my-14">
        <div class="md:w-1/2">
            <div class="space-y-4 md:space-y-8">
                <h1 class="text-4xl md:text-5xl font-extrabold text-primary">
                    Your Favorite Food <br> Delivered <span class="text-primary-secondary">Hot & Fresh</span>
                </h1>
                <p class="text-primary-gray font-poppins text-lg md:text-2xl">
                    We provide you to take the best experience in travel and explore
                </p>
                <x-button.custom
                    class="px-4 md:px-6 py-2 md:py-3 font-medium md:font-semibold text-sm md:text-lg rounded-md hover:bg-opacity-80"
                    href="#">Show Me Now</x-button.custom>

            </div>
            <div class="flex py-4 md:pt-8 space-x-6 mt-6">
                <div class="text-center">
                    <i class="fas fa-suitcase text-blue-500 text-2xl"></i>
                    <p class="text-primary font-bold">80k+</p>
                    <p class="text-primary-gray font-medium">Traveler</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-camera text-blue-500 text-2xl"></i>
                    <p class="text-primary font-bold">45k+</p>
                    <p class="text-primary-gray font-medium">Treasure</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-map-marker-alt text-blue-500 text-2xl"></i>
                    <p class="text-primary font-bold">12k+</p>
                    <p class="text-primary-gray font-medium">CityHub</p>
                </div>
            </div>
        </div>

        <div class="md:w-1/2 flex items-center md:justify-end hidden md:flex">
            <div class="relative pr-2 md:pr-8">
                <img src="/assets/images/image-1.png" class="w-32 md:w-96 rounded-lg md:rounded-3xl shadow-lg"
                    alt="Food">
            </div>
            <div class="flex flex-col space-y-4">
                <img src="/assets/images/image-2.png" class="w-32 h-32 md:w-64 md:h-64 object-cover" alt="Fruits">
            </div>
        </div>

    </section>

    <div class="my-10 md:my-36">
        <x-banner.event />
    </div>

    <div class="my-10 md:my-36">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Our Special
            Catalogue</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 md:gap-x-14 gap-y-4 md:gap-y-0 ">

            <x-card.product />
            <x-card.product />
            <x-card.product />
            <x-card.product />

        </div>
    </div>

    <div class="my-10 md:my-36">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Our Portfolio</h2>
        <div class="flex flex-wrap justify-center items-center gap-4 md:gap-6  p-4">
            <img src="/assets/images/logo/pop.png" class="h-12 md:h-16 lg:h-20 object-contain" alt="Customer Logo">
            <img src="/assets/images/logo/prime.png" class="h-12 md:h-16 lg:h-20 object-contain" alt="Customer Logo">
            <img src="/assets/images/logo/fave.png" class="h-12 md:h-16 lg:h-20 object-contain" alt="Customer Logo">
            <img src="/assets/images/logo/neo.png" class="h-12 md:h-16 lg:h-20 object-contain" alt="Customer Logo">
        </div>
    </div>

    <div class="my-10 md:my-36">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Our Coverage</h2>
        <x-map.custom :coverageArea="[
            [-7.9215, 112.6001],
            [-7.9215, 112.6652],
            [-8.0002, 112.6652],
            [-8.0002, 112.6001],
            [-7.9215, 112.6001],
        ]" />
    </div>

</x-layout.customer>