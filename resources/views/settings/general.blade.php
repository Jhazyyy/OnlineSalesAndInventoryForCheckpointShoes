<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-gray-200 dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <a href="{{ route('settings.index') }}" class="mr-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                            </a>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                                {{-- <svg class="w-8 h-8 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg> --}}
                                General Settings
                            </h1>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Configure your company information, business hours, and basic system preferences.
                        </p>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route('settings.general.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Company Information Section -->
                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Company Information
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- Company Name -->
                                <div>
                                    <x-input-label for="company_name" :value="__('Company Name')" />
                                    <x-text-input 
                                        id="company_name" 
                                        name="company_name" 
                                        type="text" 
                                        class="mt-1 block w-full" 
                                        :value="old('company_name', $settings['company_name'] ?? $defaults['company_name']['value'] ?? 'Checkpoint')" 
                                        required 
                                    />
                                    <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                                    {{-- <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The name of your business displayed throughout the system</p> --}}
                                </div>

                                <!-- Company Phone -->
                                <div>
                                    <x-input-label for="company_phone" :value="__('Company Phone')" />
                                    <x-text-input 
                                        id="company_phone" 
                                        name="company_phone" 
                                        type="text" 
                                        class="mt-1 block w-full" 
                                        :value="old('company_phone', $settings['company_phone'] ?? $defaults['company_phone']['value'] ?? '')" 
                                        placeholder="+63-XXX-XXX-XXXX"
                                    />
                                    <x-input-error class="mt-2" :messages="$errors->get('company_phone')" />
                                </div>

                                <!-- Company Email -->
                                <div>
                                    <x-input-label for="company_email" :value="__('Company Email')" />
                                    <x-text-input 
                                        id="company_email" 
                                        name="company_email" 
                                        type="email" 
                                        class="mt-1 block w-full" 
                                        :value="old('company_email', $settings['company_email'] ?? $defaults['company_email']['value'] ?? '')" 
                                        placeholder="info@checkpoint.com"
                                    />
                                    <x-input-error class="mt-2" :messages="$errors->get('company_email')" />
                                </div>

                                <!-- Timezone -->
                                <div>
                                    <x-input-label for="timezone" :value="__('Timezone')" />
                                    <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @php
                                            $timezones = [
                                                'Asia/Manila' => 'Asia/Manila (Philippines)',
                                                'UTC' => 'UTC (Coordinated Universal Time)',
                                                'America/New_York' => 'America/New_York (Eastern Time)',
                                                'America/Los_Angeles' => 'America/Los_Angeles (Pacific Time)',
                                                'Europe/London' => 'Europe/London (Greenwich Mean Time)',
                                                'Asia/Tokyo' => 'Asia/Tokyo (Japan Standard Time)',
                                                'Asia/Singapore' => 'Asia/Singapore (Singapore Standard Time)',
                                                'Australia/Sydney' => 'Australia/Sydney (Australian Eastern Time)'
                                            ];
                                            $currentTimezone = old('timezone', $settings['timezone'] ?? $defaults['timezone']['value'] ?? 'Asia/Manila');
                                        @endphp
                                        @foreach($timezones as $value => $label)
                                            <option value="{{ $value }}" {{ $currentTimezone === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                                </div>
                            </div>

                            <!-- Company Address -->
                            <div class="mt-6">
                                <x-input-label for="company_address" :value="__('Company Address')" />
                                <textarea 
                                    id="company_address" 
                                    name="company_address" 
                                    rows="1" 
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    placeholder="123 Main Street, City, Province, Postal Code, Philippines">{{ old('company_address', $settings['company_address'] ?? $defaults['company_address']['value'] ?? '') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('company_address')" />
                            </div>
                        </div>

                        <!-- Business Hours Section -->
                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Business Hours
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Set your operating hours for each day of the week</p>
                            
                            @php
                                $businessHours = old('business_hours', $settings['business_hours'] ?? $defaults['business_hours']['value'] ?? [
                                    'monday' => ['open' => '09:00', 'close' => '17:00'],
                                    'tuesday' => ['open' => '09:00', 'close' => '17:00'],
                                    'wednesday' => ['open' => '09:00', 'close' => '17:00'],
                                    'thursday' => ['open' => '09:00', 'close' => '17:00'],
                                    'friday' => ['open' => '09:00', 'close' => '17:00'],
                                    'saturday' => ['open' => '09:00', 'close' => '17:00'],
                                    'sunday' => ['open' => '10:00', 'close' => '16:00']
                                ]);
                                $days = [
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday',
                                    'saturday' => 'Saturday',
                                    'sunday' => 'Sunday'
                                ];
                            @endphp
                            
                            <div class="space-y-4">
                                @foreach($days as $day => $dayLabel)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="w-24 flex-shrink-0">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $dayLabel }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Open:</label>
                                        <input 
                                            type="time" 
                                            name="business_hours[{{ $day }}][open]" 
                                            value="{{ $businessHours[$day]['open'] ?? '09:00' }}"
                                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                                        />
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm text-gray-600 dark:text-gray-400">Close:</label>
                                        <input 
                                            type="time" 
                                            name="business_hours[{{ $day }}][close]" 
                                            value="{{ $businessHours[$day]['close'] ?? '18:00' }}"
                                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                                        />
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                    {{-- <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg> --}}
                                    Save
                                </button>
                                <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    Cancel
                                </a>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex space-x-2">
                                <button type="button" onclick="resetToDefaults()" class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 px-3 py-2 text-sm font-medium">
                                    Reset to Defaults
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Current Settings Preview -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Settings Preview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Company Information</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Name:</strong> {{ $settings['company_name'] ?? 'Not set' }}</li>
                                    <li><strong>Phone:</strong> {{ $settings['company_phone'] ?? 'Not set' }}</li>
                                    <li><strong>Email:</strong> {{ $settings['company_email'] ?? 'Not set' }}</li>
                                    <li><strong>Timezone:</strong> {{ $settings['timezone'] ?? 'Not set' }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Business Status</h4>
                                <div class="flex items-center space-x-2">
                                    @if(isBusinessOpen())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Currently Open
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Currently Closed
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ date('l, g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function resetToDefaults() {
        if (confirm('Are you sure you want to reset all general settings to their default values? This action cannot be undone.')) {
            // Reset form fields to default values
            document.getElementById('company_name').value = 'Checkpoint';
            document.getElementById('company_phone').value = '';
            document.getElementById('company_email').value = '';
            document.getElementById('company_address').value = '';
            document.getElementById('timezone').value = 'Asia/Manila';
            
            // Reset business hours
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const defaultHours = {
                'monday': { open: '09:00', close: '17:00' },
                'tuesday': { open: '09:00', close: '17:00' },
                'wednesday': { open: '09:00', close: '17:00' },
                'thursday': { open: '09:00', close: '17:00' },
                'friday': { open: '09:00', close: '17:00' },
                'saturday': { open: '09:00', close: '17:00' },
                'sunday': { open: '10:00', close: '16:00' }
            };
            
            days.forEach(day => {
                const openInput = document.querySelector(`input[name="business_hours[${day}][open]"]`);
                const closeInput = document.querySelector(`input[name="business_hours[${day}][close]"]`);
                if (openInput) openInput.value = defaultHours[day].open;
                if (closeInput) closeInput.value = defaultHours[day].close;
            });
        }
    }
    </script>
</x-app-layout>