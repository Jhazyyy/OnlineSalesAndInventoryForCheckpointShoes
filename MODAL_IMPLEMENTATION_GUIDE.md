# Modal Implementation Guide

This guide will help you convert any Create, Edit, or View page into a modal dialog.

## Table of Contents
1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Step-by-Step Implementation](#step-by-step-implementation)
4. [Complete Examples](#complete-examples)
5. [Best Practices](#best-practices)

---

## Overview

Converting a page to a modal involves:
1. **Creating the modal HTML structure** in your index/list view
2. **Adding trigger button** to open the modal
3. **Adding JavaScript functions** to control modal behavior
4. **Updating controller** to handle AJAX requests (for edit modals)
5. **Handling form submission** via AJAX or standard POST

---

## Quick Start

### Basic Modal Structure Template

```blade
<!-- Modal Trigger Button -->
<button onclick="openYourModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
    Open Modal
</button>

<!-- Modal Container -->
<div id="yourModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Modal Title
                </h3>
                <button onclick="closeYourModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="yourForm" method="POST" action="">
                @csrf
                <div class="mt-4 space-y-4">
                    <!-- Your form fields here -->
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="closeYourModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openYourModal() {
        document.getElementById('yourModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeYourModal() {
        document.getElementById('yourModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
        document.getElementById('yourForm').reset(); // Reset form
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('yourModal');
            if (!modal.classList.contains('hidden')) {
                closeYourModal();
            }
        }
    });

    // Close on outside click
    document.getElementById('yourModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeYourModal();
        }
    });
</script>
```

---

## Step-by-Step Implementation

### STEP 1: Update the Index View (Add Modal Trigger)

**Before:**
```blade
<a href="{{ route('master_data.products.create') }}" class="btn btn-primary">
    New Product
</a>
```

**After:**
```blade
<button onclick="openCreateProductModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    New Product
</button>
```

### STEP 2: Create the Modal HTML Structure

Add this at the bottom of your index view, before the closing `</x-app-layout>` tag:

```blade
<!-- Create Product Modal -->
<div id="createProductModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Product
                </h3>
                <button onclick="closeCreateProductModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body (Copy form content from create.blade.php) -->
            <form id="createProductForm" method="POST" action="{{ route('master_data.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mt-4 space-y-4">
                    <!-- Paste your form fields here -->
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="closeCreateProductModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

### STEP 3: Add JavaScript Functions

Add this script section at the bottom of your index view:

```blade
<script>
    function openCreateProductModal() {
        document.getElementById('createProductModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateProductModal() {
        document.getElementById('createProductModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('createProductForm').reset();
    }

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('createProductModal');
            if (!modal.classList.contains('hidden')) {
                closeCreateProductModal();
            }
        }
    });

    // Close on outside click
    document.getElementById('createProductModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeCreateProductModal();
        }
    });
