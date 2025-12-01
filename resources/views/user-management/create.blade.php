<x-app-layout>
    <div class="py-2">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create User</h2>
                            <p class="text-gray-600 dark:text-gray-400">Add new user and assign role</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('user-management.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Content Section -->

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Display validation errors -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user-management.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="mb-2">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Personal Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-6 gap-2">
                                <!-- First Name -->
                                <div>
                                    <label for="first_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="first_name" name="first_name"
                                        value="{{ old('first_name') }}" required maxlength="255"
                                        placeholder="Enter first name"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('first_name') border-red-500 @enderror">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <label for="last_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                        required maxlength="255" placeholder="Enter last name"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('last_name') border-red-500 @enderror">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        required maxlength="255" placeholder="Enter email address"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Username -->
                                <div>
                                    <label for="username"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Username <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                                        maxlength="255" placeholder="Enter username" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('username') border-red-500 @enderror">
                                    @error('username')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                        maxlength="20" placeholder="Enter phone number"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('phone') border-red-500 @enderror">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Profile Photo -->
                                <div>
                                    <label for="profile_photo"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Profile Photo
                                    </label>
                                    <input type="file" id="profile_photo" name="profile_photo"
                                        accept="image/jpeg,image/png,image/jpg,image/gif"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300 @error('profile_photo') border-red-500 @enderror">
                                    @error('profile_photo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs font-mono text-gray-500">Max size: 2MB. Formats: JPEG, PNG,
                                        JPG, GIF</p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="mb-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Account Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <!-- Password -->
                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="password" name="password" required minlength="8"
                                        placeholder="Enter password"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <div id="password-validation" class="mt-1 text-xs space-y-1" style="display: none;">
                                        <p id="length-check" class="text-gray-500">
                                            <span class="validation-icon">○</span> At least 8 characters
                                        </p>
                                        <p id="special-check" class="text-gray-500">
                                            <span class="validation-icon">○</span> At least one special character (!@#$%^&*(),.?":{}|<>)
                                        </p>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Confirm Password <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        required minlength="8" placeholder="Confirm password"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p id="match-check" class="mt-1 text-xs text-gray-500" style="display: none;">
                                        <span class="validation-icon">○</span> Passwords must match
                                    </p>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label for="role"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Role <span class="text-red-500">*</span>
                                    </label>
                                    <select id="role" name="role" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('role') border-red-500 @enderror">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}"
                                                {{ old('role') == $role ? 'selected' : '' }}>
                                                {{ $role === 'super_admin' ? 'Super Admin' : ucwords(str_replace('_', ' ', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status" name="status" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('status') border-red-500 @enderror">
                                        <option value="">Select Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ old('status', 'active') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Is Active -->
                                <div class="flex items-center md:col-span-2">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', '1') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="is_active"
                                        class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        User is active and can login
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Work Information Section -->
                        {{-- <div class="mb-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Work Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Department -->
                                <div>
                                    <label for="department"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Department
                                    </label>
                                    <input type="text" id="department" name="department"
                                        value="{{ old('department') }}" maxlength="255"
                                        placeholder="Enter department (optional)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('department') border-red-500 @enderror">
                                    @error('department')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Position -->
                                <div>
                                    <label for="position"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Position
                                    </label>
                                    <input type="text" id="position" name="position"
                                        value="{{ old('position') }}" maxlength="255"
                                        placeholder="Enter position (optional)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('position') border-red-500 @enderror">
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Bio -->
                                <div class="md:col-span-2">
                                    <label for="bio"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Bio
                                    </label>
                                    <textarea id="bio" name="bio" rows="4" maxlength="1000" placeholder="Enter bio or notes (optional)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('bio') border-red-500 @enderror">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('user-management.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const validationDiv = document.getElementById('password-validation');
            const lengthCheck = document.getElementById('length-check');
            const specialCheck = document.getElementById('special-check');
            const matchCheck = document.getElementById('match-check');

            // Show validation messages when user starts typing
            passwordInput.addEventListener('input', function() {
                const value = this.value;
                
                // Show validation div when user starts typing
                if (value.length > 0) {
                    validationDiv.style.display = 'block';
                } else {
                    validationDiv.style.display = 'none';
                }

                // Check length
                if (value.length >= 8) {
                    lengthCheck.classList.remove('text-gray-500', 'text-red-500');
                    lengthCheck.classList.add('text-green-600');
                    lengthCheck.querySelector('.validation-icon').textContent = '✓';
                } else if (value.length > 0) {
                    lengthCheck.classList.remove('text-gray-500', 'text-green-600');
                    lengthCheck.classList.add('text-red-500');
                    lengthCheck.querySelector('.validation-icon').textContent = '✗';
                } else {
                    lengthCheck.classList.remove('text-red-500', 'text-green-600');
                    lengthCheck.classList.add('text-gray-500');
                    lengthCheck.querySelector('.validation-icon').textContent = '○';
                }

                // Check special character
                const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;
                if (specialCharRegex.test(value)) {
                    specialCheck.classList.remove('text-gray-500', 'text-red-500');
                    specialCheck.classList.add('text-green-600');
                    specialCheck.querySelector('.validation-icon').textContent = '✓';
                } else if (value.length > 0) {
                    specialCheck.classList.remove('text-gray-500', 'text-green-600');
                    specialCheck.classList.add('text-red-500');
                    specialCheck.querySelector('.validation-icon').textContent = '✗';
                } else {
                    specialCheck.classList.remove('text-red-500', 'text-green-600');
                    specialCheck.classList.add('text-gray-500');
                    specialCheck.querySelector('.validation-icon').textContent = '○';
                }

                // Check password match
                checkPasswordMatch();
            });

            // Check password confirmation match
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length > 0) {
                    matchCheck.style.display = 'block';
                    if (password === confirmPassword && password.length > 0) {
                        matchCheck.classList.remove('text-gray-500', 'text-red-500');
                        matchCheck.classList.add('text-green-600');
                        matchCheck.querySelector('.validation-icon').textContent = '✓';
                    } else {
                        matchCheck.classList.remove('text-gray-500', 'text-green-600');
                        matchCheck.classList.add('text-red-500');
                        matchCheck.querySelector('.validation-icon').textContent = '✗';
                    }
                } else {
                    matchCheck.style.display = 'none';
                }
            }
        });
    </script>
</x-app-layout>
