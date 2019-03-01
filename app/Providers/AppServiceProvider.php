<?php

namespace App\Providers;

use App\Services\CacheManager;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use iEducar\Support\Navigation\Breadcrumb;
use iEducar\Support\Navigation\TopMenu;
use iEducar\Modules\ErrorTracking\HoneyBadgerTracker;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Dusk\ElementResolver;
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
     * Add custom methods in ElementResolver class used by Laravel Dusk.
     *
     * @return void
     */
    private function customElementResolver()
    {
        ElementResolver::macro('findByText', function ($text, $tag) {
            foreach ($this->all($tag) as $element) {
                if (Str::contains($element->getText(), $text)) {
                    return $element;
                }
            }
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
            $this->customElementResolver();
        }

        if ($this->app->runningInConsole()) {
            $this->loadLegacyMigrations();
        }

        Request::macro('getSubdomain', function () {
            $host = str_replace('-', '', $this->getHost());
            return Str::replaceFirst('.' . config('app.default_host'), '', $host);
        });

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
            $this->app->register(DebugbarServiceProvider::class);
        }

        $this->app->bind(Tracker::class, HoneyBadgerTracker::class);

        Cache::swap(new CacheManager(app()));
    }
}
