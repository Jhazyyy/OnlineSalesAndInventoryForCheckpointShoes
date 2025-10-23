<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SettingsService;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Seed the application's default system settings.
     */
    public function run(): void
    {
        // Initialize defaults only if not already present
        $service = new SettingsService();
        $service->initializeDefaults();
    }
}
