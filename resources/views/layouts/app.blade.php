<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Checkpoint') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('appleicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.css" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script type="module" src="https://unpkg.com/cally"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Sidebar Toggle Script with Alpine.js Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                open: false,
                toggle() {
                    this.open = !this.open;
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    Alpine.store('sidebar').toggle();
                });
            }
        });
    </script>
</head>


<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900"> {{--Control this!--}}
        @include('layouts.navigation')
        
        <!-- Shared Sidebar Navigation -->
        <x-sidebar-navigation />

        <!-- Main Content Wrapper with responsive margin -->
        <div class="pt-14 transition-all duration-100 ease-in-out" 
             x-data 
             :class="$store.sidebar.open ? 'lg:ml-72' : 'ml-0'">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-100 dark:bg-gray-800 shadow">
                    <div class="w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div>{{ $header }}</div>
                        <x-theme-toggle />
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
    
    <!-- Livewire Scripts -->
    @livewireScripts
</body>

</html>

@stack('scripts')
@stack('page-scripts')