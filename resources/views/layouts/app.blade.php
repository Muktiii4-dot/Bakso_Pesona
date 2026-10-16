<!DOCTYPE html>
<<<<<<< HEAD
<html lang="id">
=======
<html lang="en">
>>>>>>> b6e12ae2d658d726b7aeb6d4674d9599852860b2
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bakso Pesona')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 antialiased">
    <x-navbar />
    <main>
        @yield('content')
    </main>
    <x-footer />
</body>