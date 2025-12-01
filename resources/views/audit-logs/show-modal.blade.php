<div class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Time</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $auditLog->created_at->format('M d, Y h:i:s A') }}
                    <span class="text-xs text-gray-500">({{ $auditLog->created_at->diffForHumans() }})</span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">User</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $auditLog->user_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Module</p>
                <p class="text-sm font-medium">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                        {{ ucwords($auditLog->module) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Action</p>
                <p class="text-sm font-medium">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $auditLog->action_color }}-100 text-{{ $auditLog->action_color }}-800 dark:bg-{{ $auditLog->action_color }}-800 dark:text-{{ $auditLog->action_color }}-100">
                        {{ ucwords($auditLog->action) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Severity</p>
                <p class="text-sm font-medium">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $auditLog->severity_color }}-100 text-{{ $auditLog->severity_color }}-800 dark:bg-{{ $auditLog->severity_color }}-800 dark:text-{{ $auditLog->severity_color }}-100">
                        {{ ucwords($auditLog->severity) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">IP Address</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $auditLog->ip_address ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h4>
        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            {{ $auditLog->description }}
        </p>
    </div>

    <!-- Record Information -->
    @if($auditLog->record_type || $auditLog->record_id || $auditLog->record_identifier)
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Record Information</h4>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-2">
                @if($auditLog->record_type)
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Type:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ class_basename($auditLog->record_type) }}</span>
                    </div>
                @endif
                @if($auditLog->record_id)
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">ID:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $auditLog->record_id }}</span>
                    </div>
                @endif
                @if($auditLog->record_identifier)
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Identifier:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $auditLog->record_identifier }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Changes (for update actions) -->
    @if($auditLog->action === 'update' && $auditLog->old_values && $auditLog->new_values)
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Changes</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Field</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Old Value</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($auditLog->new_values as $field => $newValue)
                            @if(isset($auditLog->old_values[$field]))
                                <tr>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                    <td class="px-4 py-2 text-sm text-red-600 dark:text-red-400">
                                        <code class="bg-red-50 dark:bg-red-900 px-2 py-1 rounded">{{ is_array($auditLog->old_values[$field]) ? json_encode($auditLog->old_values[$field]) : $auditLog->old_values[$field] }}</code>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-green-600 dark:text-green-400">
                                        <code class="bg-green-50 dark:bg-green-900 px-2 py-1 rounded">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</code>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Old Values (for delete actions) -->
    @if($auditLog->action === 'delete' && $auditLog->old_values)
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Deleted Data</h4>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    <!-- New Values (for create actions) -->
    {{-- @if($auditLog->action === 'create' && $auditLog->new_values)
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Created Data</h4>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif --}}

    <!-- User Agent -->
    @if($auditLog->user_agent)
        <div>
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">User Agent</h4>
            <p class="text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 break-all">
                {{ $auditLog->user_agent }}
            </p>
        </div>
    @endif
</div>
