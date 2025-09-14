<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center space-x-4">
                            @if($customer->avatar)
                                <img src="{{ asset('storage/' . $customer->avatar) }}" 
                                     alt="{{ $customer->full_name }}" 
                                     class="h-16 w-16 object-cover rounded-full">
                            @else
                                <div class="h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-xl font-bold text-gray-600 dark:text-gray-300">
                                        {{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->display_name }}</h2>
                                <div class="flex items-center space-x-4 mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->customer_type === 'business' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($customer->customer_type) }}
                                    </span>
                                    @if($customer->is_vip)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            VIP Customer
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <form method="POST" action="{{ route('sales.customers.toggle-status', $customer) }}" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <a href="{{ route('sales.customers.edit', $customer) }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Customer
                            </a>
                            <a href="{{ route('sales.customers.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Customer Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->full_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:text-blue-500">
                                            {{ $customer->email }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($customer->phone)
                                            <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:text-blue-500">
                                                {{ $customer->phone }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Not provided</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Age</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($customer->date_of_birth)
                                            {{ $customer->age }} years old
                                            <span class="text-gray-500">({{ $customer->date_of_birth->format('M d, Y') }})</span>
                                        @else
                                            <span class="text-gray-400">Date of birth not provided</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Information (if applicable) -->
                    @if($customer->customer_type === 'business')
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Company Name</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->company_name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tax ID / VAT Number</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->tax_id ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Address Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address Information</h3>
                            <div class="grid grid-cols-1 gap-4">
                                @if($customer->address || $customer->city || $customer->state || $customer->postal_code || $customer->country)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Address</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            @if($customer->address)
                                                <div>{{ $customer->address }}</div>
                                            @endif
                                            <div>{{ $customer->full_address }}</div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-400 text-sm">No address information provided</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($customer->notes)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes</h3>
                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Recent Sales/Orders would go here -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Orders</h3>
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders yet</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Orders will appear here when the customer makes purchases.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Statistics and Quick Info -->
                <div class="space-y-6">
                    <!-- Customer Statistics -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Statistics</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spent</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">₱{{ number_format($customer->total_spent, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $customer->total_orders }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Order Value</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">₱{{ number_format($customer->average_order_value, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Order</span>
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        @if($customer->last_order_date)
                                            {{ $customer->last_order_date->format('M d, Y') }}
                                        @else
                                            <span class="text-gray-400">Never</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Since</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $customer->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="mailto:{{ $customer->email }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Send Email
                                </a>
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        Call Customer
                                    </a>
                                @endif
                                <a href="{{ route('sales.orders.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create Order
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Timeline -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Activity Timeline</h3>
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div>
                                                        <div class="text-sm">
                                                            <span class="font-medium text-gray-900 dark:text-white">Customer registered</span>
                                                        </div>
                                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $customer->created_at->format('M d, Y \a\t g:i A') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @if($customer->updated_at != $customer->created_at)
                                        <li>
                                            <div class="relative pb-8">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div>
                                                            <div class="text-sm">
                                                                <span class="font-medium text-gray-900 dark:text-white">Profile updated</span>
                                                            </div>
                                                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $customer->updated_at->format('M d, Y \a\t g:i A') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
