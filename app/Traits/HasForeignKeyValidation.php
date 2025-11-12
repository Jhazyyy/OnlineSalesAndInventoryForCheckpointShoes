<?php

namespace App\Traits;

/**
 * Trait for validating foreign key relationships at application level.
 * 
 * This replaces database-level foreign key constraints with application-level validation,
 * providing the same data integrity while avoiding migration complexity.
 * 
 * Usage in Form Requests:
 * use HasForeignKeyValidation;
 * 
 * public function rules() {
 *     return array_merge([
 *         // your rules
 *     ], $this->foreignKeyRules());
 * }
 */
trait HasForeignKeyValidation
{
    /**
     * Get all foreign key validation rules.
     */
    protected function foreignKeyRules(): array
    {
        return [
            // Products
            'product_id' => 'nullable|exists:products,product_id',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,product_id',
            
            // Customers
            'customer_id' => 'nullable|exists:customers,customer_id',
            
            // Users
            'user_id' => 'nullable|exists:users,id',
            'created_by' => 'nullable|exists:users,id',
            'processed_by' => 'nullable|exists:users,id',
            
            // Suppliers
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            
            // Orders
            'order_id' => 'nullable|exists:sales_orders,order_id',
            'sales_order_id' => 'nullable|exists:sales_orders,order_id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,order_id',
            
            // Exchanges
            'exchange_id' => 'nullable|exists:exchanges,exchange_id',
            
            // Terms and Conditions
            'terms_id' => 'nullable|exists:terms_and_conditions,id',
            
            // Receives
            'receive_id' => 'nullable|exists:purchase_receives,receive_id',
            
            // Tax Discounts
            'tax_rule_id' => 'nullable|exists:tax_discounts,id',
            'discount_rule_id' => 'nullable|exists:tax_discounts,id',
        ];
    }

    /**
     * Get validation rules for specific foreign key.
     */
    protected function foreignKeyRule(string $field, bool $required = false): string
    {
        $rules = $this->foreignKeyRules();
        $rule = $rules[$field] ?? 'nullable';
        
        if ($required) {
            $rule = str_replace('nullable|', 'required|', $rule);
        }
        
        return $rule;
    }

    /**
     * Validate item arrays with foreign keys (for bulk operations).
     */
    protected function itemArrayRules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.order_id' => 'nullable|exists:sales_orders,order_id',
            'items.*.purchase_order_id' => 'nullable|exists:purchase_orders,order_id',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,item_id',
        ];
    }

    /**
     * Get custom error messages for foreign key validation.
     */
    protected function foreignKeyMessages(): array
    {
        return [
            'product_id.exists' => 'The selected product does not exist.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'user_id.exists' => 'The selected user does not exist.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'order_id.exists' => 'The selected order does not exist.',
            'sales_order_id.exists' => 'The selected sales order does not exist.',
            'purchase_order_id.exists' => 'The selected purchase order does not exist.',
            'exchange_id.exists' => 'The selected exchange does not exist.',
            'terms_id.exists' => 'The selected terms and conditions do not exist.',
            'receive_id.exists' => 'The selected receive does not exist.',
            'items.*.product_id.exists' => 'One or more selected products do not exist.',
            'items.*.order_id.exists' => 'One or more selected orders do not exist.',
        ];
    }
}
