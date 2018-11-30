<?php

namespace App\Providers;

use iEducar\Modules\ErrorTracking\HoneyBadgerTracker;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register routes for fake auth using Laravel Dusk.
     *
     * @return void
     */
    private function registerRoutesForFakeAuth()
    {
        Route::get('/_dusk/legacy/login', [
            'middleware' => 'web',
            'uses' => 'App\Http\Controllers\LegacyFakeAuthController@doFakeLogin',
        ]);

        Route::get('/_dusk/legacy/logout', [
            'middleware' => 'web',
            'uses' => 'App\Http\Controllers\LegacyFakeAuthController@doFakeLogout',
        ]);
    }

    /**
     * Add custom methods in Browser class used by Laravel Dusk.
     *
     * @return void
     */
    private function customBrowserForFakeAuth()
    {
        Browser::macro('loginLegacy', function () {
            return $this->visit('/_dusk/legacy/login');
        });

        Browser::macro('logoutLegacy', function () {
            return $this->visit('/_dusk/legacy/logout');
        });
    }

    /**
     * Load migrations from other repositories or packages.
     *
     * @return void
     */
    private function loadLegacyMigrations()
    {
        foreach (config('legacy.migrations') as $path) {
            if (is_dir($path)) {
                $this->loadMigrationsFrom($path);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('development', 'dusk', 'local', 'testing')) {
            $this->registerRoutesForFakeAuth();
            $this->customBrowserForFakeAuth();
        }

        if ($this->app->runningInConsole()) {
            $this->loadLegacyMigrations();
        }

        // https://laravel.com/docs/5.5/migrations#indexes
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('development', 'dusk', 'local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(Tracker::class, HoneyBadgerTracker::class);
    }
}
