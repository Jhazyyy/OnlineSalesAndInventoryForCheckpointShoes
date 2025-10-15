<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Section (Matches Show View) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-xl font-bold text-gray-600 dark:text-gray-300">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit: {{ $user->name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($user->role ?? 'User') }}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-green-800' }}">
                                        {{ ucfirst($user->status ?? 'Active') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('user-management.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Users
                        </a>
                    </div>
                </div>
            </div>

            <!-- Edit Form Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('user-management.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input id="name" name="name" type="text"
                                   class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-transparent focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:text-white"
                                   value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input id="email" name="email" type="email"
                                   class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-transparent focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:text-white"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Password <span class="text-xs text-gray-400">(leave blank to keep current)</span>
                            </label>
                            <input id="password" name="password" type="password"
                                   class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-transparent focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:text-white"
                                   autocomplete="new-password">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-transparent focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:text-white"
                                   autocomplete="new-password">
                            @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select id="role" name="role"
                                    class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-transparent focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:text-white">
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                            </select>
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select id="status" name="status"
                                    class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-700 border-transparent focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:text-white">
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('user-management.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-semibold rounded-md text-sm">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-md text-sm">
                                Update User
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
