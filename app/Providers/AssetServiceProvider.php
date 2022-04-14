<?php

namespace App\Providers;

use App\Contracts\AssetServiceContract;
use App\Services\AssetService;
use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/assets.php' => config_path('assets.php')
        ], 'config');

        $this->app->singleton(AssetServiceContract::class, function () {
            return new AssetService(config('assets.version'), config('assets.secure'), config('assets.auto', false));
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
    }
}
