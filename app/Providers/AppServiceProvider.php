<?php

namespace App\Providers;

use Exception;
use App\Services\CacheManager;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use iEducar\Support\Navigation\Breadcrumb;
use iEducar\Support\Navigation\TopMenu;
use iEducar\Modules\ErrorTracking\HoneyBadgerTracker;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Dusk\DuskServiceProvider;
use Laravel\Dusk\ElementResolver;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
     * Load legacy bootstrap application.
     *
     * @return void
     *
     * @throws Exception
     */
    private function loadLegacyBootstrap()
    {
        global $coreExt;

        $coreExt = [];
        $coreExt['Config'] = json_decode(json_encode(config('legacy')));

        setlocale(LC_ALL, 'en_US.UTF-8');
        date_default_timezone_set($coreExt['Config']->app->locale->timezone);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     *
     * @throws Exception
     */
    public function boot()
    {
        if ($this->app->environment('development', 'dusk', 'local', 'testing')) {
            $this->customElementResolver();
        }

        if ($this->app->runningInConsole()) {
            $this->loadLegacyMigrations();
        }

        $this->loadLegacyBootstrap();

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
