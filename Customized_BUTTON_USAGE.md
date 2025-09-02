# Gray Button Component Usage

## Overview
The `gray-button` component is a reusable Blade component that provides consistent styling for secondary action buttons throughout the application.

## Component Location
`resources/views/components/gray-button.blade.php`

## Styling Classes Applied
```css
inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150
```

## Basic Usage Examples

### 1. Simple Button with Text
```blade
<x-gray-button>
    Cancel
</x-gray-button>
```

### 2. Button with Click Handler
```blade
<x-gray-button onclick="window.history.back()">
    Go Back
</x-gray-button>
```

### 3. Button with Navigation
```blade
<x-gray-button onclick="window.location.href='{{ route('dashboard') }}'">
    Back to Dashboard
</x-gray-button>
```

### 4. Button with Custom Attributes
```blade
<x-gray-button id="custom-btn" class="ml-4" data-action="close">
    Close
</x-gray-button>
```

### 5. Form Submit Button (Override Default Type)
```blade
<x-gray-button type="submit">
    Save Draft
</x-gray-button>
```

### 6. Button with Icon
```blade
<x-gray-button>
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
    Back
</x-gray-button>
```

### 7. Disabled Button
```blade
<x-gray-button disabled>
    Processing...
</x-gray-button>
```

## Advanced Usage

### With Alpine.js
```blade
<x-gray-button x-data @click="showModal = false">
    Cancel
</x-gray-button>
```

### With Laravel Route Helper
```blade
<x-gray-button onclick="window.location.href='{{ route('inventory.stock.index') }}'">
    Back to Stock Management
</x-gray-button>
```

### With Confirmation Dialog
```blade
<x-gray-button onclick="if(confirm('Are you sure?')) { window.location.href='{{ route('some.route') }}' }">
    Delete
</x-gray-button>
```

## Comparison with Other Button Components

| Component | Purpose | Background Color |
|-----------|---------|------------------|
| `x-primary-button` | Main actions | `bg-gray-800` |
| `x-secondary-button` | Secondary actions | `bg-white` with border |
| `x-gray-button` | Neutral/Cancel actions | `bg-gray-600` |
| `x-danger-button` | Destructive actions | `bg-red-600` |

## When to Use

✅ **Use `x-gray-button` for:**
- Cancel buttons
- Back/navigation buttons
- Neutral secondary actions
- Close dialogs/modals
- Reset form buttons

❌ **Don't use `x-gray-button` for:**
- Primary form submissions (use `x-primary-button`)
- Destructive actions (use `x-danger-button`)
- Actions requiring high visibility (use `x-primary-button`)

## Customization

The component accepts all standard button attributes through Laravel's `$attributes` merge functionality:

```blade
<x-gray-button 
    class="w-full" 
    data-test="cancel-button" 
    aria-label="Cancel current operation"
    @click="handleCancel">
    Cancel Operation
</x-gray-button>
```

## Examples in Stock Management Views

The component has been implemented in several stock management views:

1. **Stock Show View**: Back to List button
2. **Stock Edit View**: Cancel and Back to List buttons
3. **Stock Transfer View**: Cancel button
4. **Stock Waste View**: Cancel button

This ensures consistent styling and behavior across the stock adjustment module.
