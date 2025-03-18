<x-layout.customer>
    <div class="my-10 md:my-14">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Our Office</h2>

        @php
            $officeAddress = [];
            // $data = $setting->office_address ? json_decode($setting->office_address, true) : null;
            $address = json_decode($setting->office_address, true);
            $data = [
                'lat' => (float) $address['latitude'],
                'lng' => (float) $address['longitude'],
                'label' => 'Kantor',
            ];
            array_push($officeAddress, $data);
        @endphp

        @if (is_array($officeAddress))
            <x-map.custom :pinArea="$officeAddress" />
        @else
            <p>Data lokasi kantor tidak valid atau tidak tersedia.</p>
        @endif

    </div>

    <div class="my-10 md:my-36">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Social Media</h2>
        <div class="flex flex-col md:grid md:grid-cols-3 gap-4 md:gap-8">
            @foreach ($social_medias as $social_media)
                <a href="{{ $social_media['href'] }}" target="_blank">
                    <x-card.custom>
                        <img src="{{ $social_media['logo'] }}" alt="Instagram"
                            class="w-12 md:w-16 h-12 md:h-16 object-contain">
                        <p class="mt-2 text-sm md:text-lg text-primary-gray">{{ $social_media['name'] }}</p>
                    </x-card.custom>
                </a>
            @endforeach
        </div>

    </div>
</x-layout.customer>
