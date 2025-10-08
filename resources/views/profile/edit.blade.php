<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <!-- Success Message -->
            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Profile updated successfully!</span>
                    </div>
                </div>
            @endif

            <!-- Profile Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <!-- Gradient Header with Avatar -->
                <div class="relative h-32 bg-gradient-to-r from-gray-400 via-orange-400 to-cyan-500">
                    <!-- User Profile Link (top right) -->
                    <div class="absolute top-4 right-6">
                        <a href="{{ route('profile.edit') }}"
                            class="text-white hover:text-gray-100 text-sm font-medium">
                            User Profile
                        </a>
                    </div>

                    <!-- Avatar positioned to overlap -->
                    <div class="absolute -bottom-16 left-8">
                        <div class="relative">
                            <!-- Avatar Container -->
                            <div
                                class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden bg-gray-200 dark:bg-gray-700 group cursor-pointer relative">
                                <!-- Hidden File Input -->
                                <form id="avatarForm" method="POST" action="{{ route('profile.update') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Hidden fields to preserve existing data (only include if ProfileUpdateRequest requires them) -->
                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                    @if($user->first_name)
                                        <input type="hidden" name="first_name" value="{{ $user->first_name }}">
                                    @endif
                                    @if($user->last_name)
                                        <input type="hidden" name="last_name" value="{{ $user->last_name }}">
                                    @endif
                                    @if($user->phone)
                                        <input type="hidden" name="phone" value="{{ $user->phone }}">
                                    @endif
                                    @if($user->username)
                                        <input type="hidden" name="username" value="{{ $user->username }}">
                                    @endif

                                    <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*"
                                        class="hidden" onchange="previewAndSubmit(event)">
                                </form>


                                <!-- Display Image or Placeholder -->
                                @if($user->profile_photo)
                                    <img id="avatarPreview" src="{{ asset('storage/' . $user->profile_photo) }}"
                                        alt="{{ $user->name }}"
                                        class="w-full h-full object-cover transition-opacity duration-300">
                                @else
                                    <div id="avatarPreview"
                                        class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Overlay (appears on hover) -->
                                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
                                    id="hoverOverlay">
                                    <span class="text-white text-sm">Upload New Photo</span>
                                </div>

                                <!-- Loading Overlay -->
                                <div id="loadingOverlay"
                                    class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden">
                                    <div class="text-white text-center">
                                        <svg class="animate-spin h-8 w-8 mx-auto mb-2"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span class="text-xs">Uploading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function previewAndSubmit(event) {
                            const input = event.target;
                            const file = input.files[0];
                            if (file) {
                                // Validate file size (2MB max)
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('File size must be less than 2MB');
                                    input.value = '';
                                    return;
                                }

                                // Validate file type
                                if (!file.type.match('image.*')) {
                                    alert('Please select an image file');
                                    input.value = '';
                                    return;
                                }

                                const preview = document.getElementById('avatarPreview');
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    if (preview.tagName === 'IMG') {
                                        preview.src = e.target.result;
                                    } else {
                                        const img = document.createElement('img');
                                        img.src = e.target.result;
                                        img.className = "w-full h-full object-cover transition-opacity duration-300";
                                        preview.replaceWith(img);
                                        img.id = "avatarPreview";
                                    }
                                };
                                reader.readAsDataURL(file);

                                // Show loading overlay
                                const loadingOverlay = document.getElementById('loadingOverlay');
                                const hoverOverlay = document.getElementById('hoverOverlay');
                                hoverOverlay.classList.add('hidden');
                                loadingOverlay.classList.remove('hidden');
                                loadingOverlay.classList.add('flex');

                                // Auto-submit after showing preview
                                setTimeout(() => {
                                    document.getElementById('avatarForm').submit();
                                }, 500);
                            }
                        }

                        // Open file dialog when clicking the avatar
                        document.addEventListener('DOMContentLoaded', () => {
                            const avatarContainer = document.querySelector('.group');
                            if (avatarContainer) {
                                avatarContainer.addEventListener('click', () => {
                                    document.getElementById('profile_photo_input').click();
                                });
                            }
                        });
                    </script>
                </div>

                <!-- Profile Info and Form -->
                <div class="pt-20 pb-8 px-8">
                    <!-- User Name Display -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update your photo and personal information.
                        </p>
                    </div>

                    <!-- Profile Form -->
                    <div class="mt-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Password Update Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>