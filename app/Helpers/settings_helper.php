<?php

use App\Models\SystemSetting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by category and key
     * 
     * @param string $key Format: 'category.key' or just 'key' (assumes 'general' category)
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    function setting($key, $default = null)
    {
        if (str_contains($key, '.')) {
            [$category, $settingKey] = explode('.', $key, 2);
        } else {
            $category = 'general';
            $settingKey = $key;
        }
        
        return SystemSetting::getValue($category, $settingKey, $default);
    }
}

if (!function_exists('updateSetting')) {
    /**
     * Update a setting value
     * 
     * @param string $key Format: 'category.key' or just 'key' (assumes 'general' category)
     * @param mixed $value New value
     * @param string $dataType Data type (string, number, boolean, json)
     * @return SystemSetting
     */
    function updateSetting($key, $value, $dataType = 'string')
    {
        if (str_contains($key, '.')) {
            [$category, $settingKey] = explode('.', $key, 2);
        } else {
            $category = 'general';
            $settingKey = $key;
        }
        
        return SystemSetting::setValue($category, $settingKey, $value, $dataType);
    }
}

if (!function_exists('getSettingsCategory')) {
    /**
     * Get all settings for a category
     * 
     * @param string $category
     * @return array
     */
    function getSettingsCategory($category)
    {
        return SystemSetting::getByCategory($category);
    }
}

if (!function_exists('companyName')) {
    /**
     * Get company name setting
     * 
     * @return string
     */
    function companyName()
    {
        return setting('general.company_name', 'Checkpoint');
    }
}

if (!function_exists('companyCurrency')) {
    /**
     * Get company currency setting
     * 
     * @return string
     */
    function companyCurrency()
    {
        return setting('financial.default_currency', 'PHP');
    }
}

if (!function_exists('currencySymbol')) {
    /**
     * Get currency symbol setting
     * 
     * @return string
     */
    function currencySymbol()
    {
        return setting('financial.currency_symbol', '₱');
    }
}

if (!function_exists('taxRate')) {
    /**
     * Get tax rate setting
     * 
     * @return float
     */
    function taxRate()
    {
        return (float) setting('financial.tax_rate', 12.0);
    }
}

if (!function_exists('lowStockThreshold')) {
    /**
     * Get low stock threshold setting
     * 
     * @return int
     */
    function lowStockThreshold()
    {
        return (int) setting('inventory.low_stock_threshold', 10);
    }
}

if (!function_exists('criticalStockLevel')) {
    /**
     * Get critical stock level setting
     * 
     * @return int
     */
    function criticalStockLevel()
    {
        return (int) setting('inventory.critical_stock_level', 5);
    }
}

if (!function_exists('returnPolicyDays')) {
    /**
     * Get return policy days setting
     * 
     * @return int
     */
    function returnPolicyDays()
    {
        return (int) setting('sales.return_policy_days', 30);
    }
}

if (!function_exists('exchangePolicyDays')) {
    /**
     * Get exchange policy days setting
     * 
     * @return int
     */
    function exchangePolicyDays()
    {
        return (int) setting('sales.exchange_policy_days', 15);
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format amount with currency symbol and precision
     * 
     * @param float $amount
     * @param bool $showSymbol
     * @return string
     */
    function formatCurrency($amount, $showSymbol = true)
    {
        $symbol = $showSymbol ? currencySymbol() : '';
        $precision = (int) setting('financial.decimal_precision', 2);
        
        return $symbol . number_format($amount, $precision);
    }
}

if (!function_exists('isBusinessOpen')) {
    /**
     * Check if business is currently open based on business hours setting
     * 
     * @return bool
     */
    function isBusinessOpen()
    {
        $businessHours = setting('general.business_hours', []);
        
        if (empty($businessHours)) {
            return true; // If no hours set, assume always open
        }
        
        $dayOfWeek = strtolower(date('l')); // monday, tuesday, etc.
        $currentTime = date('H:i');
        
        if (!isset($businessHours[$dayOfWeek])) {
            return false;
        }
        
        $hours = $businessHours[$dayOfWeek];
        
        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }
}

if (!function_exists('getOrderPrefix')) {
    /**
     * Get sales order prefix
     * 
     * @return string
     */
    function getOrderPrefix()
    {
        return setting('sales.order_prefix', 'SO-');
    }
}

if (!function_exists('getInvoicePrefix')) {
    /**
     * Get invoice prefix
     * 
     * @return string
     */
    function getInvoicePrefix()
    {
        return setting('sales.invoice_prefix', 'INV-');
    }
}