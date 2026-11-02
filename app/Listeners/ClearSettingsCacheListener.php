<?php

namespace App\Listeners;

use App\Events\SystemSettingsUpdatedEvent;
use App\Services\CacheService;

class ClearSettingsCacheListener
{
    public function handle(SystemSettingsUpdatedEvent $event)
    {
        CacheService::clearSettings();
    }
}
