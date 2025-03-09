<x-layout.customer>
    <div class="my-10 md:my-14">
        <h2 class="text-xl md:text-3xl font-extrabold font-poppins text-primary mb-4 md:mb-8">Company Information</h2>
        <div>
            <div class="space-y-8 md:space-y-14">
                <div class="flex flex-col md:flex-row items-center bg-transparent">
                    <div class="md:w-1/2">
                        <img src="/assets/images/image-4.png" alt="Visi"
                            class="rounded-lg w-full h-40 md:h-96 object-cover">
                    </div>
                    <div class="md:w-1/2 md:pl-6 mt-4 md:mt-0">
                        <h2 class="text-lg md:text-5xl font-bold font-poppins">Visi</h2>
                        <p class="text-primary-gray font-poppins mt-2 text-md md:text-2xl">
                            {{ $setting->visi }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row-reverse items-center bg-transparent">
                    <div class="md:w-1/2">
                        <img src="/assets/images/image-5.png" alt="Misi"
                            class="rounded-lg w-full h-40 md:h-96 object-cover">
                    </div>
                    <div class="md:w-1/2 md:pr-6 mt-4 md:mt-0">
                        <h2 class="text-lg md:text-5xl font-bold font-poppins">Misi</h2>
                        <p class="text-primary-gray font-poppins mt-2 text-md md:text-2xl">
                            {{ $setting->misi }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="my-10 md:my-36">
        <div class="flex flex-col md:grid md:grid-cols-12 gap-4">
            <div class="col-span-5">
                <h1 class="text-3xl md:text-5xl font-extrabold text-primary">
                    Layanan pada Kusuka Catering
                </h1>
                <x-button.custom
                    class="mt-4 md:mt-10 px-4 md:px-6 py-2 md:py-3 font-medium md:font-semibold text-sm md:text-lg rounded-md hover:bg-opacity-80"
                    href="#">Show Me Now</x-button.custom>
            </div>
            <div class="col-span-4">
                <div class="relative rounded-lg overflow-hidden shadow-md">
                    <img src="/assets/images/image-6.jpg" alt="Office Event" class="w-full h-40 md:h-72 object-cover">
                    <div
                        class="absolute bottom-0 left-0 w-full text-white text-xs md:text-lg font-poppins font-bold px-4 py-2">
                        Menyediakan makanan untuk perusahaan
                    </div>
                </div>
            </div>
            <div class="col-span-3">
                <div class="relative rounded-lg overflow-hidden shadow-md">
                    <img src="/assets/images/image-7.jpg" alt="Office Event" class="w-full h-40 md:h-72 object-cover">
                    <div
                        class="absolute bottom-0 left-0 w-full text-white text-xs md:text-lg font-poppins font-bold px-4 py-2">
                        Acara Pernikahan
                    </div>
                </div>
            </div>
            <div class="col-span-3">
                <div class="relative rounded-lg overflow-hidden shadow-md">
                    <img src="/assets/images/image-8.png" alt="Office Event" class="w-full h-40 md:h-72 object-cover">
                    <div
                        class="absolute bottom-0 left-0 w-full text-white text-xs md:text-lg font-poppins font-bold px-4 py-2">
                        Snack Box
                    </div>
                </div>
            </div>
            <div class="col-span-4">
                <div class="relative rounded-lg overflow-hidden shadow-md">
                    <img src="/assets/images/image-9.jpg" alt="Office Event" class="w-full h-40 md:h-72 object-cover">
                    <div
                        class="absolute bottom-0 left-0 w-full text-white text-xs md:text-lg font-poppins font-bold px-4 py-2">
                        Snack Box font-poppins font-bold px-4 py-2">
                        Hampers
                    </div>
                </div>
            </div>
            <div class="col-span-5">
                <div class="relative rounded-lg overflow-hidden shadow-md">
                    <img src="/assets/images/image-10.jpg" alt="Office Event" class="w-full h-40 md:h-72 object-cover">
                    <div
                        class="absolute bottom-0 left-0 w-full text-white text-xs md:text-lg font-poppins font-bold px-4 py-2">
                        Nasi Tumpeng
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="my-10 md:my-36">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Customer Rating
        </h2>
        <div class="flex flex-col md:grid md:grid-cols-3 gap-4 md:gap-8">
            @foreach ($rates as $rate)
                <x-card.rate name="{{ $rate->user->name }}" review="{{ $rate->message }}"
                    role="{{ $rate->user->role->name }}" rate="{{ $rate->rate }}" />
            @endforeach
        </div>

    </div>
</x-layout.customer>
