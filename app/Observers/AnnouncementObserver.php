<?php

namespace App\Observers;

use App\Models\Announcement;
use App\Services\CacheService;

class AnnouncementObserver
{
    public function saved(Announcement $announcement): void
    {
        CacheService::clearAnnouncements();
    }

    public function deleted(Announcement $announcement): void
    {
        CacheService::clearAnnouncements();
    }

    public function restored(Announcement $announcement): void
    {
        CacheService::clearAnnouncements();
    }
}
