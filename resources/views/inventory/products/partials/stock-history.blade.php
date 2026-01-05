@if($movements->isEmpty())
    <div class="text-center py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No stock movements</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This product has no recorded stock movements yet.</p>
    </div>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Change</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Before</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">After</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reason/Notes</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">By</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($movements as $movement)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $movement->created_at->format('M d, Y') }}
                            <div class="text-xs text-gray-400">{{ $movement->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $typeColors = [
                                    'sale' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                    'purchase' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'return' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                    'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'transfer_in' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'transfer_out' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'waste' => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
                                    'initial_stock' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                ];
                                $colorClass = $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                                {{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($movement->quantity > 0)
                                <span class="text-green-600 dark:text-green-400 font-semibold">+{{ $movement->quantity }}</span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-semibold">{{ $movement->quantity }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ $movement->quantity_before ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white">
                            {{ $movement->quantity_after ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="{{ $movement->notes ?? $movement->reason ?? '-' }}">
                            {{ $movement->notes ?? $movement->reason ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $movement->user->name ?? 'System' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
        Showing {{ $movements->count() }} most recent movements
    </div>
@endif
