<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


    <!-- Error Messages -->
    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-4 sm:space-y-6">
        @csrf

        <!-- Email or Username -->
        <div>
            <x-input-label for="login" :value="__('Email or Username')" class="text-sm sm:text-base" />
            <x-text-input id="login" class="block mt-1 w-full text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5"
                type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm sm:text-base" />

            <x-text-input id="password" class="block mt-1 w-full text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2.5"
                type="password" name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800 w-4 h-4"
                    name="remember">
                <span class="ms-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        {{-- <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
           <div class="order-1 sm:order-1">
                @if (Route::has('password.request'))
                        <a class="underline text-xs sm:text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                @endif 
                <x-primary-button class="order-1 sm:order-2 w-full sm:w-auto justify-center px-6 py-2.5 sm:px-4 sm:py-2 text-sm sm:text-base">
                {{ __('Log in') }}
                </x-primary-button>
             
            </div>   --}}

        <x-primary-button
            class="w-full lg:w-full sm:w-auto md:w-full items-center  justify-center px-6 py-2.5 sm:px-4 sm:py-2 text-sm sm:text-base">
            {{ __('Log in') }}
        </x-primary-button>
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
            const login = formData.get('login');
            if (login) {
                sessionStorage.setItem('loginValue', login);
            }
        });

        // Restore login value if coming back from 419 error
        window.addEventListener('load', function() {
            const savedLogin = sessionStorage.getItem('loginValue');
            if (savedLogin && document.getElementById('login').value === '') {
                document.getElementById('login').value = savedLogin;
            }
        });
    </script>
</x-guest-layout>
