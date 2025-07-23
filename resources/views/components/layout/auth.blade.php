@php
    $setting = app(\App\Http\Controllers\GuestController::class)->setting();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Auth</title>
    <link rel="icon" type="image/png" href="{{ $setting['logo'] }}">
    
    <script src="https://kit.fontawesome.com/a47e0565cc.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="h-screen font-poppins grid md:grid-cols-2">
        <div
            class="bg-gradient-to-r from-primary to-primary-secondary flex flex-col justify-center relative min-h-screen md:min-h-full md:block hidden">
            <div class="mx-10 my-10 md:my-0 md:flex md:items-center md:h-full">
                <div class="md:w-4/5">
                    <p class="text-white text-3xl md:text-4xl font-bold mb-4">
                        Your Favorite Food
                    </p>
                    <p class="text-white text-3xl md:text-4xl font-bold mb-4">
                        Delivered Fresh Catering & Snack
                    </p>
                    <p class="text-white text-xl">
                        The most favorite catering in the Malang area
                    </p>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#ffffff" fill-opacity="0.1"
                        d="M0,160L48,176C96,192,192,224,288,208C384,192,480,128,576,117.3C672,107,768,149,864,170.7C960,192,1056,192,1152,176C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                    </path>
                </svg>
            </div>
        </div>
        {{ $slot }}
    </div>

    <x-button.custom
        class="w-16 h-16 fixed bottom-8 right-8 bg-primary text-white rounded-full shadow-lg flex items-center justify-center text-2xl hover:bg-white hover:text-primary transition-all"
        href="https://wa.me/{{ preg_replace('/^0/', '62', $setting->phone_number) }}" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
    </x-button.custom>
</body>

</html>
