<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display the settings dashboard
     */
    public function index()
    {
        $categories = [
            'general' => 'General Settings',
            'financial' => 'Financial Settings',
            'inventory' => 'Inventory Settings',
            'sales' => 'Sales Settings',
            'notifications' => 'Notification Settings'
        ];

        // Get current settings for each category
        $currentSettings = [];
        foreach (array_keys($categories) as $category) {
            $currentSettings[$category] = $this->settingsService->getCategory($category);
        }

        return view('settings.index', compact('categories', 'currentSettings'));
    }

    /**
     * Show general settings form
     */
    public function general()
    {
        $settings = $this->settingsService->getCategory('general');
        $defaults = $this->settingsService->getDefaultSettings()['general'];
        
        return view('settings.general', compact('settings', 'defaults'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        try {
            // Validate non-file settings first
            $validatedData = $this->settingsService->validateSettings('general', $request->except(['company_logo', 'remove_logo']));

            // Handle logo removal
            if ($request->input('remove_logo') == '1') {
                // Delete old logo file if exists
                $oldPath = $this->settingsService->get('general', 'company_logo');
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                
                // Set logo to empty string (will show default)
                $validatedData['company_logo'] = '';
            }
            // Handle logo upload
            elseif ($request->hasFile('company_logo')) {
                $request->validate([
                    'company_logo' => 'image|mimes:jpeg,png,gif,webp,svg|max:2048|dimensions:max_width=836,max_height=836',
                ]);

                $file = $request->file('company_logo');

                // Delete old logo if exists
                $oldPath = $this->settingsService->get('general', 'company_logo');
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                // Store new logo in public disk under logos/
                $path = $file->store('logos', 'public');
                $validatedData['company_logo'] = $path;
            }
            
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData as $key => $value) {
                    $dataType = $this->getDataType($key, $value);
                    
                    // Log for debugging
                    Log::info("Saving setting: {$key}", [
                        'value' => $value,
                        'dataType' => $dataType,
                        'is_array' => is_array($value)
                    ]);
                    
                    $this->settingsService->set('general', $key, $value, $dataType);
                }
            });

            // Clear the settings cache to ensure fresh data is loaded
            \Illuminate\Support\Facades\Cache::forget('settings_category_general');

            return redirect()->route('settings.general')
                           ->with('success', 'General settings updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show financial settings form
     */
    public function financial()
    {
        $settings = $this->settingsService->getCategory('financial');
        $defaults = $this->settingsService->getDefaultSettings()['financial'];
        
        return view('settings.financial', compact('settings', 'defaults'));
    }

    /**
     * Update financial settings
     */
    public function updateFinancial(Request $request)
    {
        try {
            $validatedData = $this->settingsService->validateSettings('financial', $request->all());
            
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData as $key => $value) {
                    $dataType = $this->getDataType($key, $value);
                    $this->settingsService->set('financial', $key, $value, $dataType);
                }
            });

            return redirect()->route('settings.financial')
                           ->with('success', 'Financial settings updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Show inventory settings form
     */
    public function inventory()
    {
        $settings = $this->settingsService->getCategory('inventory');
        $defaults = $this->settingsService->getDefaultSettings()['inventory'];
        
        return view('settings.inventory', compact('settings', 'defaults'));
    }

    /**
     * Update inventory settings
     */
    public function updateInventory(Request $request)
    {
        try {
            $data = $request->all();
            
            // Handle checkbox values that aren't submitted when unchecked
            $checkboxes = ['auto_reorder_enabled', 'waste_tracking_enabled', 'negative_stock_allowed', 'apply_to_all_products'];
            foreach ($checkboxes as $checkbox) {
                if (!isset($data[$checkbox])) {
                    $data[$checkbox] = false;
                }
            }
            
            // Extract the apply_to_all_products flag before validation
            $applyToAllProducts = $data['apply_to_all_products'] ?? false;
            unset($data['apply_to_all_products']); // Remove from data to avoid validation issues
            
            $validatedData = $this->settingsService->validateSettings('inventory', $data);
            
            DB::transaction(function () use ($validatedData, $applyToAllProducts) {
                // Save settings
                foreach ($validatedData as $key => $value) {
                    $dataType = $this->getDataType($key, $value);
                    $this->settingsService->set('inventory', $key, $value, $dataType);
                }
                
                // Apply thresholds to all products if checkbox is checked
                if ($applyToAllProducts) {
                    $lowStockThreshold = $validatedData['low_stock_threshold'] ?? 10;
                    $criticalStockLevel = $validatedData['critical_stock_level'] ?? 5;
                    
                    // Update all products with threshold fields enabled
                    \App\Models\Product::query()->update([
                        'reorder_level' => $lowStockThreshold,
                        'critical_level' => $criticalStockLevel,
                        'threshold_alerts_enabled' => true,
                        'updated_at' => now()
                    ]);
                }
            });

            $message = 'Inventory settings updated successfully.';
            if ($applyToAllProducts) {
                $productCount = \App\Models\Product::count();
                $message .= " Thresholds have been applied to {$productCount} products.";
            }

            return redirect()->route('settings.inventory')
                           ->with('success', $message);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Show sales settings form
     */
    public function sales()
    {
        $settings = $this->settingsService->getCategory('sales');
        $defaults = $this->settingsService->getDefaultSettings()['sales'];
        
        return view('settings.sales', compact('settings', 'defaults'));
    }

    /**
     * Update sales settings
     */
    public function updateSales(Request $request)
    {
        try {
            $data = $request->all();
            
            // Handle checkbox values that aren't submitted when unchecked
            $checkboxes = ['require_receipt_for_return', 'allow_defective_returns'];
            foreach ($checkboxes as $checkbox) {
                if (!isset($data[$checkbox])) {
                    $data[$checkbox] = false;
                }
            }
            
            $validatedData = $this->settingsService->validateSettings('sales', $data);
            
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData as $key => $value) {
                    $dataType = $this->getDataType($key, $value);
                    $this->settingsService->set('sales', $key, $value, $dataType);
                }
            });

            return redirect()->route('settings.sales')
                           ->with('success', 'Sales settings updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Show notification settings form
     */
    public function notifications()
    {
        $settings = $this->settingsService->getCategory('notifications');
        $defaults = $this->settingsService->getDefaultSettings()['notifications'];
        
        return view('settings.notifications', compact('settings', 'defaults'));
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        try {
            $data = $request->all();
            
            // Handle checkbox values that aren't submitted when unchecked
            $checkboxes = ['email_notifications_enabled', 'low_stock_alerts', 'order_status_notifications', 'payment_confirmations'];
            foreach ($checkboxes as $checkbox) {
                if (!isset($data[$checkbox])) {
                    $data[$checkbox] = false;
                }
            }
            
            $validatedData = $this->settingsService->validateSettings('notifications', $data);
            
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData as $key => $value) {
                    $dataType = $this->getDataType($key, $value);
                    $this->settingsService->set('notifications', $key, $value, $dataType);
                }
            });

            return redirect()->route('settings.notifications')
                           ->with('success', 'Notification settings updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        try {
            $this->settingsService->initializeDefaults();
            
            return redirect()->route('settings.index')
                           ->with('success', 'Default settings initialized successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to initialize default settings: ' . $e->getMessage());
        }
    }

    /**
     * Export settings
     */
    public function export()
    {
        try {
            $settings = $this->settingsService->exportSettings();
            
            return response()->json($settings)
                           ->header('Content-Disposition', 'attachment; filename="checkpoint-settings-' . date('Y-m-d') . '.json"');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        try {
            $this->settingsService->clearCache();
            
            return redirect()->route('settings.index')
                           ->with('success', 'Settings cache cleared successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get settings
     */
    public function getSettings($category = null)
    {
        try {
            if ($category) {
                $settings = $this->settingsService->getCategory($category);
            } else {
                $settings = $this->settingsService->exportSettings();
            }
            
            return response()->json($settings);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Determine data type based on key and value
     */
    private function getDataType($key, $value)
    {
        // Boolean fields
        if (in_array($key, [
            'auto_reorder_enabled', 'waste_tracking_enabled', 'negative_stock_allowed',
            'require_receipt_for_return', 'allow_defective_returns',
            'email_notifications_enabled', 'low_stock_alerts', 'order_status_notifications', 'payment_confirmations'
        ])) {
            return 'boolean';
        }

        // Numeric fields
        if (in_array($key, ['tax_rate'])) {
            return 'number';
        }

        // Integer fields
        if (in_array($key, [
            'decimal_precision', 'low_stock_threshold', 'critical_stock_level',
            'return_policy_days', 'exchange_policy_days'
        ])) {
            return 'integer';
        }

        // JSON fields
        if (in_array($key, ['business_hours'])) {
            return 'json';
        }

        // File path fields
        if (in_array($key, ['company_logo'])) {
            return 'string';
        }

        return 'string';
    }
}
