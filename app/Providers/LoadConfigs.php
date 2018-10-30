<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LoadConfigs extends ServiceProvider
{
    public function register()
    {
        $configObject = new \iEducar\Support\Config\LegacyConfig(config('app.env'), request()->getHttpHost());
        \Config::set($configObject->getArrayConfig());
    }
}
