{{-- Header --}}
<nav x-data="{ open: false }"
    class="fixed top-0 left-0 right-0 z-50 bg-gray-200 antialiased dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 shadow-sm">

    <!-- Primary Navigation Menu -->
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex justify-items-stretch">
                <!-- Sidebar Toggle Button -->
                <div class="flex content-start mr-2">
                    <button id="sidebar-toggle"
                        class=" rounded-md text-gray-400 dark:text-gray-100 hover:text-gray-500 dark:hover:text-blue-400  focus:outline-none dark:focus:text-gray-400 transition duration-150 ease-in-out"
                        title="Toggle Navigation">
                        <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div class="shrink-0 flex items-center pl-2">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="h-12 rounded-lg border-2" />
                    </a>
                </div>

                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}}
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-1">

                <!-- Theme Toggle -->
                <x-theme-toggle />

                {{-- <!-- User Management Settings -->
                <a href="{{ route('user-management.index') }}"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                    title="User Management">
                </a> --}}

                <!-- Settings Icon -->
                <a href="{{ route('settings.index') }}"
                    class="flex items-center justify-center w-6 h-6 rounded-full text-black dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 focus:outline-none focus:ring-1"
                    title="System Settings">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </a>

                <!-- Notification Dropdown -->
                @include('partials.notifications_dropdown')
                {{-- notifications script is bundled by Vite (imported in resources/js/app.js) --}}

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <!-- Profile Icon with Name -->
                        <div class="flex items-center space-x-2 px-2 py-1 rounded-md transition-colors">
                            <!-- Profile Image -->
                            <img src="{{ Auth::user()->profile_photo
                                ? asset('storage/' . Auth::user()->profile_photo)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                alt="{{ Auth::user()->name }}"
                                class="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600" />

                            <!-- User Name -->
                            {{-- <span class="text-gray-800 dark:text-gray-200 text-sm font-medium">
                                {{ Auth::user()->name }}
                            </span> --}}
                    </x-slot>

                    <x-slot name="content" class="ml-0">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>

                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Navigation -->
            <div class="-me-2 flex items-center sm:hidden space-x-2">
                <!-- Mobile Theme Toggle -->
                <x-theme-toggle />
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-white hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    {{-- End of Primary Navigation Menu --}}


    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">

        {{-- <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div> --}}

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-700 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-2 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Notifications') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('user-management.index')">
                    {{ __('User Management') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('settings.index')">
                    {{ __('System Settings') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
