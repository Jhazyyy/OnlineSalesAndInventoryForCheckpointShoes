<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SettingsService
{
    /**
     * Get setting value with fallback to default
     */
    public function get($category, $key, $default = null)
    {
        return SystemSetting::getValue($category, $key, $default);
    }

    /**
     * Set a setting value
     */
    public function set($category, $key, $value, $dataType = 'string', $description = null)
    {
        return SystemSetting::setValue($category, $key, $value, $dataType, $description);
    }

    /**
     * Get all settings for a category
     */
    public function getCategory($category)
    {
        return SystemSetting::getByCategory($category);
    }

    /**
     * Update multiple settings at once
     */
    public function updateBatch($settings, $category = null)
    {
        $updated = [];

        foreach ($settings as $key => $data) {
            if (is_array($data)) {
                $settingCategory = $category ?? $data['category'] ?? 'general';
                $value = $data['value'] ?? null;
                $dataType = $data['data_type'] ?? 'string';
                $description = $data['description'] ?? null;
            } else {
                $settingCategory = $category ?? 'general';
                $value = $data;
                $dataType = 'string';
                $description = null;
            }

            $updated[$key] = $this->set($settingCategory, $key, $value, $dataType, $description);
        }

        return $updated;
    }

    /**
     * Get default business settings structure
     */
    public function getDefaultSettings()
    {
        return [
            'general' => [
                'company_logo' => [
                    'value' => '',
                    'data_type' => 'string',
                    'description' => 'Path to company logo image stored in public disk'
                ],
                'company_name' => [
                    'value' => 'Checkpoint',
                    'data_type' => 'string',
                    'description' => 'Company name displayed throughout the system'
                ],
                'company_address' => [
                    'value' => '',
                    'data_type' => 'string',
                    'description' => 'Company physical address'
                ],
                'company_phone' => [
                    'value' => '',
                    'data_type' => 'string',
                    'description' => 'Company contact phone number'
                ],
                'company_email' => [
                    'value' => '',
                    'data_type' => 'string',
                    'description' => 'Company contact email address'
                ],
                'timezone' => [
                    'value' => 'Asia/Manila',
                    'data_type' => 'string',
                    'description' => 'System timezone'
                ],
                'business_hours' => [
                    'value' => [
                        'monday' => ['open' => '09:00', 'close' => '18:00'],
                        'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                        'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                        'thursday' => ['open' => '09:00', 'close' => '18:00'],
                        'friday' => ['open' => '09:00', 'close' => '18:00'],
                        'saturday' => ['open' => '09:00', 'close' => '17:00'],
                        'sunday' => ['open' => '10:00', 'close' => '16:00']
                    ],
                    'data_type' => 'json',
                    'description' => 'Business operating hours'
                ]
            ],
            'financial' => [
                'default_currency' => [
                    'value' => 'PHP',
                    'data_type' => 'string',
                    'description' => 'Default currency code'
                ],
                'currency_symbol' => [
                    'value' => '₱',
                    'data_type' => 'string',
                    'description' => 'Currency symbol'
                ],
                'tax_rate' => [
                    'value' => 12.0,
                    'data_type' => 'number',
                    'description' => 'Default tax rate percentage'
                ],
                'tax_display' => [
                    'value' => 'inclusive',
                    'data_type' => 'string',
                    'description' => 'Tax display method (inclusive/exclusive)'
                ],
                'decimal_precision' => [
                    'value' => 2,
                    'data_type' => 'integer',
                    'description' => 'Number of decimal places for currency'
                ]
            ],
            'inventory' => [
                'low_stock_threshold' => [
                    'value' => 10,
                    'data_type' => 'integer',
                    'description' => 'Default low stock alert threshold'
                ],
                'critical_stock_level' => [
                    'value' => 5,
                    'data_type' => 'integer',
                    'description' => 'Critical stock level threshold'
                ],
                'auto_reorder_enabled' => [
                    'value' => false,
                    'data_type' => 'boolean',
                    'description' => 'Enable automatic reorder suggestions'
                ],
                'waste_tracking_enabled' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Enable waste and damage tracking'
                ],
                'negative_stock_allowed' => [
                    'value' => false,
                    'data_type' => 'boolean',
                    'description' => 'Allow negative stock levels'
                ]
            ],
            'sales' => [
                'order_prefix' => [
                    'value' => 'SO-',
                    'data_type' => 'string',
                    'description' => 'Sales order number prefix'
                ],
                'invoice_prefix' => [
                    'value' => 'INV-',
                    'data_type' => 'string',
                    'description' => 'Invoice number prefix'
                ],
                'return_policy_days' => [
                    'value' => 30,
                    'data_type' => 'integer',
                    'description' => 'Number of days for return policy'
                ],
                'exchange_policy_days' => [
                    'value' => 15,
                    'data_type' => 'integer',
                    'description' => 'Number of days for exchange policy'
                ],
                'require_receipt_for_return' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Require receipt for returns'
                ],
                'allow_defective_returns' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Allow returns for defective products'
                ]
            ],
            'notifications' => [
                'email_notifications_enabled' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Enable email notifications'
                ],
                'low_stock_alerts' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Send low stock alerts'
                ],
                'order_status_notifications' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Send order status update notifications'
                ],
                'payment_confirmations' => [
                    'value' => true,
                    'data_type' => 'boolean',
                    'description' => 'Send payment confirmation notifications'
                ],
                'admin_email' => [
                    'value' => '',
                    'data_type' => 'string',
                    'description' => 'Admin email for system notifications'
                ]
            ]
        ];
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        $defaults = $this->getDefaultSettings();
        
        foreach ($defaults as $category => $settings) {
            foreach ($settings as $key => $config) {
                // Only create if setting doesn't exist
                $existing = SystemSetting::where('category', $category)
                                         ->where('key', $key)
                                         ->first();
                
                if (!$existing) {
                    SystemSetting::create([
                        'category' => $category,
                        'key' => $key,
                        'value' => $config['value'],
                        'data_type' => $config['data_type'],
                        'description' => $config['description'],
                        'is_active' => true,
                        'is_public' => in_array($key, ['company_name', 'company_phone', 'business_hours'])
                    ]);
                }
            }
        }
    }

    /**
     * Validate settings data
     */
    public function validateSettings($category, $data)
    {
        $rules = $this->getValidationRules($category);
        
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Get validation rules for settings category
     */
    private function getValidationRules($category)
    {
        $rules = [
            'general' => [
                // The file upload is validated in controller; the stored value is a string path
                'company_logo' => 'nullable|string|max:255',
                'company_name' => 'required|string|max:100',
                'company_address' => 'nullable|string|max:500',
                'company_phone' => 'nullable|string|max:20',
                'company_email' => 'nullable|email|max:100',
                'timezone' => 'required|string|max:50',
                'business_hours' => 'nullable|array',
                'business_hours.*.open' => 'required|string|regex:/^\d{2}:\d{2}$/',
                'business_hours.*.close' => 'required|string|regex:/^\d{2}:\d{2}$/'
            ],
            'financial' => [
                'default_currency' => 'required|string|size:3',
                'currency_symbol' => 'required|string|max:5',
                'tax_rate' => 'required|numeric|min:0|max:100',
                'tax_display' => 'required|in:inclusive,exclusive',
                'decimal_precision' => 'required|integer|min:0|max:4'
            ],
            'inventory' => [
                'low_stock_threshold' => 'required|integer|min:0',
                'critical_stock_level' => 'required|integer|min:0',
                'auto_reorder_enabled' => 'required|boolean',
                'waste_tracking_enabled' => 'required|boolean',
                'negative_stock_allowed' => 'required|boolean'
            ],
            'sales' => [
                'order_prefix' => 'required|string|max:10',
                'invoice_prefix' => 'required|string|max:10',
                'return_policy_days' => 'required|integer|min:0|max:365',
                'exchange_policy_days' => 'required|integer|min:0|max:365',
                'require_receipt_for_return' => 'required|boolean',
                'allow_defective_returns' => 'required|boolean'
            ],
            'notifications' => [
                'email_notifications_enabled' => 'required|boolean',
                'low_stock_alerts' => 'required|boolean',
                'order_status_notifications' => 'required|boolean',
                'payment_confirmations' => 'required|boolean',
                'admin_email' => 'nullable|email|max:100'
            ]
        ];

        return $rules[$category] ?? [];
    }

    /**
     * Export settings to array
     */
    public function exportSettings()
    {
        return SystemSetting::all()
            ->groupBy('category')
            ->map(function ($settings) {
                return $settings->pluck('value', 'key')->toArray();
            })
            ->toArray();
    }

    /**
     * Clear all settings cache
     */
    public function clearCache()
    {
        SystemSetting::clearCache();
    }
}