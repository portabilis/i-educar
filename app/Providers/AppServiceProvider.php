<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
