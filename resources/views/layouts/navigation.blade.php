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
                {{-- <div class="shrink-0 flex items-center pl-2">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="h-12 rounded-lg border-2" />
                    </a>
                </div> --}}

                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}}


                <x-breadcrumb :items="$breadcrumbs" />
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
                {{-- @hasanyrole('super_admin|admin')
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
                @endhasanyrole --}}

                <!-- Notifications Modal -->
                <div x-data="notificationModal()" @click.away="closeModal()" class="relative">
                    <button @click="toggleModal()" type="button"
                        class="relative flex items-center justify-center w-6 h-6 rounded-full text-black dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 focus:outline-none focus:ring-1"
                        title="Notifications">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <!-- Notification Badge - shows count when there are unread notifications -->
                        @if (isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                            <span
                                class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-opacity-100 text-xs font-bold text-white bg-red-500 rounded-md ring-white dark:ring-gray-900"
                                x-text="unreadCount > 99 ? '99+' : unreadCount">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Notification Modal Dropdown -->
                    <div x-show="isOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50"
                         style="display: none;">
                        
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                <div class="flex items-center gap-2">
                                    <button @click="markAllAsRead()" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        Mark all read
                                    </button>
                                    <a href="{{ route('notifications.list') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        View all
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="loading">
                                <div class="p-8 text-center">
                                    <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Loading notifications...</p>
                                </div>
                            </template>

                            <template x-if="!loading && notifications.length === 0">
                                <div class="p-8 text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">No notifications</p>
                                </div>
                            </template>

                            <template x-if="!loading && notifications.length > 0">
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                                             :class="{ 'bg-blue-50 dark:bg-blue-900/10': !notification.read_at }">
                                            <div class="flex items-start gap-3">
                                                <!-- Icon -->
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                                         :class="{
                                                             'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400': notification.level === 'danger',
                                                             'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400': notification.level === 'warning',
                                                             'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400': notification.level === 'success',
                                                             'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400': notification.level === 'info'
                                                         }">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <template x-if="notification.level === 'danger' || notification.level === 'warning'">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                            </template>
                                                            <template x-if="notification.level === 'success'">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </template>
                                                            <template x-if="notification.level === 'info'">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </template>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <div class="flex-1">
                                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="notification.title"></h4>
                                                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400 line-clamp-2" x-text="notification.message"></p>
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500" x-text="notification.time_ago"></p>
                                                        </div>
                                                        <template x-if="!notification.read_at">
                                                            <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                                                        </template>
                                                    </div>
                                                    
                                                    <!-- Actions -->
                                                    <div class="mt-2 flex gap-2">
                                                        <template x-if="notification.link">
                                                            <a :href="notification.link" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                                                View Details
                                                            </a>
                                                        </template>
                                                        <template x-if="!notification.read_at">
                                                            <button @click.stop="markAsRead(notification.id)" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                                                                Mark as read
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

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

                <x-responsive-nav-link :href="route('notifications.list')" class="flex items-center justify-between">
                    <span>{{ __('Notifications') }}</span>
                    @if (isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        <span
                            class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>

                @hasanyrole('super_admin|admin')
                    <x-responsive-nav-link :href="route('user-management.index')">
                        {{ __('User Management') }}
                    </x-responsive-nav-link>
                @endhasanyrole

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

<script>
function notificationModal() {
    return {
        isOpen: false,
        loading: false,
        notifications: [],
        unreadCount: {{ isset($unreadNotificationCount) ? $unreadNotificationCount : 0 }},

        toggleModal() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.notifications.length === 0) {
                this.fetchNotifications();
            }
        },

        closeModal() {
            this.isOpen = false;
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("notifications.latest") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    // Update the notification in the list
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.read_at = new Date().toISOString();
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    // Update all notifications
                    this.notifications.forEach(n => n.read_at = new Date().toISOString());
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        }
    };
}
</script>
