<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage system users and their permissions</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('user-management.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Users</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
{{-- 
                <div class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Super Admins</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['super_admins'] }}</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                </div> --}}
{{-- 
                <div class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Admins</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['admins'] }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                </div> --}}

                {{-- <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New This Month</p>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $stats['new_this_month'] }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Search and Filters -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('user-management.index') }}" class="space-y-4">
                        <!-- Inputs Row -->
                        <div class="flex flex-col md:flex-row md:items-end md:space-x-6">
                            <!-- Search -->
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="Search by name, email, username..."
                                    class="mt-1 block w-full md:w-72 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Role Filter -->
                            <div class="mt-4 md:mt-0">
                                <label for="role"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                                <select id="role" name="role"
                                    class="mt-1 block w-full md:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}"
                                            {{ request('role') == $role ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $role)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="mt-4 md:mt-0">
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" name="status"
                                    class="mt-1 block w-full md:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Statuses</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Active Filter -->
                            <div class="mt-4 md:mt-0">
                                <label for="is_active"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
                                <select id="is_active" name="is_active"
                                    class="mt-1 block w-full md:w-40 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Buttons Row -->
                        <div class="flex gap-2">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-800 focus:bg-blue-800 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>

                            <a href="{{ route('user-management.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User List with Bulk Actions -->
            <form id="bulkActionForm" method="POST" action="{{ route('user-management.bulk-delete') }}">
                @csrf
                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        {{-- <div class="flex items-center space-x-4">
                            <button type="button" id="selectAll" 
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Select All
                            </button>
                            <button type="button" id="deselectAll" 
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Deselect All
                            </button>
                            <span id="selectedCount" class="text-sm text-gray-600 dark:text-gray-400">
                                0 selected
                            </span>
                        </div> --}}
                        {{-- <button type="submit" id="bulkDeleteBtn" disabled
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            onclick="return confirm('Are you sure you want to delete selected users?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Selected
                        </button> --}}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Last Login
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if ($user->profile_photo)
                                                        <img class="max-w-20 max-h-auto rounded-full object-cover"
                                                            src="{{ asset(path: 'storage/' . $user->profile_photo) }}"
                                                            alt="{{ $user->name }}">
                                                    @else
                                                        <div
                                                            class="h-12 w-12 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                            <span
                                                                class="text-xl font-semibold text-gray-600 dark:text-gray-300">
                                                                {{ substr($user->name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $user->name }}
                                                        @if ($user->id === auth()->id())
                                                            <span
                                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                You
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($user->username)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $user->username }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $user->email }}
                                            </div>
                                            @if ($user->phone)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->phone }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $user->role_color }}-100 text-{{ $user->role_color }}-800">
                                                {{ $user->primary_role === 'super_admin' ? 'Super Admin' : ucwords(str_replace('_', ' ', $user->primary_role)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                // Determine the correct status text based on both is_active and status
                                                $statusText = 'Inactive';
                                                
                                                if ($user->status === 'suspended') {
                                                    $statusText = 'Suspended';
                                                } elseif ($user->is_active && $user->status === 'active') {
                                                    $statusText = 'Active';
                                                } elseif (!$user->is_active || $user->status === 'inactive') {
                                                    $statusText = 'Inactive';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $user->status_color }}-100 text-{{ $user->status_color }}-800">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if ($user->last_login_at)
                                                <div>{{ $user->last_login_at->diffForHumans() }}</div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                                    {{ $user->login_count }} {{ Str::plural('login', $user->login_count) }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('user-management.show', $user) }}"
                                                    class="text-blue-600 hover:text-blue-900" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                @if (!$user->hasAnyRole(['super_admin', 'admin']) || (auth()->user()->hasRole('super_admin') && $user->hasRole('admin')))
                                                    <a href="{{ route('user-management.edit', $user) }}"
                                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @if ($user->id !== auth()->id() && (!$user->hasAnyRole(['super_admin', 'admin']) || (auth()->user()->hasRole('super_admin') && $user->hasRole('admin'))))
                                                    <form action="{{ route('user-management.destroy', $user) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                                            title="Delete">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                const userCheckboxes = document.querySelectorAll('.user-checkbox');
                const selectedCount = document.getElementById('selectedCount');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const selectAllBtn = document.getElementById('selectAll');
                const deselectAllBtn = document.getElementById('deselectAll');

                function updateSelectedCount() {
                    const checkedCount = document.querySelectorAll('.user-checkbox:checked:not(:disabled)').length;
                    selectedCount.textContent = `${checkedCount} selected`;
                    bulkDeleteBtn.disabled = checkedCount === 0;
                }

                // Select All Checkbox in header
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        userCheckboxes.forEach(checkbox => {
                            if (!checkbox.disabled) {
                                checkbox.checked = this.checked;
                            }
                        });
                        updateSelectedCount();
                    });
                }

                // Individual checkboxes
                userCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateSelectedCount();

                        // Update "Select All" checkbox state
                        const enabledCheckboxes = document.querySelectorAll(
                            '.user-checkbox:not(:disabled)');
                        const checkedEnabledCheckboxes = document.querySelectorAll(
                            '.user-checkbox:checked:not(:disabled)');

                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = enabledCheckboxes.length > 0 &&
                                enabledCheckboxes.length === checkedEnabledCheckboxes.length;
                        }
                    });
                });

                // Select All button
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        userCheckboxes.forEach(checkbox => {
                            if (!checkbox.disabled) {
                                checkbox.checked = true;
                            }
                        });
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = true;
                        }
                        updateSelectedCount();
                    });
                }

                // Deselect All button
                if (deselectAllBtn) {
                    deselectAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        userCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                        }
                        updateSelectedCount();
                    });
                }

                // Initial count
                updateSelectedCount();
            });
        </script>
    @endpush
</x-app-layout>
