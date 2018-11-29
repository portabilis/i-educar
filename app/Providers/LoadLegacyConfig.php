<?php

namespace App\Providers;

use iEducar\Support\Config\LegacyConfig;
use Illuminate\Support\ServiceProvider;

class LoadLegacyConfig extends ServiceProvider
{
    public function register()
    {
        $legacyConfigPath = base_path() . '/' . config('legacy.path') . '/configuration/';
        $configObject = new LegacyConfig($legacyConfigPath, config('legacy.env'), request()->getHttpHost());

        $laravelLegacyConfig = config()->get('legacy');

        config()->set(['legacy' => array_merge($configObject->getArrayConfig(), $laravelLegacyConfig)]);
    }
}
