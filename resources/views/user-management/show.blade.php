<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            @if ($user->profile_photo)
                                <img class="h-16 w-16 rounded-full object-cover mr-4"
                                    src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}">
                            @else
                                <div
                                    class="h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-gray-600 dark:text-gray-300">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->primary_role === 'super_admin' ? 'Super Admin' : ucfirst(str_replace('_', ' ', $user->primary_role)) }}
                                    @php
                                        // Determine the correct badge based on both is_active and status
                                        $badgeClass = 'bg-gray-100 text-gray-800';
                                        $badgeText = 'Inactive';

                                        if ($user->is_active && $user->status === 'active') {
                                            $badgeClass = 'bg-green-100 text-green-800';
                                            $badgeText = 'Active';
                                        } elseif ($user->status === 'suspended') {
                                            $badgeClass = 'bg-red-100 text-red-800';
                                            $badgeText = 'Suspended';
                                        } elseif (!$user->is_active || $user->status === 'inactive') {
                                            $badgeClass = 'bg-gray-100 text-gray-800';
                                            $badgeText = 'Inactive';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 {{ $badgeClass }}">
                                        {{ $badgeText }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            @if (!auth()->user()->hasRole('admin'))
                                <a href="{{ route('user-management.edit', $user) }}"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit User
                                </a>
                            @endif
                            <a href="{{ route('user-management.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Personal Information -->
                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Personal Information
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Full Name:</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                            </div>
                            @if ($user->first_name || $user->last_name)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">First Name:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Last Name:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->last_name ?? 'N/A' }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white break-all">{{ $user->email }}</span>
                            </div>
                            @if ($user->username)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Username:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->username }}</span>
                                </div>
                            @endif
                            @if ($user->phone)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Phone:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                    <div
                        class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Account Information
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Role:</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->primary_role === 'super_admin' ? 'Super Admin' : ucfirst(str_replace('_', ' ', $user->primary_role)) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                    <span
                                        class="text-sm font-medium {{ $user->status === 'active' ? 'text-green-600' : ($user->status === 'suspended' ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ ucfirst($user->status ?? 'Active') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Can Login:</span>
                                    <span
                                        class="text-sm font-medium {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $user->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Last Login:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y g:i A') : 'Never' }}
                                    </span>
                                </div>
                                @if (isset($user->login_count))
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Login Count:</span>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->login_count ?? 0 }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                <!-- Work Information -->
                {{-- <div class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Work Information
                        </h3>
                        <div class="space-y-3">
                            @if ($user->department)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Department:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->department }}</span>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Department:</span>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-500">Not specified</span>
                                </div>
                            @endif
                            @if ($user->position)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Position:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->position }}</span>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Position:</span>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-500">Not specified</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Created:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->created_at->format('M d, Y g:i A') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->updated_at->format('M d, Y g:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Bio Section (if exists) -->
            @if ($user->bio)
                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Bio
                        </h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $user->bio }}</p>
                    </div>
                </div>
            @endif

            <!-- Activity History -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Recent Activity
                    </h3>
                    @if ($activities->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Action</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Description</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($activities as $activity)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ ucfirst($activity->action ?? 'Action') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                {{ $activity->description ?? 'No description' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $activity->created_at->format('M d, Y g:i A') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No activity yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user has no recent actions.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
