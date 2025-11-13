<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Apply timezone and app name from settings (with safe fallbacks)
        // Helpers are autoloaded via composer.json (app/Helpers/settings_helper.php)
        try {
            $timezone = function_exists('setting') ? setting('general.timezone', 'Asia/Manila') : 'Asia/Manila';
            $appName = function_exists('setting') ? setting('general.company_name', config('app.name', 'Checkpoint')) : config('app.name', 'Checkpoint');
        } catch (\Throwable $e) {
            // In case DB isn't ready during early boot (migrate/seed), use fallbacks
            $timezone = 'Asia/Manila';
            $appName = config('app.name', 'Checkpoint');
        }

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);
        config(['app.name' => $appName]);

        // Register observers
        Product::observe(ProductObserver::class);

        // Register navigation helper as a Blade directive
        Blade::directive('navIcon', function ($expression) {
            return "<?php echo App\Helpers\NavigationHelper::getIcon($expression); ?>";
        });

        Blade::directive('isActiveRoute', function ($expression) {
            return "<?php echo App\Helpers\NavigationHelper::isActiveRoute($expression) ? 'true' : 'false'; ?>";
        });

        Blade::directive('activeClass', function ($expression) {
            return "<?php echo App\Helpers\NavigationHelper::getActiveClass($expression); ?>";
        });

        // Register view composer for notification count
        View::composer('layouts.navigation', \App\View\Composers\NotificationComposer::class);
    }
}
