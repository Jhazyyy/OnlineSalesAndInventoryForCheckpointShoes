@if($adjustments->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Movement Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity Change</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Before</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">After</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $adjustment->created_at->format('M d, Y') }}
                            <br>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $adjustment->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @php
                                $refType = $adjustment->reference_type ?? 'unknown';
                                $badgeClass = '';
                                $label = ucfirst(str_replace('_', ' ', $refType));
                                
                                switch($refType) {
                                    case 'sale':
                                    case 'sales_order':
                                        $badgeClass = 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100';
                                        $label = 'Sale';
                                        break;
                                    case 'purchase':
                                    case 'purchase_order':
                                    case 'purchase_receive':
                                        $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
                                        $label = 'Purchase';
                                        break;
                                    case 'manual_adjustment':
                                    case 'adjustment':
                                        $badgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
                                        $label = 'Adjustment';
                                        break;
                                    case 'return':
                                    case 'sales_return':
                                        $badgeClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
                                        $label = 'Return';
                                        break;
                                    case 'transfer':
                                        $badgeClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100';
                                        $label = 'Transfer';
                                        break;
                                    case 'waste':
                                        $badgeClass = 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
                                        $label = 'Waste';
                                        break;
                                    default:
                                        $badgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100';
                                }
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                            @if($adjustment->quantity_change > 0)
                                <span class="ml-1 text-xs text-green-600 dark:text-green-400">↑</span>
                            @else
                                <span class="ml-1 text-xs text-red-600 dark:text-red-400">↓</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            @if($adjustment->reference_id)
                                <span class="text-xs font-mono text-gray-600 dark:text-gray-400">#{{ $adjustment->reference_id }}</span>
                            @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                            @endif
                            @if($adjustment->reason)
                                <br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $adjustment->reason }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold">
                            @if($adjustment->quantity_change > 0)
                                <span class="text-green-600 dark:text-green-400">+{{ $adjustment->quantity_change }}</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">{{ $adjustment->quantity_change }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ number_format($adjustment->quantity_before) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ number_format($adjustment->quantity_after) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $adjustment->user->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            @if($adjustment->notes)
                                <div class="max-w-xs break-words" title="{{ $adjustment->notes }}">
                                    {{ $adjustment->notes }}
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($adjustments->hasPages())
        <div class="mt-4 px-4">
            {{ $adjustments->links() }}
        </div>
    @endif
@else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No movement history</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This product has no stock movement history yet.</p>
    </div>
@endif
