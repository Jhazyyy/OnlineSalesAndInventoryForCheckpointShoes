<?php

namespace App\Console\Commands;

use App\Models\Supplier;
use Illuminate\Console\Command;

class GenerateSuppliers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:suppliers 
                            {count=10 : Number of suppliers to generate}
                            {--type= : Type of supplier (local, distributor, manufacturer, service_provider)}
                            {--active : Generate only active suppliers}
                            {--philippines : Generate only Philippine suppliers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake suppliers for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $type = $this->option('type');
        $activeOnly = $this->option('active');
        $philippinesOnly = $this->option('philippines');

        $this->info("Generating {$count} suppliers...");

        $factory = Supplier::factory()->count($count);

        // Apply type filter
        if ($type && in_array($type, ['local', 'distributor', 'manufacturer', 'service_provider'])) {
            $factory = $factory->{$type}();
            $this->info("Type: {$type}");
        }

        // Apply active filter
        if ($activeOnly) {
            $factory = $factory->active();
            $this->info("Status: Active only");
        }

        // Apply Philippines filter
        if ($philippinesOnly) {
            $factory = $factory->philippines();
            $this->info("Location: Philippines only");
        }

        $suppliers = $factory->create();

        $this->info("Successfully generated {$suppliers->count()} suppliers!");
        
        // Display some sample data
        $this->table(
            ['Name', 'Type', 'Phone', 'City', 'Country', 'Status'],
            $suppliers->take(5)->map(function ($supplier) {
                return [
                    $supplier->supplier_name,
                    ucfirst($supplier->supplier_type),
                    $supplier->phone,
                    $supplier->city,
                    $supplier->country,
                    ucfirst($supplier->status)
                ];
            })
        );

        if ($suppliers->count() > 5) {
            $this->info("... and " . ($suppliers->count() - 5) . " more suppliers.");
        }

        $totalSuppliers = Supplier::count();
        $this->info("Total suppliers in database: {$totalSuppliers}");
    }
}