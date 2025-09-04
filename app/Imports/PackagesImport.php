<?php

namespace App\Imports;

use App\Models\Package;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PackagesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Generate tracking code if not provided
        if (empty($row['tracking_code'])) {
            $row['tracking_code'] = Package::generateTrackingCode();
        }

        // Process contents if provided (JSON format)
        $contents = null;
        if (!empty($row['contents'])) {
            $contents = is_string($row['contents']) ? json_decode($row['contents'], true) : $row['contents'];
        }

        return new Package([
            'package_name' => $row['package_name'],
            'package_type' => $row['package_type'] ?? 'standard',
            'description' => $row['description'] ?? null,
            'weight' => !empty($row['weight']) ? (float) $row['weight'] : null,
            'dimensions' => $row['dimensions'] ?? null,
            'price' => (float) $row['price'],
            'quantity' => (int) $row['quantity'],
            'tracking_code' => $row['tracking_code'],
            'status' => $row['status'] ?? 'active',
            'contents' => $contents,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'package_name' => 'required|string|max:255',
            'package_type' => 'nullable|string|in:standard,custom,bundle',
            'description' => 'nullable|string|max:1000',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'tracking_code' => 'nullable|string|max:255|unique:packages,tracking_code',
            'status' => 'nullable|string|in:active,inactive,discontinued',
            'contents' => 'nullable|string', // JSON string format
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages(): array
    {
        return [
            'package_name.required' => 'Package name is required.',
            'package_name.max' => 'Package name must not exceed 255 characters.',
            'package_type.in' => 'Package type must be one of: standard, custom, bundle.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least 0.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a valid integer.',
            'quantity.min' => 'Quantity must be at least 0.',
            'weight.numeric' => 'Weight must be a valid number.',
            'weight.min' => 'Weight must be at least 0.',
            'tracking_code.unique' => 'This tracking code already exists.',
            'status.in' => 'Status must be one of: active, inactive, discontinued.',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * @return array
     */
    public function uniqueBy()
    {
        return 'tracking_code';
    }

    /**
     * Handle a row that failed validation or processing.
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            logger()->error('Package import failed', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }
}
