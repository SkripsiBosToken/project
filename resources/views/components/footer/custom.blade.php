<div class="bg-primary-gray_light py-8 md:py-16 px-6 md:px-48 font-poppins">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-primary-gray">
        <div>
            <h2 class="text-lg font-semibold text-primary">KusukaCatering</h2>
        </div>

        <div>
            <h3 class="font-semibold text-primary">Legal</h3>
            <ul class="mt-2 space-y-1 text-sm">
                <li><a href="#" class="hover:underline">Privacy policy</a></li>
                <li><a href="#" class="hover:underline">Term and conditions</a></li>
                <li><a href="#" class="hover:underline">FAQ</a></li>
            </ul>
        </div>

        <div>
            <h3 class="font-semibold text-primary">Contact Us</h3>
            <div class="flex items-center space-x-3 mt-2">
                @foreach (json_decode($setting['social_media'], true) as $item)
                    <a href="{{$item['href']}}" class="text-primary hover:text-white">
                        <img src="{{ $item['logo'] }}"
                            class="w-4 md:w-8 h-4 md:h-8 object-contain">
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="font-semibold text-primary">Getting Touch</h3>
            <p class="mt-1 text-sm">{{ $setting['phone_number'] }}</p>
            <p class="mt-1 text-sm">{{ json_decode($setting['office_address'], true)['address'] }}</p>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/YOUR_KIT_CODE.js" crossorigin="anonymous"></script>
