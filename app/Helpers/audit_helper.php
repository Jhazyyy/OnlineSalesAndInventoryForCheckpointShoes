<?php

use App\Models\AuditLog;

if (!function_exists('audit_log')) {
    /**
     * Helper function to create audit logs
     */
    function audit_log(
        string $action,
        string $module,
        string $description,
        ?string $recordType = null,
        ?int $recordId = null,
        ?string $recordIdentifier = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = AuditLog::SEVERITY_INFO
    ) {
        return AuditLog::logAction(
            $action,
            $module,
            $description,
            $recordType,
            $recordId,
            $recordIdentifier,
            $oldValues,
            $newValues,
            $severity
        );
    }
}

if (!function_exists('audit_inventory')) {
    /**
     * Log inventory-related actions
     */
    function audit_inventory(string $action, string $description, $product, ?array $oldValues = null, ?array $newValues = null, string $severity = AuditLog::SEVERITY_INFO)
    {
        return audit_log(
            $action,
            AuditLog::MODULE_INVENTORY,
            $description,
            get_class($product),
            $product->product_id ?? $product->id ?? null,
            $product->product_name ?? $product->name ?? null,
            $oldValues,
            $newValues,
            $severity
        );
    }
}

if (!function_exists('audit_sales')) {
    /**
     * Log sales-related actions
     */
    function audit_sales(string $action, string $description, $order, ?array $oldValues = null, ?array $newValues = null, string $severity = AuditLog::SEVERITY_INFO)
    {
        return audit_log(
            $action,
            AuditLog::MODULE_SALES,
            $description,
            get_class($order),
            $order->order_id ?? $order->id ?? null,
            $order->order_number ?? "Order #{$order->order_id}" ?? null,
            $oldValues,
            $newValues,
            $severity
        );
    }
}

if (!function_exists('audit_purchase')) {
    /**
     * Log purchase-related actions
     */
    function audit_purchase(string $action, string $description, $order, ?array $oldValues = null, ?array $newValues = null, string $severity = AuditLog::SEVERITY_INFO)
    {
        return audit_log(
            $action,
            AuditLog::MODULE_PURCHASES,
            $description,
            get_class($order),
            $order->order_id ?? $order->id ?? null,
            $order->po_number ?? "PO #{$order->order_id}" ?? null,
            $oldValues,
            $newValues,
            $severity
        );
    }
}

if (!function_exists('audit_user')) {
    /**
     * Log user-related actions
     */
    function audit_user(string $action, string $description, $user, ?array $oldValues = null, ?array $newValues = null, string $severity = AuditLog::SEVERITY_INFO)
    {
        return audit_log(
            $action,
            AuditLog::MODULE_USERS,
            $description,
            get_class($user),
            $user->id ?? null,
            $user->name ?? $user->email ?? null,
            $oldValues,
            $newValues,
            $severity
        );
    }
}

if (!function_exists('audit_auth')) {
    /**
     * Log authentication-related actions
     */
    function audit_auth(string $action, string $description, string $severity = AuditLog::SEVERITY_INFO)
    {
        return audit_log(
            $action,
            AuditLog::MODULE_AUTH,
            $description,
            null,
            null,
            null,
            null,
            null,
            $severity
        );
    }
}
