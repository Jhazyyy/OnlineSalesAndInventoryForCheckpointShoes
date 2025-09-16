<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a mix of different types of suppliers
        
        // 15 Local suppliers (Philippine-based)
        Supplier::factory()
            ->count(15)
            ->local()
            ->active()
            ->create();

        // 10 International distributors
        Supplier::factory()
            ->count(10)
            ->distributor()
            ->active()
            ->create();

        // 8 Manufacturers with complete business info
        Supplier::factory()
            ->count(8)
            ->manufacturer()
            ->withBusinessInfo()
            ->active()
            ->create();

        // 5 Service providers
        Supplier::factory()
            ->count(5)
            ->serviceProvider()
            ->active()
            ->create();

        // 3 Philippine suppliers (mixed types)
        Supplier::factory()
            ->count(3)
            ->philippines()
            ->create();

        // 2 Inactive suppliers (for testing filtering)
        Supplier::factory()
            ->count(2)
            ->inactive()
            ->create();

        // Create some specific example suppliers for better demo data
        $specificSuppliers = [
            [
                'supplier_name' => 'Metro Manila Electronics Supply',
                'phone' => '+63 2 8123 4567',
                'email' => 'sales@manilaelectronics.ph',
                'supplier_type' => 'distributor',
                'address' => '1234 Rizal Avenue',
                'city' => 'Manila',
                'state' => 'Metro Manila',
                'postal_code' => '1000',
                'country' => 'Philippines',
                'tax_id' => '123-456-789',
                'payment_terms' => 'Net 30',
                'status' => 'active',
                'notes' => 'Reliable supplier for electronic components and gadgets. Has been our partner for over 5 years.',
            ],
            [
                'supplier_name' => 'Global Tech Manufacturing Inc.',
                'phone' => '+1 555 123 4567',
                'email' => 'orders@globaltech.com',
                'supplier_type' => 'manufacturer',
                'address' => '789 Industrial Blvd',
                'city' => 'San Francisco',
                'state' => 'California',
                'postal_code' => '94105',
                'country' => 'United States',
                'tax_id' => '987-654-321',
                'payment_terms' => '2/10 Net 30',
                'status' => 'active',
                'notes' => 'Large-scale manufacturer of computer hardware and accessories.',
            ],
            [
                'supplier_name' => 'Cebu Office Supplies Co.',
                'phone' => '+63 32 234 5678',
                'email' => 'info@cebuoffice.ph',
                'supplier_type' => 'local',
                'address' => '456 Colon Street',
                'city' => 'Cebu City',
                'state' => 'Cebu',
                'postal_code' => '6000',
                'country' => 'Philippines',
                'tax_id' => '456-789-012',
                'payment_terms' => 'Net 15',
                'status' => 'active',
                'notes' => 'Local supplier specializing in office supplies and stationery.',
            ],
            [
                'supplier_name' => 'Quick Delivery Services',
                'phone' => '+63 917 123 4567',
                'email' => 'dispatch@quickdelivery.ph',
                'supplier_type' => 'service_provider',
                'address' => '321 EDSA',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'postal_code' => '1100',
                'country' => 'Philippines',
                'tax_id' => '789-012-345',
                'payment_terms' => 'Upon Receipt',
                'status' => 'active',
                'notes' => 'Logistics and delivery service provider for our supply chain.',
            ],
        ];

        foreach ($specificSuppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        $this->command->info('Created ' . Supplier::count() . ' suppliers successfully!');
    }
}