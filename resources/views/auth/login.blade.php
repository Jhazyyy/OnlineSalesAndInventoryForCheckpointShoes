<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


    <!-- Error Messages -->
    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div>
                @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                @endif
                <x-primary-button class="px-4 py-2">
                {{ __('Log in') }}
                </x-primary-button>
        </div>
    </form>

    <!-- Auto-refresh token if page is idle for too long -->
    <script>
        // Refresh the page if it's been idle for more than 100 minutes (before session expires at 120 minutes)
        let idleTime = 0;
        const maxIdleTime = 100; // minutes
        
        // Increment idle time counter every minute
        const idleInterval = setInterval(timerIncrement, 60000); // 1 minute
        
        // Reset timer on user activity
        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('keypress', resetTimer);
        document.addEventListener('click', resetTimer);
        document.addEventListener('scroll', resetTimer);
        
        function timerIncrement() {
            idleTime++;
            if (idleTime >= maxIdleTime) {
                // Reload the page to get a fresh CSRF token
                window.location.reload();
            }
        }
        
        function resetTimer() {
            idleTime = 0;
        }

        // Handle form submission errors (419)
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Store form data in case of error
            const formData = new FormData(this);
            const email = formData.get('email');
            if (email) {
                sessionStorage.setItem('loginEmail', email);
            }
        });

        // Restore email if coming back from 419 error
        window.addEventListener('load', function() {
            const savedEmail = sessionStorage.getItem('loginEmail');
            if (savedEmail && document.getElementById('email').value === '') {
                document.getElementById('email').value = savedEmail;
            }
        });
    </script>
</x-guest-layout>