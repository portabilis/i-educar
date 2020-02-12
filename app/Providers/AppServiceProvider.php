<?php

namespace App\Providers;

use App\Models\LegacyInstitution;
use App\Providers\Postgres\DatabaseServiceProvider;
use App\Services\CacheManager;
use App\Services\StudentUnificationService;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Exception;
use iEducar\Modules\ErrorTracking\HoneyBadgerTracker;
use iEducar\Modules\ErrorTracking\Tracker;
use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
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
        setlocale(LC_ALL, 'en_US.UTF-8');
        date_default_timezone_set(config('legacy.app.locale.timezone'));
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

        Collection::macro('getKeyValueArray', function ($valueField) {
            $keyValueArray = [];
            foreach ($this->items as $item) {
                $keyValueArray[$item->getKey()] = $item->getAttribute($valueField);
            }

            return $keyValueArray;
        });

        // https://laravel.com/docs/5.5/migrations#indexes
        Schema::defaultStringLength(191);

        Paginator::defaultView('vendor.pagination.default');

        Builder::macro('whereUnaccent', function ($column, $value) {
            $this->whereRaw('unaccent(' . $column . ') ilike unaccent(\'%\' || ? || \'%\')', [$value]);
        });
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

        $this->app->bind(StudentUnificationService::class, function () {
            return new StudentUnificationService(Auth::user());
        });

        Cache::swap(new CacheManager(app()));
        $this->app->register(DatabaseServiceProvider::class);
    }
}
