<?php

namespace App\Observers;

use App\Models\LegacyGeneralConfiguration;
use App\Services\CacheService;

class LegacyGeneralConfigurationObserver
{
    public function saved(LegacyGeneralConfiguration $generalConfiguration): void
    {
        CacheService::clearGeneralConfiguration();
    }

    public function deleted(LegacyGeneralConfiguration $generalConfiguration): void
    {
        CacheService::clearGeneralConfiguration();
    }
}
