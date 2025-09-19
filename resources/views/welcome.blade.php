<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <title>Checkpoint</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="dark:bg-gray-900 text-gray-900 flex p-6 lg:p-4 items-center min-h-screen flex-col">
    <header class="w-full lg:max-w-7xl text-sm" x-data="{ mobileMenuOpen: false }">
        @if (Route::has('login'))
            <nav class="flex items-center justify-between mb-6">
                <!-- Logo -->
                <div class="flex items-center">
                    <x-application-logo class="block h-10 w-auto border-2" />
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <x-theme-toggle />
                    
                    <!-- Auth Links -->
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex px-5 py-2 dark:text-white border border-gray-500 hover:border-black text-black dark:border-gray-500 dark:hover:border-blue-400 rounded-md text-sm font-medium transition-colors duration-200">
                            Back to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center gap-2">
                    <x-theme-toggle />
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </nav>
            
            <!-- Mobile menu -->
            <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="md:hidden mb-4">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                            Back to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        @endif
        
        <!-- Main Navigation -->
        <nav class="mb-6">
            <!-- Desktop Navigation -->
            <div class="hidden md:flex flex-wrap justify-center gap-2 sm:gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Dashboard
                    </a>
                    <a href="{{ route('inventory.products.index') }}"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Products
                    </a>
                    <a href="{{ route('sales.customers.index') }}"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Customers
                    </a>
                    <a href="{{ route('sales.orders.index') }}"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Orders
                    </a>
                    <a href="{{ route('inventory.product_stocks.index') }}"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Stock
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Get Started
                    </a>
                    <a href="#features"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        Features
                    </a>
                    <a href="#about"
                        class="font-semibold rounded-lg px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        About
                    </a>
                @endauth
            </div>
            
            <!-- Mobile Navigation -->
            <div class="md:hidden">
                <div class="grid grid-cols-2 gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Dashboard
                        </a>
                        <a href="{{ route('inventory.products.index') }}"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Products
                        </a>
                        <a href="{{ route('sales.customers.index') }}"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Customers
                        </a>
                        <a href="{{ route('sales.orders.index') }}"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Orders
                        </a>
                        <a href="{{ route('inventory.product_stocks.index') }}"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 col-span-2">
                            Stock Management
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Get Started
                        </a>
                        <a href="#features"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            Features
                        </a>
                        <a href="#about"
                            class="font-semibold rounded-lg px-3 py-2 text-center text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 col-span-2">
                            About
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <div class="flex flex-row items-center lg:items-stretch w-full lg:max-w-7xl dark:text-white font-medium">
        <div class="mt-10">
            <h1 class="font-bold font-mono text-5xl mb-4">Welcome to Checkpoint Shoes <br> Inventory System</h1>
            <div class="flex lg:flex-row dark:text-white mt-2 text-3xl">
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repudiandae maxime illo porro natus
                    adipisci beatae quasi voluptas commodi, maiores mollitia error illum, ad optio laudantium possimus
                    sunt cupiditate tempora vel.</p>
            </div>

            {{-- Image Automatic Swipe --}}
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 pt-6 lg:max-w-3xl lg:gap-4 overflow-hidden rounded-lg bg-red-300 dark:bg-red-900 bg-opacity-50 dark:bg-opacity-50 mt-4">
                <div class="flex lg:flex-row m-2 border-2 border-blue-400 dark:dark:border-pink-800">
                    <img src="just some image.png" alt="">
                </div>
                <div class="flex lg:flex-row m-2 border-2 border-blue-400 dark:border-pink-800">
                    <img src="just some image.png" alt="">
                </div>
                <div class="flex lg:flex-row m-2 border-2 border-blue-400 dark:border-pink-800">
                    <img src="just some image.png" alt="">
                </div>
                <div class="flex lg:flex-row m-2 border-2 border-blue-400 dark:border-pink-800">
                    <img src="just some image.png" alt="">
                </div>
                <div class="flex lg:flex-row m-2 border-2 border-blue-400 dark:border-pink-800">
                    <img src="just some image.png" alt="">
                </div>
                <div class="flex lg:flex-row m-2 border-2 border-blue-400 dark:border-pink-800">
                    <img src="just some image.png" alt="">
                </div>
            </div>

            <div class="mt-4">
                {{-- Learn more button --}}
                <a href="#"
                    class="inline-flex items-center px-6 py-6 bg-black border dark:bg-blue-600 border-transparent rounded-md font-semibold text-base text-white  tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-offset-2 transition ease-in-out duration-150">
                    Learn more...
                </a>
            </div>
        </div>
    </div>
</body>

</html>
