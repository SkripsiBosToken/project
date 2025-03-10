<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Auth</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="h-screen flex font-poppins">
        {{ $slot }}
    </div>

    <x-button.custom
        class="px-4 py-4 fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-white hover:text-primary transition-all"
        href="https://wa.me/{{ preg_replace('/^0/', '62', $setting->phone_number) }}" target="_blank">
        💬
    </x-button.custom>
</body>

</html>
