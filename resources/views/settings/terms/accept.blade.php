<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Terms & Conditions Acceptance</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Please review and accept the following terms and conditions to continue using our system.
                    </p>
                </div>
            </div>

            <!-- Error Messages -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Terms to Accept -->
            <form action="{{ route('terms.accept') }}" method="POST" class="space-y-6">
                @csrf

                @foreach($pendingTerms as $terms)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $terms->title }}</h3>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $terms->type_label }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Version {{ $terms->version }}
                                        </span>
                                        @if($terms->effective_date)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                Effective: {{ $terms->effective_date->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <input type="checkbox" name="terms_ids[]" value="{{ $terms->id }}" required
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            </div>

                            <!-- Content -->
                            <div class="mt-4 max-h-96 overflow-y-auto border dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                                <div class="prose dark:prose-invert prose-sm max-w-none text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    {!! nl2br(e($terms->content)) !!}
                                </div>
                            </div>

                            <!-- View Full Link -->
                            <div class="mt-4 text-center">
                                <a href="{{ route('terms.public', $terms->slug) }}" target="_blank"
                                   class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    View full document in new tab →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Acceptance Statement -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="accept_all" name="accept_all" type="checkbox" required
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="accept_all" class="font-medium text-gray-900 dark:text-white">
                                I have read and agree to all the terms and conditions listed above
                            </label>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                By checking this box, you acknowledge that you have read, understood, and agree to be bound by these terms and conditions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                You must accept all terms to continue using the system.
                            </p>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Accept & Continue
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
