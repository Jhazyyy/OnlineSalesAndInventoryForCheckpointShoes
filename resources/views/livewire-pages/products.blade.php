<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Products (Livewire)</h2>
                    <p class="text-gray-600 dark:text-gray-400">Interactive product list with search and sorting.</p>
                </div>
            </div>
            <livewire:product-list />
        </div>
    </div>
    @push('page-scripts')
    @endpush
</x-app-layout>
