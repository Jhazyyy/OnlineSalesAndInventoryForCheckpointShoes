<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'stock_code', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Code
                        @if (request('sort') === 'stock_code')
                            <span class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Name
                        @if (request('sort') === 'name')
                            <span class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Description
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Products Count
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        Created
                        @if (request('sort') === 'created_at')
                            <span class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </a>
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($stockNames as $stockName)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $stockName->stock_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $stockName->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white break-words max-w-xs">
                            {{ $stockName->description ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900 dark:text-white">
                            {{ $stockName->products()->count() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($stockName->is_active)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Active
                            </span>
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $stockName->created_at ? $stockName->created_at->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('master_data.stock_names.show', $stockName) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600">
                            View
                        </a>
                        <a href="{{ route('master_data.stock_names.edit', $stockName) }}"
                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No stock names found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
