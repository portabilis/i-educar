<?php

namespace App\Providers;

use App\Models\SchoolManager;
use App\Observers\SchoolManagerObserver;
use App\Services\CacheManager;
use App\Models\LegacyInstitution;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use iEducar\Support\Navigation\Breadcrumb;
use iEducar\Modules\ErrorTracking\HoneyBadgerTracker;
use iEducar\Modules\ErrorTracking\Tracker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('development', 'dusk', 'local', 'testing')) {
            $this->customElementResolver();
        }

        if ($this->app->runningInConsole()) {
            $this->loadLegacyMigrations();
        }

        Request::macro('getSubdomain', function () {
            $host = str_replace('-', '', $this->getHost());
            return Str::replaceFirst('.' . config('app.default_host'), '', $host);
        });

        Collection::macro('getKeyValueArray', function ($valueField) {
            $keyValueArray = [];
            foreach ($this->items as $item) {
                $keyValueArray[$item->getKey()] = $item->getAttribute($valueField);
            }

            return $keyValueArray;
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

        if ($this->app->environment('development', 'dusk', 'local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(DebugbarServiceProvider::class);
        }

        $this->app->bind(Tracker::class, HoneyBadgerTracker::class);

        $this->app->bind(LegacyInstitution::class, function () {
            return LegacyInstitution::query()->where('ativo', 1)->firstOrFail();
        });

        Cache::swap(new CacheManager(app()));
    }
}
