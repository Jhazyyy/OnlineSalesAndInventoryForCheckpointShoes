<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule inventory threshold checks
Schedule::command('inventory:check-thresholds --silent')
    ->everyThirtyMinutes()
    ->between('06:00', '22:00')
    ->weekdays()
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(config('app.admin_email', 'admin@example.com'));

// Daily comprehensive threshold check
Schedule::command('inventory:check-thresholds --force')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();

// Weekly threshold system health check
Schedule::command('inventory:check-thresholds --force')
    ->weeklyOn(1, '09:00') // Monday at 9 AM
    ->withoutOverlapping();
