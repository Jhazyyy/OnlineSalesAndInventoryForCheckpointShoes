<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tax/Discount Details</h2>
                            <p class="text-gray-600 dark:text-gray-400">View {{ ucfirst($taxDiscount->type) }}
                                information</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('master_data.tax_discounts.edit', $taxDiscount) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('master_data.tax_discounts.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to Tax/Discounts
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax/Discount Information -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Code:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $taxDiscount->code }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Name:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $taxDiscount->name }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Type:</span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $taxDiscount->type === 'tax' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($taxDiscount->type) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $taxDiscount->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $taxDiscount->isValid() ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Calculation Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Calculation Details
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Method:</span>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ ucfirst($taxDiscount->calculation_method) }}</p>
                                </div>
                                <div>
                                    <span
                                        class="text-sm font-medium text-gray-700 dark:text-gray-300">Rate/Amount:</span>
                                    <p class="text-gray-900 dark:text-white text-xl font-bold">
                                        {{ $taxDiscount->formatted_rate }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Compound:</span>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ $taxDiscount->is_compound ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($taxDiscount->description)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $taxDiscount->description }}</p>
                        </div>
                    @endif

                    <!-- Validity Period -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Validity Period</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Valid From:</span>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $taxDiscount->valid_from ? $taxDiscount->valid_from->format('M d, Y') : 'No start date' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Valid To:</span>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $taxDiscount->valid_to ? $taxDiscount->valid_to->format('M d, Y') : 'No end date' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Application Scope -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Scope</h3>
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Applies To:</span>
                            <p class="text-gray-900 dark:text-white">{{ ucfirst($taxDiscount->applies_to) }} Products
                            </p>
                        </div>

                        @if ($taxDiscount->applies_to === 'specific')
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if (count($categories) > 0)
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Applicable
                                            Categories:</span>
                                        <ul class="mt-2 list-disc list-inside text-gray-900 dark:text-white">
                                            @foreach ($categories as $category)
                                                <li>{{ $category->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (count($products) > 0)
                                    <div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Applicable
                                            Products:</span>
                                        <ul class="mt-2 list-disc list-inside text-gray-900 dark:text-white">
                                            @foreach ($products as $product)
                                                <li>{{ $product->product_name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Timestamps -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Created:</span>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $taxDiscount->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated:</span>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $taxDiscount->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Breakdown Calculator -->
            <div class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profit Breakdown Calculator
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Test how this {{ $taxDiscount->type }}
                        affects profit calculations</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cost Price
                                (₱)</label>
                            <input type="number" id="calc_cost_price" step="0.01" value="100"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selling Price
                                (₱)</label>
                            <input type="number" id="calc_selling_price" step="0.01" value="150"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                            <input type="number" id="calc_quantity" value="1" min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <button onclick="calculateBreakdown()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Calculate Breakdown
                    </button>

                    <div id="breakdown_result" class="mt-4" style="display: none;">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Results:</h4>
                            <div id="breakdown_content" class="space-y-2 text-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function calculateBreakdown() {
            const costPrice = parseFloat(document.getElementById('calc_cost_price').value) || 0;
            const sellingPrice = parseFloat(document.getElementById('calc_selling_price').value) || 0;
            const quantity = parseInt(document.getElementById('calc_quantity').value) || 1;

            fetch('{{ route('master_data.tax_discounts.profit-breakdown') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cost_price: costPrice,
                        selling_price: sellingPrice,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayBreakdown(data.breakdown);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function displayBreakdown(breakdown) {
            const content = document.getElementById('breakdown_content');
            let html = `
                <div class="text-gray-900 dark:text-white"><strong>Subtotal:</strong> ₱${breakdown.subtotal.toFixed(2)}</div>
                <div class="text-gray-900 dark:text-white"><strong>Total Cost:</strong> ₱${breakdown.total_cost.toFixed(2)}</div>
                <div class="text-gray-900 dark:text-white"><strong>Gross Profit:</strong> ₱${breakdown.gross_profit.toFixed(2)} (${breakdown.gross_profit_percentage}%)</div>
                <div class="border-t border-gray-300 dark:border-gray-600 my-2"></div>
            `;

            if (breakdown.taxes.length > 0) {
                html += '<div class="text-blue-800 dark:text-blue-400"><strong>Taxes:</strong></div>';
                breakdown.taxes.forEach(tax => {
                    html +=
                        `<div class="ml-4 text-gray-700 dark:text-gray-300">${tax.name} (${tax.rate}%): ₱${tax.amount.toFixed(2)}</div>`;
                });
                html +=
                    `<div class="text-blue-800 dark:text-blue-400"><strong>Total Tax:</strong> ₱${breakdown.total_tax.toFixed(2)}</div>`;
            }

            if (breakdown.discounts.length > 0) {
                html += '<div class="text-green-800 dark:text-green-400"><strong>Discounts:</strong></div>';
                breakdown.discounts.forEach(discount => {
                    html +=
                        `<div class="ml-4 text-gray-700 dark:text-gray-300">${discount.name} (${discount.rate}%): -₱${discount.amount.toFixed(2)}</div>`;
                });
                html +=
                    `<div class="text-green-800 dark:text-green-400"><strong>Total Discount:</strong> -₱${breakdown.total_discount.toFixed(2)}</div>`;
            }

            html += `
                <div class="border-t border-gray-300 dark:border-gray-600 my-2"></div>
                <div class="text-lg font-bold text-gray-900 dark:text-white"><strong>Net Amount:</strong> ₱${breakdown.net_amount.toFixed(2)}</div>
                <div class="text-lg font-bold text-gray-900 dark:text-white"><strong>Net Profit:</strong> ₱${breakdown.net_profit.toFixed(2)} (${breakdown.net_profit_percentage}%)</div>
            `;

            content.innerHTML = html;
            document.getElementById('breakdown_result').style.display = 'block';
        }
    </script>
</x-app-layout>
