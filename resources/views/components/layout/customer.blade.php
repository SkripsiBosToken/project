<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Customer</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container mx-auto px-4 md:px-0">
        <x-header.customer />
        {{ $slot }}
    </div>

    <x-button.custom
        class="px-4 py-4 fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-white hover:text-primary transition-all" href="#">
        💬
    </x-button.custom>


    <x-footer.custom />
</body>

</html>
