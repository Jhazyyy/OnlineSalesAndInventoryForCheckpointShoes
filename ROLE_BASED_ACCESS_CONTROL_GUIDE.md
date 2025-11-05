# Role-Based Access Control Guide

This guide explains how to restrict views, buttons, and modules based on user roles in your Laravel application using Spatie Permission package.

## Table of Contents
1. [Blade Directives](#blade-directives)
2. [Route Protection](#route-protection)
3. [Controller Methods](#controller-methods)
4. [View Examples](#view-examples)
5. [Practical Examples](#practical-examples)

---

## 1. Blade Directives

### Basic Role Checks

#### `@role` / `@hasrole` - Check if user has a specific role
```blade
@hasrole('admin')
    <!-- This content is only visible to admins -->
    <button>Admin Only Button</button>
@endhasrole

@role('admin')
    <!-- Alternative syntax, same as @hasrole -->
    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
@endrole
```

#### `@hasanyrole` - Check if user has any of the specified roles
```blade
@hasanyrole('admin|manager')
    <!-- Visible to both admins and managers -->
    <button>Management Button</button>
@endhasanyrole
```

#### `@hasallroles` - Check if user has all specified roles
```blade
@hasallroles('admin|super-admin')
    <!-- Visible only if user has BOTH roles -->
    <button>Super Admin Button</button>
@endhasallroles
```

#### `@unlessrole` - Show content if user does NOT have the role
```blade
@unlessrole('admin')
    <!-- This content is hidden from admins -->
    <p>Limited view for regular users</p>
@endunlessrole
```

### Permission-Based Checks

#### `@can` / `@cannot` - Check specific permissions
```blade
@can('edit users')
    <button>Edit User</button>
@endcan

@cannot('delete users')
    <span>You don't have permission to delete users</span>
@endcannot
```

#### `@canany` - Check if user has any of the specified permissions
```blade
@canany(['edit users', 'delete users', 'create users'])
    <div class="user-management-panel">
        <!-- User management options -->
    </div>
@endcanany
```

---

## 2. Route Protection

### Protect Routes with Middleware

#### Single Role
```php
// Protect individual route
Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware(['role:admin']);

// Protect route group
Route::middleware(['role:admin'])->group(function () {
    Route::resource('user-management', UserManagementController::class);
    Route::get('/admin/settings', [AdminController::class, 'settings']);
});
```

#### Multiple Roles (OR logic)
```php
Route::middleware(['role:admin|manager'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});
```

#### Multiple Roles (AND logic - all required)
```php
Route::middleware(['role:admin', 'role:super-admin'])->group(function () {
    Route::get('/critical-settings', [SettingsController::class, 'critical']);
});
```

#### Permission-Based Protection
```php
Route::middleware(['permission:edit users'])->group(function () {
    Route::get('/users/edit', [UserController::class, 'edit']);
});

// Multiple permissions (OR)
Route::middleware(['permission:edit users|delete users'])->group(function () {
    Route::resource('users', UserController::class);
});
```

---

## 3. Controller Methods

### Check Roles in Controller

```php
use Illuminate\Support\Facades\Auth;

class YourController extends Controller
{
    public function index()
    {
        // Check if user has role
        if (Auth::user()->hasRole('admin')) {
            // Admin-specific logic
        }
        
        // Check if user has any role
        if (Auth::user()->hasAnyRole(['admin', 'manager'])) {
            // Admin or manager logic
        }
        
        // Check if user has all roles
        if (Auth::user()->hasAllRoles(['admin', 'super-admin'])) {
            // User must have both roles
        }
        
        return view('your.view');
    }
    
    // Authorize in constructor
    public function __construct()
    {
        $this->middleware('role:admin')->only(['destroy', 'edit']);
        $this->middleware('role:admin|manager')->except(['index', 'show']);
    }
    
    // Manual authorization
    public function delete($id)
    {
        // Throw 403 if user doesn't have role
        abort_unless(Auth::user()->hasRole('admin'), 403, 'Unauthorized action.');
        
        // Or redirect
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
    }
}
```

---

## 4. View Examples

### Sidebar Navigation (Already Implemented)
```blade
<!-- resources/views/components/sidebar-navigation.blade.php -->

<!-- User Management (Admin Only) -->
@hasrole('admin')
    <x-nav-item route="user-management.index" 
        route-pattern="user-management.*" 
        :icon="App\Helpers\NavigationHelper::getIcon('user_accounts_control', 'w-4 h-4 mr-3')"
        title="User Accounts Control" 
        size="large" />
@endhasrole

<!-- Another module for managers only -->
@hasrole('manager')
    <x-nav-item route="reports.index" 
        :icon="App\Helpers\NavigationHelper::getIcon('reports')" 
        title="Reports" />
@endhasrole

<!-- Module for both admin and manager -->
@hasanyrole('admin|manager')
    <x-nav-item route="analytics.index" 
        :icon="App\Helpers\NavigationHelper::getIcon('analytics')" 
        title="Analytics" />
@endhasanyrole
```

### Buttons in Views
```blade
<!-- Show button only to admins -->
@hasrole('admin')
    <button class="btn-delete" onclick="deleteUser()">
        Delete User
    </button>
@endhasrole

<!-- Show different buttons for different roles -->
@hasrole('admin')
    <a href="{{ route('users.edit', $user->id) }}" class="btn-primary">
        Full Edit Access
    </a>
@else
    <a href="{{ route('users.show', $user->id) }}" class="btn-secondary">
        View Only
    </a>
@endhasrole

<!-- Conditional display with else -->
@hasrole('admin')
    <button class="btn-danger">Delete</button>
@else
    <span class="text-gray-500">Deletion requires admin access</span>
@endhasrole
```

### Table Actions
```blade
<table>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>
                    <!-- Everyone can view -->
                    <a href="{{ route('users.show', $user->id) }}">View</a>
                    
                    <!-- Only admins can edit -->
                    @hasrole('admin')
                        <a href="{{ route('users.edit', $user->id) }}">Edit</a>
                    @endhasrole
                    
                    <!-- Only admins can delete -->
                    @hasrole('admin')
                        <form method="POST" action="{{ route('users.destroy', $user->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    @endhasrole
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Form Fields
```blade
<form>
    <!-- Basic fields for all users -->
    <input type="text" name="name" value="{{ $user->name }}">
    <input type="email" name="email" value="{{ $user->email }}">
    
    <!-- Admin-only fields -->
    @hasrole('admin')
        <div class="admin-section">
            <label>User Role</label>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            
            <label>
                <input type="checkbox" name="is_active" {{ $user->is_active ? 'checked' : '' }}>
                Active Account
            </label>
        </div>
    @endhasrole
    
    <button type="submit">Save</button>
</form>
```

---

## 5. Practical Examples

### Example 1: Complete Module Protection (Inventory Management)

**Routes (web.php)**
```php
// Only admins and managers can access inventory management
Route::middleware(['role:admin|manager'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/create', [InventoryController::class, 'create'])->name('create');
    Route::post('/', [InventoryController::class, 'store'])->name('store');
    
    // Only admins can delete
    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
    });
});
```

**Sidebar Navigation**
```blade
@hasanyrole('admin|manager')
    <x-nav-item route="inventory.index" 
        route-pattern="inventory.*"
        :icon="App\Helpers\NavigationHelper::getIcon('inventory')" 
        title="Inventory Management" 
        :is-dropdown="true">
        
        <x-nav-item route="inventory.index" title="View Inventory" size="small" />
        
        @hasanyrole('admin|manager')
            <x-nav-item route="inventory.create" title="Add Item" size="small" />
        @endhasanyrole
    </x-nav-item>
@endhasanyrole
```

**Index View (inventory/index.blade.php)**
```blade
<div class="header">
    <h1>Inventory Management</h1>
    
    @hasanyrole('admin|manager')
        <a href="{{ route('inventory.create') }}" class="btn-primary">
            Add New Item
        </a>
    @endhasanyrole
</div>

<table>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>
                <a href="{{ route('inventory.show', $item->id) }}">View</a>
                
                @hasanyrole('admin|manager')
                    <a href="{{ route('inventory.edit', $item->id) }}">Edit</a>
                @endhasanyrole
                
                @hasrole('admin')
                    <form method="POST" action="{{ route('inventory.destroy', $item->id) }}">
                        @csrf
                        @method('DELETE')
                        <button>Delete</button>
                    </form>
                @endhasrole
            </td>
        </tr>
    @endforeach
</table>
```

### Example 2: Financial Module (Admin Only)

**Routes**
```php
Route::middleware(['role:admin'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [FinanceController::class, 'reports'])->name('reports');
    Route::get('/salary', [FinanceController::class, 'salary'])->name('salary');
});
```

**Sidebar**
```blade
@hasrole('admin')
    <x-nav-item route-pattern="finance.*" 
        :icon="App\Helpers\NavigationHelper::getIcon('finance')" 
        title="Financial Management" 
        :is-dropdown="true">
        
        <x-nav-item route="finance.dashboard" title="Dashboard" size="small" />
        <x-nav-item route="finance.reports" title="Reports" size="small" />
        <x-nav-item route="finance.salary" title="Salary Management" size="small" />
    </x-nav-item>
@endhasrole
```

### Example 3: Reports (Different Access Levels)

**Routes**
```php
Route::prefix('reports')->name('reports.')->group(function () {
    // All authenticated users can view basic reports
    Route::get('/', [ReportController::class, 'index'])->name('index');
    
    // Managers and admins can view detailed reports
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::get('/detailed', [ReportController::class, 'detailed'])->name('detailed');
        Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
    });
    
    // Only admins can view financial reports
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/salary', [ReportController::class, 'salary'])->name('salary');
    });
});
```

**View**
```blade
<div class="reports-menu">
    <!-- Everyone sees this -->
    <a href="{{ route('reports.index') }}">Basic Reports</a>
    
    <!-- Managers and admins -->
    @hasanyrole('admin|manager')
        <a href="{{ route('reports.detailed') }}">Detailed Reports</a>
        <a href="{{ route('reports.analytics') }}">Analytics</a>
    @endhasanyrole
    
    <!-- Admin only -->
    @hasrole('admin')
        <a href="{{ route('reports.financial') }}">Financial Reports</a>
        <a href="{{ route('reports.salary') }}">Salary Reports</a>
    @endhasrole
