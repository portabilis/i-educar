<?php

namespace App\Providers;

use iEducar\Support\Config\LegacyConfig;
use Illuminate\Support\ServiceProvider;

class LoadLegacyConfig extends ServiceProvider
{
    /**
     * Load legacy configs
     *
     * @return void
     */
    public function register()
    {
        $legacyConfigPath = base_path() . '/' . config('legacy.path') . '/configuration/';
        $configObject = new LegacyConfig($legacyConfigPath, config('app.env'), request()->getHttpHost());
        \Config::set($configObject->getArrayConfig());
    }
}
