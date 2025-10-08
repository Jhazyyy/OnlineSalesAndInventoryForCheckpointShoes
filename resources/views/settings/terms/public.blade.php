<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $terms->title }} - Checkpoint Shoes</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $terms->title }}
                        </h1>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $terms->type_label }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Version {{ $terms->version }}
                            </span>
                            @if($terms->effective_date)
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Effective Date: {{ $terms->effective_date->format('F d, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('welcome') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <!-- Document Info -->
                        <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $terms->updated_at->format('F d, Y') }}</dd>
                                </div>
                                @if($terms->effective_date)
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Effective Date</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $terms->effective_date->format('F d, Y') }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Version</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $terms->version }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            <div class="whitespace-pre-wrap break-words">
                                {!! nl2br(e($terms->content)) !!}
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    © {{ date('Y') }} Checkpoint Shoes. All rights reserved.
                                </p>
                                @auth
                                    <a href="{{ route('dashboard') }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Sign In
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