</script>
```

### STEP 4: Update Controller (For Edit Modals Only)

For edit modals, you need to return JSON data when requested via AJAX:

```php
public function edit(Product $product)
{
    // Check if request wants JSON (AJAX request)
    if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
        return response()->json([
            'product' => $product,
            'brands' => Brand::where('is_active', true)->pluck('name', 'name'),
            'categories' => Category::where('is_active', true)->pluck('name', 'name'),
        ]);
    }
    
    // Otherwise return normal view
    $brands = Brand::where('is_active', true)->pluck('name', 'name');
    $categories = Category::where('is_active', true)->pluck('name', 'name');
    return view('master_data.products.edit', compact('product', 'brands', 'categories'));
}
```

### STEP 5: Handle Dynamic Data Loading (Edit Modals)

For edit modals, you need to fetch and populate data:

```javascript
function openEditProductModal(productId) {
    // Show modal first
    document.getElementById('editProductModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Fetch product data
    fetch(`/master_data/products/${productId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update form action
        document.getElementById('editProductForm').action = `/master_data/products/${productId}`;
        
        // Populate form fields
        document.getElementById('modal_product_name').value = data.product.product_name;
        document.getElementById('modal_sku').value = data.product.sku || '';
        document.getElementById('modal_price').value = data.product.price;
        // ... populate other fields
        
        // Populate dropdowns
        const brandSelect = document.getElementById('modal_product_brand');
        brandSelect.innerHTML = '<option value="">Select a brand...</option>';
        Object.entries(data.brands).forEach(([key, value]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = value;
            option.selected = data.product.product_brand === key;
            brandSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load product data.');
        closeEditProductModal();
    });
}
```

---

## Complete Examples

### Example 1: Create Product Modal (Simple)

**In `products/index.blade.php`:**

```blade
<!-- Trigger Button in Header Section -->
<button onclick="openCreateProductModal()" 
    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    New Product
</button>

<!-- Modal at bottom of file -->
<div id="createProductModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <!-- Modal content here -->
</div>

<script>
    // Modal functions here
</script>
```

### Example 2: Edit Modal with Dynamic Loading

**In `inventory/thresholds/index.blade.php`:**

```blade
<!-- Trigger Button in Table -->
<button onclick="openEditThresholdModal({{ $product->product_id }})" 
    class="text-blue-600 hover:text-blue-900">
    Edit
</button>

<!-- Modal -->
<div id="editThresholdModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <form id="editThresholdForm" method="POST">
            @csrf
            @method('PUT')
            <!-- Form fields -->
        </form>
    </div>
</div>

<script>
    function openEditThresholdModal(productId) {
        document.getElementById('editThresholdModal').classList.remove('hidden');
        
        fetch(`/inventory/thresholds/${productId}/edit`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('editThresholdForm').action = `/inventory/thresholds/${productId}`;
            // Populate fields
            document.getElementById('modal_reorder_level').value = data.product.reorder_level || '';
        });
    }
    
    function closeEditThresholdModal() {
        document.getElementById('editThresholdModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
```

### Example 3: View/Show Modal (Read-only)

```blade
<!-- Trigger -->
<button onclick="openViewProductModal({{ $product->product_id }})" 
    class="text-green-600 hover:text-green-900">
    View Details
</button>

<!-- Modal -->
<div id="viewProductModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-xl font-semibold">Product Details</h3>
                <button onclick="closeViewProductModal()">✕</button>
            </div>
            
            <div id="productDetails" class="mt-4 space-y-4">
                <!-- Details will be loaded here -->
            </div>
            
            <div class="flex justify-end mt-6 pt-4 border-t">
                <button onclick="closeViewProductModal()" class="px-4 py-2 bg-gray-200 rounded-md">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openViewProductModal(productId) {
        document.getElementById('viewProductModal').classList.remove('hidden');
        
        fetch(`/master_data/products/${productId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('productDetails').innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Product Name:</strong> ${data.product_name}</div>
                    <div><strong>SKU:</strong> ${data.sku}</div>
                    <div><strong>Price:</strong> ₱${data.price}</div>
                    <div><strong>Brand:</strong> ${data.product_brand}</div>
                </div>
            `;
        });
    }
</script>
```

---

## Best Practices

### 1. Modal Sizing
- **Small Forms**: `w-1/2` or `max-w-md`
- **Medium Forms**: `w-2/3` or `max-w-2xl`
- **Large Forms**: `w-11/12 md:w-2/3 lg:w-3/4` or `max-w-4xl`

### 2. Accessibility
```blade
<!-- Add ARIA attributes -->
<div id="myModal" 
     class="hidden fixed inset-0..." 
     role="dialog" 
     aria-labelledby="modalTitle" 
     aria-modal="true">
    <h3 id="modalTitle">Modal Title</h3>
</div>
```

### 3. Form Validation
```javascript
document.getElementById('myForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate
    const formData = new FormData(this);
    if (!formData.get('product_name')) {
        alert('Product name is required');
        return;
    }
    
    // Submit via AJAX or allow normal submission
    this.submit();
});
```

### 4. Loading States
```javascript
function openEditModal(id) {
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    
    // Show loading
    modal.querySelector('.modal-body').innerHTML = '<div class="text-center py-4">Loading...</div>';
    
    fetch(`/resource/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            // Populate fields
        });
}
```

### 5. Error Handling
```javascript
fetch('/api/endpoint')
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        // Handle success
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        closeModal();
    });
```

### 6. Success Feedback
```javascript
document.getElementById('myForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload(); // or update table dynamically
        }
    });
});
```

### 7. Multiple Modals
```javascript
// Use unique IDs and function names
// Modal 1: createProductModal, openCreateProductModal()
// Modal 2: editProductModal, openEditProductModal()
// Modal 3: deleteConfirmModal, openDeleteConfirmModal()
```

---

## Common Issues and Solutions

### Issue: Modal not closing on outside click
**Solution:** Ensure event listener targets the modal container itself:
```javascript
document.getElementById('myModal').addEventListener('click', function(event) {
    if (event.target === this) { // Check if clicked on backdrop, not children
        closeModal();
    }
});
```

### Issue: Form not submitting
**Solution:** Check form has proper action and method:
```blade
<form id="myForm" method="POST" action="{{ route('resource.store') }}">
    @csrf
    <!-- fields -->
</form>
```

### Issue: Data not loading in edit modal
**Solution:** Verify controller returns JSON:
```php
if (request()->wantsJson()) {
    return response()->json(['product' => $product]);
}
```

### Issue: Scroll not prevented when modal is open
**Solution:** Add `document.body.style.overflow = 'hidden'` when opening.

---

## Summary Checklist

When creating a modal:
- [ ] Add modal HTML structure to index view
- [ ] Add trigger button
- [ ] Create `openModal()` function
- [ ] Create `closeModal()` function
- [ ] Add Escape key listener
- [ ] Add outside click listener
- [ ] Update controller (if edit modal)
- [ ] Add AJAX data loading (if edit modal)
- [ ] Test form submission
- [ ] Test validation
- [ ] Test on mobile/responsive

---

**End of Guide**
