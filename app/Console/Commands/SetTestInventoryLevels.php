<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use App\Services\InventoryService;

class SetTestInventoryLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:set-inventory-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set test inventory levels for demonstration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inventory = Inventory::first();
        
        if ($inventory) {
            // Set some test values
            $inventory->quantity_on_hand = 45;
            $inventory->quantity_reserved = 5;
            $inventory->save();
            
            $this->info("Set inventory levels:");
            $this->line("  - Total Stock: 45");
            $this->line("  - Reserved: 5");
            $this->line("  - Available: 40");
        } else {
            $this->error("No inventory records found.");
        }

        return Command::SUCCESS;
    }
}
