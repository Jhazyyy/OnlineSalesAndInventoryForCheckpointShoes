<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Notifications</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Stay updated with your latest activities and
                    alerts</p>
            </div>

            <!-- Actions Bar -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('notifications.list', ['filter' => 'all']) }}"
                            class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'all' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            All
                            <span
                                class="ml-2 px-2 py-0.5 text-xs {{ $filter === 'all' ? 'bg-blue-600 dark:bg-blue-700' : 'bg-gray-500' }} text-white rounded-full">{{ $counts['all'] }}</span>
                        </a>
                        <a href="{{ route('notifications.list', ['filter' => 'unread']) }}"
                            class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'unread' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            Unread
                            <span
                                class="ml-2 px-2 py-0.5 text-xs {{ $counts['unread'] > 0 ? 'bg-red-500' : 'bg-gray-500' }} text-white rounded-full">{{ $counts['unread'] }}</span>
                        </a>
                        <a href="{{ route('notifications.list', ['filter' => 'orders']) }}"
                            class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'orders' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            Orders
                            <span
                                class="ml-2 px-2 py-0.5 text-xs bg-gray-500 text-white rounded-full">{{ $counts['orders'] }}</span>
                        </a>
                        <a href="{{ route('notifications.list', ['filter' => 'inventory']) }}"
                            class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'inventory' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            Inventory
                            <span
                                class="ml-2 px-2 py-0.5 text-xs bg-gray-500 text-white rounded-full">{{ $counts['inventory'] }}</span>
                        </a>
                        {{-- <a href="{{ route('notifications.list', ['filter' => 'system']) }}"
                            class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'system' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
                            System
                            <span
                                class="ml-2 px-2 py-0.5 text-xs bg-gray-500 text-white rounded-full">{{ $counts['system'] }}</span>
                        </a> --}}
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                            <button onclick="markAllAsRead()"
                                class="px-4 py-2 text-sm font-medium rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Mark all as read
                            </button>
                            <button onclick="deleteAllNotifications()"
                                class="px-4 py-2 text-sm font-medium rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Delete all
                            </button>
                        {{-- <a href="{{ route('settings.notifications') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a> --}}
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            @if ($notifications->isEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="w-16 h-56 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">No notifications</h3>
                    <p class="text-gray-600 dark:text-gray-400">You're all caught up! No new notifications at the
                        moment.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($notifications as $notification)
                        @php
                            $iconColors = [
                                'danger' => 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400',
                                'warning' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400',
                                'success' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400',
                                'info' => 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400',
                            ];
                            $borderColors = [
                                'danger' => 'border-red-500',
                                'warning' => 'border-yellow-500',
                                'success' => 'border-green-500',
                                'info' => 'border-blue-500',
                            ];
                            $iconClass = $iconColors[$notification->level] ?? $iconColors['info'];
                            $borderClass = $borderColors[$notification->level] ?? $borderColors['info'];

                            $categoryBadge = '';
                            if (str_contains($notification->type, 'inventory.')) {
                                $categoryBadge = 'Inventory';
                                $badgeColor = 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300';
                            } elseif (str_contains($notification->type, 'sales.')) {
                                $categoryBadge = 'Sales';
                                $badgeColor = 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300';
                            } elseif (str_contains($notification->type, 'purchases.')) {
                                $categoryBadge = 'Purchases';
                                $badgeColor = 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
                            } else {
                                $categoryBadge = 'System';
                                $badgeColor = 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                            }
                        @endphp

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 {{ $borderClass }} hover:shadow-md transition-shadow notification-item"
                            data-id="{{ $notification->id }}">
                            <div class="p-4">
                                <div class="flex items-start gap-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 rounded-full {{ $iconClass }} flex items-center justify-center">
                                            @if ($notification->level === 'danger' || $notification->level === 'warning')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                    </path>
                                                </svg>
                                            @elseif($notification->level === 'success')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex-1">
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $notification->title }}
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $notification->message }}
                                                </p>
                                                <div
                                                    class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </span>
                                                    <span class="px-2 py-0.5 {{ $badgeColor }} rounded-full">
                                                        {{ $categoryBadge }}
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- Unread Indicator -->
                                            @if ($notification->isUnread())
                                                <div class="flex-shrink-0">
                                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="mt-3 flex gap-2">
                                            @if ($notification->link)
                                                <a href="{{ $notification->link }}"
                                                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                                    View Details
                                                </a>
                                            @endif
                                            @if ($notification->isUnread())
                                                <button onclick="markAsRead({{ $notification->id }})"
                                                    class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition-colors">
                                                    Mark as Read
                                                </button>
                                            @endif
                                            <button onclick="deleteNotification({{ $notification->id }})"
                                                class="px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function markAsRead(notificationId) {
                fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the notification item or reload
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function markAllAsRead() {
                if (!confirm('Mark all notifications as read?')) return;

                fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function deleteNotification(notificationId) {
                if (!confirm('Are you sure you want to delete this notification?')) return;

                fetch(`/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the notification item from DOM
                            document.querySelector(`[data-id="${notificationId}"]`).remove();

                            // Check if no more notifications, reload to show empty state
                            if (document.querySelectorAll('.notification-item').length === 0) {
                                location.reload();
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function deleteAllNotifications() {
                if (!confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) return;

                fetch('/notifications/delete-all', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        </script>
    @endpush
</x-app-layout>
