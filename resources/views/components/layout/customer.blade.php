@php
    $setting = app(\App\Http\Controllers\GuestController::class)->setting();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Catering Malang | Pesan Makanan Lezat, Hangat & Fresh</title>
    <link rel="icon" type="image/png" href="{{ $setting['logo'] }}">
    
    <meta name="description"
        content="Pesan catering di Malang dengan makanan berkualitas, fresh, dan siap diantar ke lokasi Anda. Cek menu spesial kami!">
    <meta name="keywords" content="Catering Malang, pesan catering Malang, catering murah Malang">
    <meta name="author" content="Kusuka Catering">

    <script src="https://kit.fontawesome.com/a47e0565cc.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container mx-auto px-4 md:px-0 font-poppins">
        <x-header.customer />
        {{ $slot }}
    </div>

    <x-button.custom
        class="w-16 h-16 fixed bottom-8 right-8 bg-primary text-white rounded-full shadow-lg flex items-center justify-center text-2xl hover:bg-white hover:text-primary transition-all"
        href="https://wa.me/{{ preg_replace('/^0/', '62', $setting->phone_number) }}" target="_blank">
        <i class="fa-brands fa-whatsapp"></i>
    </x-button.custom>

    <x-footer.custom />
</body>

</html>
