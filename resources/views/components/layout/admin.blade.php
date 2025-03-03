<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="container mx-auto px-4 md:px-0">
    {{ $slot }}
</body>

</html>