</div>
```

---

## Quick Reference

### Blade Directives Cheat Sheet

| Directive | Description | Example |
|-----------|-------------|---------|
| `@hasrole('admin')` | Check single role | `@hasrole('admin') ... @endhasrole` |
| `@role('admin')` | Same as hasrole | `@role('admin') ... @endrole` |
| `@hasanyrole('admin\|manager')` | Check multiple roles (OR) | `@hasanyrole('admin\|manager') ... @endhasanyrole` |
| `@hasallroles('admin\|manager')` | Check multiple roles (AND) | `@hasallroles('admin\|manager') ... @endhasallroles` |
| `@unlessrole('admin')` | Inverse role check | `@unlessrole('admin') ... @endunlessrole` |
| `@can('permission')` | Check permission | `@can('edit users') ... @endcan` |
| `@cannot('permission')` | Inverse permission | `@cannot('edit users') ... @endcannot` |
| `@canany(['p1', 'p2'])` | Check multiple permissions | `@canany(['edit', 'delete']) ... @endcanany` |

### Route Middleware Cheat Sheet

```php
// Single role
Route::middleware(['role:admin'])

// Multiple roles (OR - user needs ANY of these)
Route::middleware(['role:admin|manager|supervisor'])

// Multiple roles (AND - user needs ALL of these)
Route::middleware(['role:admin', 'role:manager'])

