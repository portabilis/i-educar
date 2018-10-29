<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LoadConfigs extends ServiceProvider
{
    public function register()
    {
        $configObject = new \iEducar\Modules\Config\LegacyConfig(null, null);
        \Config::set($configObject->getArrayConfig());
    }
}
