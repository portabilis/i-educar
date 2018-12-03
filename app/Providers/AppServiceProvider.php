<?php

namespace App\Providers;

use iEducar\Support\Navigation\Breadcrumb;
use iEducar\Support\Navigation\TopMenu;
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

        $this->app->register(RepositoryServiceProvider::class);
        $this->app->singleton(Breadcrumb::class);
        $this->app->singleton(TopMenu::class);

        if ($this->app->environment('development', 'dusk', 'local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(\iEducar\Modules\ErrorTracking\Tracker::class, \iEducar\Modules\ErrorTracking\HoneyBadgerTracker::class);
    }
}
