<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Name Management</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage parent product stock names</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('master_data.stock_names.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Stock Name
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2"
                x-data="{
                    search: '{{ request('search') }}',
                    status: '{{ request('status') }}',
                    loading: false,
                    searchTimeout: null,
                    performSearch() {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            this.fetchResults();
                        }, 100);
                    },
                    fetchResults() {
                        this.loading = true;
                        const params = new URLSearchParams();
                        if (this.search) params.append('search', this.search);
                        if (this.status !== '') params.append('status', this.status);
                        params.append('sort', '{{ request('sort', 'name') }}');
                        params.append('order', '{{ request('order', 'asc') }}');
                        
                        fetch('{{ route('master_data.stock_names.index') }}?' + params.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('stockNamesTable').innerHTML = data.html;
                            document.getElementById('stockNamesPagination').innerHTML = data.pagination;
                            this.loading = false;
                            
                            // Update URL without page reload
                            const newUrl = '{{ route('master_data.stock_names.index') }}?' + params.toString();
                            window.history.pushState({}, '', newUrl);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.loading = false;
                        });
                    },
                    clearFilters() {
                        this.search = '';
                        this.status = '';
                        window.location.href = '{{ route('master_data.stock_names.index') }}';
                    }
                }">
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <div class="relative">
                                    <input type="text" 
                                        id="search" 
                                        x-model="search"
                                        @input="performSearch()"
                                        placeholder="Search by name, code, or description..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <div x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" 
                                    x-model="status"
                                    @change="fetchResults()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="button"
                                @click="clearFilters()"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{-- <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg> --}}
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Names Table -->
            <div class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="stockNamesTable">
                        @include('master_data.stock_names.partials.table', ['stockNames' => $stockNames])
                    </div>

                    <!-- Pagination -->
                    <div id="stockNamesPagination" class="mt-4">
                        {{ $stockNames->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
