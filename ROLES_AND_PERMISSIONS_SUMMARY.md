# Roles and Permissions Summary

## Overview
The system now includes 5 roles with different permission levels:

---

## 1. Super Admin (super_admin)
**Color Badge:** Red  
**Description:** Highest level access with all permissions including managing other admins

### Permissions:
- **All permissions in the system**
- Can manage other super admins and admins
- Full control over all modules

---

## 2. Admin (admin)
**Color Badge:** Purple  
**Description:** Full administrative access except managing super admins

### Permissions:
- ✅ User Management (view, create, edit, delete - except super admins)
- ✅ Product Management (view, create, edit, delete)
- ✅ Inventory Management (view, create, edit, delete, manage stock)
- ✅ Sales Management (view, create, edit, delete, process refunds)
- ✅ Customer Management (view, create, edit, delete)
- ✅ Supplier Management (view, create, edit, delete)
- ✅ Purchase Management (view, create, edit, delete)
- ✅ Reports (view, export)
- ✅ Settings (view, manage)
- ✅ Categories & Brands (view, manage)

---

## 3. Salesperson (salesperson)
**Color Badge:** Green  
**Description:** Focused on sales and customer interactions

### Permissions:
- ✅ Products (view only)
- ✅ Inventory (view only)
- ✅ Sales (view, create, edit)
- ✅ Customers (view, create, edit)
- ✅ Reports (view only)
- ✅ Categories & Brands (view only)

### Use Case:
Perfect for staff who handle:
- Creating and managing sales orders
- Customer interactions and registrations
- Checking product availability
- Viewing sales reports

---

## 4. Inventory Clerk (inventory_clerk)
**Color Badge:** Yellow  
**Description:** Focused on inventory and purchase management

### Permissions:
- ✅ Products (view, create, edit)
- ✅ Inventory (view, create, edit, manage stock)
- ✅ Suppliers (view, create, edit)
- ✅ Purchases (view, create, edit)
- ✅ Reports (view only)
- ✅ Categories & Brands (view only)

### Use Case:
Perfect for staff who handle:
- Stock management and adjustments
- Creating purchase orders
- Managing suppliers
- Product inventory tracking
- Receiving goods

---

## 5. User (user)
**Color Badge:** Blue  
**Description:** Basic access with limited permissions

### Permissions:
- ✅ Products (view only)
- ✅ Inventory (view only)
- ✅ Sales (view, create)
- ✅ Customers (view, create)
- ✅ Suppliers (view only)
- ✅ Reports (view only)
- ✅ Categories & Brands (view only)

### Use Case:
Default role for basic users with minimal system access

---

## Permission Categories

### User Management
- `view users` - View user list
- `create users` - Create new users
- `edit users` - Modify user information
- `delete users` - Remove users
- `manage admins` - Super Admin only permission

### Product Management
- `view products` - View product catalog
- `create products` - Add new products
- `edit products` - Modify product details
- `delete products` - Remove products

### Inventory Management
- `view inventory` - View inventory levels
- `create inventory` - Add inventory records
- `edit inventory` - Modify inventory data
- `delete inventory` - Remove inventory records
- `manage stock` - Adjust stock levels

### Sales Management
- `view sales` - View sales orders
- `create sales` - Create new sales orders
- `edit sales` - Modify sales orders
- `delete sales` - Remove sales orders
- `process refunds` - Handle refund requests

### Customer Management
- `view customers` - View customer list
- `create customers` - Add new customers
- `edit customers` - Modify customer information
- `delete customers` - Remove customers

### Supplier Management
- `view suppliers` - View supplier list
- `create suppliers` - Add new suppliers
- `edit suppliers` - Modify supplier details
- `delete suppliers` - Remove suppliers

### Purchase Management
- `view purchases` - View purchase orders
- `create purchases` - Create purchase orders
- `edit purchases` - Modify purchase orders
- `delete purchases` - Remove purchase orders

### Reports
- `view reports` - Access reporting system
- `export reports` - Export report data

### Settings
- `view settings` - View system settings
- `manage settings` - Modify system settings

### Categories & Brands
- `view categories` - View product categories
- `manage categories` - Modify categories
- `view brands` - View product brands
- `manage brands` - Modify brands

---

## Role Assignment

### Who Can Assign Roles:
- **Super Admin** can assign any role (super_admin, admin, salesperson, inventory_clerk, user)
- **Admin** can assign: admin, salesperson, inventory_clerk, user (cannot create super admins)
- **Other roles** can only create basic users

### Role Color Codes:
| Role | Color | Badge |
|------|-------|-------|
| Super Admin | Red | 🔴 |
| Admin | Purple | 🟣 |
| Salesperson | Green | 🟢 |
| Inventory Clerk | Yellow | 🟡 |
| User | Blue | 🔵 |

---

## Implementation Notes

### Files Modified:
1. `database/seeders/RolesAndPermissionsSeeder.php` - Added salesperson and inventory_clerk roles
2. `app/Http/Controllers/UserManagementController.php` - Updated to support new roles
3. `app/Models/User.php` - Added color badges for new roles

### Database Tables:
- `roles` - Stores role definitions
- `permissions` - Stores permission definitions
- `role_has_permissions` - Links roles to permissions
- `model_has_roles` - Assigns roles to users

### Package Used:
- **Spatie Laravel Permission** - Handles role and permission management