// Single permission
Route::middleware(['permission:edit users'])

// Multiple permissions (OR)
Route::middleware(['permission:edit users|delete users'])

// Combine role and permission
Route::middleware(['role:admin', 'permission:edit users'])
```

---

## Best Practices

1. **Always protect routes with middleware** - Don't rely only on view-level restrictions
2. **Use consistent role names** - Stick to lowercase with hyphens (e.g., 'super-admin')
3. **Document your roles** - Keep a list of all roles and their permissions
4. **Test with different user roles** - Always test features with all user types
5. **Provide feedback** - Show users why they can't access something
6. **Use permission-based checks** for granular control
7. **Use role-based checks** for broader access control

---

## Common Patterns

### Pattern 1: Progressive Disclosure
```blade
<!-- Show basic info to everyone -->
<div class="user-info">
    <p>Name: {{ $user->name }}</p>
</div>

<!-- Show more info to managers -->
@hasanyrole('admin|manager')
    <div class="user-details">
        <p>Email: {{ $user->email }}</p>
        <p>Phone: {{ $user->phone }}</p>
    </div>
@endhasanyrole

<!-- Show sensitive info to admins only -->
@hasrole('admin')
    <div class="user-sensitive">
        <p>Salary: {{ $user->salary }}</p>
        <p>SSN: {{ $user->ssn }}</p>
    </div>
@endhasrole
```

### Pattern 2: Action Restriction
```blade
<div class="actions">
    <!-- Everyone can view -->
    <button onclick="viewItem()">View</button>
    
    <!-- Role-based actions -->
    @hasanyrole('admin|manager')
        <button onclick="editItem()">Edit</button>
    @endhasanyrole
    
    @hasrole('admin')
        <button onclick="deleteItem()" class="btn-danger">Delete</button>
    @endhasrole
</div>
```

### Pattern 3: Module Gating
```blade
@hasrole('admin')
    <x-nav-item route-pattern="financial.*" title="Financial Module" :is-dropdown="true">
        <x-nav-item route="financial.dashboard" title="Dashboard" />
        <x-nav-item route="financial.reports" title="Reports" />
    </x-nav-item>
@endhasrole
```

---

## Need Help?

- Check user's roles: `{{ Auth::user()->getRoleNames() }}`
- Check user's permissions: `{{ Auth::user()->getAllPermissions()->pluck('name') }}`
- Debug in controller: `dd(Auth::user()->roles, Auth::user()->permissions);`
