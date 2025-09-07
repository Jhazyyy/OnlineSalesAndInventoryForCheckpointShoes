<?php

namespace App\Imports;

use App\Models\Shipment;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShipmentsImport
{
    private $rowCount = 0;

    public function __construct()
    {
        $this->rowCount = 0;
    }

    public function import($file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        if ($handle === false) {
            throw new \Exception('Could not open file for reading');
        }

        $header = fgetcsv($handle); // Skip header row
        $this->rowCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) { // Ensure minimum required columns
                try {
                    // Map CSV columns to database fields
                    $data = [
                        'sales_order_number' => $row[0] ?? '',
                        'carrier' => $row[1] ?? '',
                        'service_type' => $row[2] ?? null,
                        'tracking_number' => $row[3] ?? null,
                        'priority' => $this->normalizePriority($row[4] ?? 'normal'),
                        'shipment_date' => $this->parseDate($row[5] ?? null),
                        'expected_delivery_date' => $this->parseDate($row[6] ?? null),
                        'recipient_name' => $row[7] ?? '',
                        'recipient_phone' => $row[8] ?? null,
                        'recipient_email' => $row[9] ?? null,
                        'shipping_address' => $row[10] ?? '',
                        'total_packages' => (int)($row[11] ?? 1),
                        'total_weight' => $this->parseDecimal($row[12] ?? null),
                        'shipping_cost' => $this->parseDecimal($row[13] ?? 0),
                        'insurance_cost' => $this->parseDecimal($row[14] ?? 0),
                        'additional_fees' => $this->parseDecimal($row[15] ?? 0),
                        'is_insured' => $this->parseBoolean($row[16] ?? 'no'),
                        'requires_signature' => $this->parseBoolean($row[17] ?? 'no'),
                        'is_fragile' => $this->parseBoolean($row[18] ?? 'no'),
                        'is_perishable' => $this->parseBoolean($row[19] ?? 'no'),
                        'special_instructions' => $row[20] ?? null,
                        'internal_notes' => $row[21] ?? null,
                        'status' => 'pending', // Default to pending
                    ];

                    // Find sales order by order number
                    $salesOrder = SalesOrder::where('order_number', $data['sales_order_number'])->first();
                    if (!$salesOrder) {
                        Log::warning('Shipment import: Sales order not found', [
                            'order_number' => $data['sales_order_number'],
                            'row' => $row
                        ]);
                        continue;
                    }

                    $data['sales_order_id'] = $salesOrder->order_id;

                    // Calculate total shipping cost
                    $data['total_shipping_cost'] = $data['shipping_cost'] + $data['insurance_cost'] + $data['additional_fees'];

                    // Validate the data
                    $validator = Validator::make($data, [
                        'sales_order_id' => 'required|exists:sales_orders,order_id',
                        'carrier' => 'required|string|max:255',
                        'service_type' => 'nullable|string|max:255',
                        'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number',
                        'priority' => 'required|in:low,normal,high,urgent',
                        'shipment_date' => 'required|date',
                        'expected_delivery_date' => 'nullable|date|after_or_equal:shipment_date',
                        'recipient_name' => 'required|string|max:255',
                        'recipient_phone' => 'nullable|string|max:20',
                        'recipient_email' => 'nullable|email|max:255',
                        'shipping_address' => 'required|string',
                        'total_packages' => 'required|integer|min:1',
                        'total_weight' => 'nullable|numeric|min:0',
                        'shipping_cost' => 'nullable|numeric|min:0',
                        'insurance_cost' => 'nullable|numeric|min:0',
                        'additional_fees' => 'nullable|numeric|min:0',
                        'is_insured' => 'boolean',
                        'requires_signature' => 'boolean',
                        'is_fragile' => 'boolean',
                        'is_perishable' => 'boolean',
                        'special_instructions' => 'nullable|string',
                        'internal_notes' => 'nullable|string',
                    ]);

                    if ($validator->passes()) {
                        // Remove null values and empty strings to avoid database issues
                        $cleanData = array_filter($data, function($value) {
                            return $value !== null && $value !== '';
                        });

                        // Ensure required fields are present
                        $cleanData['sales_order_id'] = $data['sales_order_id'];
                        $cleanData['carrier'] = $data['carrier'];
                        $cleanData['priority'] = $data['priority'];
                        $cleanData['shipment_date'] = $data['shipment_date'];
                        $cleanData['recipient_name'] = $data['recipient_name'];
                        $cleanData['shipping_address'] = $data['shipping_address'];
                        $cleanData['total_packages'] = $data['total_packages'];
                        $cleanData['status'] = $data['status'];
                        $cleanData['is_insured'] = $data['is_insured'];
                        $cleanData['requires_signature'] = $data['requires_signature'];
                        $cleanData['is_fragile'] = $data['is_fragile'];
                        $cleanData['is_perishable'] = $data['is_perishable'];
                        $cleanData['total_shipping_cost'] = $data['total_shipping_cost'];

                        // Remove the sales_order_number as it's not a database field
                        unset($cleanData['sales_order_number']);

                        Shipment::create($cleanData);
                        $this->rowCount++;
                    } else {
                        Log::warning('Shipment import validation failed', [
                            'row' => $row,
                            'errors' => $validator->errors()->toArray()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error importing shipment: ' . $e->getMessage(), $row);
                    // Continue with next row instead of failing completely
                }
            }
        }

        fclose($handle);
        return $this;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    private function parseDate($dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'Y-m-d H:i:s', 'm-d-Y'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try Carbon's flexible parsing
            $date = Carbon::parse($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Could not parse date: ' . $dateString);
            return null;
        }
    }

    private function parseDecimal($value): float
    {
        if (empty($value)) {
            return 0.00;
        }

        // Remove any currency symbols or commas
        $cleaned = preg_replace('/[^\d.-]/', '', $value);
        return (float) $cleaned;
    }

    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['yes', 'true', '1', 'on']);
    }

    private function normalizePriority($priority): string
    {
        $priority = strtolower(trim($priority));
        
        switch ($priority) {
            case 'low':
            case 'l':
                return 'low';
            case 'high':
            case 'h':
                return 'high';
            case 'urgent':
            case 'u':
            case 'critical':
                return 'urgent';
            default:
                return 'normal';
        }
    }
}
