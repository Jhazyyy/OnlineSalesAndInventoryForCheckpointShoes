<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductsImport
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
            if (count($row) >= 4) { // Ensure minimum required columns
                try {
                    Product::create([
                        'product_name' => $row[0] ?? '',
                        'product_brand' => $row[1] ?? '',
                        'product_category' => $row[2] ?? '',
                        'quantity' => (int)($row[3] ?? 0),
                        'price' => (float)($row[4] ?? 0),
                        'description' => $row[5] ?? null,
                    ]);
                    $this->rowCount++;
                } catch (\Exception $e) {
                    Log::error('Error importing product: ' . $e->getMessage(), $row);
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
}
