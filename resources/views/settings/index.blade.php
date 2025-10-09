<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                            <svg class="w-8 h-8 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            System Settings
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            Configure your business parameters and system preferences
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mb-6 flex flex-wrap gap-3">
                        <form action="{{ route('settings.initialize-defaults') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Initialize Defaults
                            </button>
                        </form>
                        
                        {{-- <a href="{{ route('settings.export') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Settings
                        </a> --}}
                        
                        <form action="{{ route('settings.clear-cache') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Clear Cache
                            </button>
                        </form>
                    </div>

                    <!-- Settings Categories Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- General Settings -->
                        <a href="{{ route('settings.general') }}" class="block group">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-xl border border-blue-200 dark:border-blue-700 hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-blue-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                        {{ count($currentSettings['general'] ?? []) }} items
                                    </span> --}}
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">General Settings</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Company information, business hours, timezone, and basic configuration</p>
                                <div class="mt-4 flex items-center text-blue-600 dark:text-blue-400 text-sm font-medium">
                                    Configure <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Financial Settings -->
                        <a href="{{ route('settings.financial') }}" class="block group">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 p-6 rounded-xl border border-green-200 dark:border-green-700 hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-green-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                                        {{ count($currentSettings['financial'] ?? []) }} items
                                    </span> --}}
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Financial Settings</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Currency, tax rates, decimal precision, and financial display preferences</p>
                                <div class="mt-4 flex items-center text-green-600 dark:text-green-400 text-sm font-medium">
                                    Configure <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Inventory Settings -->
                        <a href="{{ route('settings.inventory') }}" class="block group">
                            <div class="bg-gradient-to-br from-yellow-50 to-orange-100 dark:from-yellow-900/20 dark:to-orange-900/20 p-6 rounded-xl border border-yellow-200 dark:border-yellow-700 hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-yellow-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                        {{ count($currentSettings['inventory'] ?? []) }} items
                                    </span> --}}
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Inventory Settings</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Stock thresholds, auto-reorder settings, and inventory management preferences</p>
                                <div class="mt-4 flex items-center text-yellow-600 dark:text-yellow-400 text-sm font-medium">
                                    Configure <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Sales Settings -->
                        <a href="{{ route('settings.sales') }}" class="block group">
                            <div class="bg-gradient-to-br from-purple-50 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 p-6 rounded-xl border border-purple-200 dark:border-purple-700 hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-purple-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200">
                                        {{ count($currentSettings['sales'] ?? []) }} items
                                    </span> --}}
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sales Settings</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Order prefixes, return policies, exchange rules, and sales preferences</p>
                                <div class="mt-4 flex items-center text-purple-600 dark:text-purple-400 text-sm font-medium">
                                    Configure <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Notification Settings -->
                        <a href="{{ route('settings.notifications') }}" class="block group">
                            <div class="bg-gradient-to-br from-red-50 to-pink-100 dark:from-red-900/20 dark:to-pink-900/20 p-6 rounded-xl border border-red-200 dark:border-red-700 hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-red-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    {{-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">
                                        {{ count($currentSettings['notifications'] ?? []) }} items
                                    </span> --}}
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notification Settings</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Email alerts, low stock notifications, and system messaging preferences</p>
                                <div class="mt-4 flex items-center text-red-600 dark:text-red-400 text-sm font-medium">
                                    Configure <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Terms & Conditions -->
                        <a href="{{ route('settings.terms.index') }}" class="block group">
                            <div class="bg-gradient-to-br from-indigo-50 to-blue-100 dark:from-indigo-900/20 dark:to-blue-900/20 p-6 rounded-xl border border-indigo-200 dark:border-indigo-700 hover:shadow-lg transition-all duration-200 group-hover:scale-105">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-indigo-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Terms & Conditions</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Manage legal documents, terms of service, policies, and user acceptance tracking</p>
                                <div class="mt-4 flex items-center text-indigo-600 dark:text-indigo-400 text-sm font-medium">
                                    Manage <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- API Documentation -->
                        {{-- <div class="bg-gradient-to-br from-gray-50 to-slate-100 dark:from-gray-900/20 dark:to-slate-900/20 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-gray-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                    API
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Settings API</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Access settings programmatically via REST endpoints</p>
                            <div class="space-y-2 text-xs">
                                <code class="block bg-gray-100 dark:bg-gray-800 p-2 rounded text-gray-800 dark:text-gray-200">GET /settings/api</code>
                                <code class="block bg-gray-100 dark:bg-gray-800 p-2 rounded text-gray-800 dark:text-gray-200">GET /settings/api/{category}</code>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Current Settings Summary -->
                    {{-- @if(!empty($currentSettings))
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Settings Overview</h2>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                @foreach($categories as $category => $label)
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($currentSettings[$category] ?? []) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>