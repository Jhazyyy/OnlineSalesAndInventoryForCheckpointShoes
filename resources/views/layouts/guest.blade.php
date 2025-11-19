<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Checkpoint') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    
    <div class="flex flex-col min-h-screen bg-cover bg-center justify-center items-center px-4 sm:px-6 lg:px-8"
        style="background-image: url('{{ asset('storage/background.jpg') }}');">
        <div class="w-full max-w-md mx-auto px-4 py-8 sm:px-6 sm:py-10 md:px-8 md:py-12 bg-gray-300/95 dark:bg-gray-800/95 shadow-lg overflow-hidden rounded-lg backdrop-blur-sm">
            <div class="flex justify-center mb-6 sm:mb-8">
                <a href="/">
                    <img src="{{ asset('https://scontent.fmnl9-1.fna.fbcdn.net/v/t39.30808-6/476127608_1181950400600927_1924746276343773299_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=kgd2FGk2FOMQ7kNvwFuEohz&_nc_oc=Adn4YpSuCROCfqxjYwjrv-uGIRIGNTcHyu-pvS9B5y0L69gMgh4VMabnAiK4iHMAUMU&_nc_zt=23&_nc_ht=scontent.fmnl9-1.fna&_nc_gid=dQkLvtzByf6JLJmKh-ayaQ&oh=00_AfjQhu2W7QJkus5FHIJAnjD_XrbB00ueX6lMLDfGMBxxkQ&oe=69225ED6') }}" alt="Checkpoint Logo" class="max-w-32 max-h-32 object-cover rounded-full" />
                </a>
            </div>
            <div>
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>