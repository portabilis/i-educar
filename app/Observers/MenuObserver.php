<?php

namespace App\Observers;

use App\Menu;
use App\Services\MenuCacheService;
use Throwable;

class MenuObserver
{
    public bool $afterCommit = true;

    public function saved(Menu $menu): void
    {
        $this->flush();
    }

    public function deleted(Menu $menu): void
    {
        $this->flush();
    }

    private function flush(): void
    {
        try {
            app(MenuCacheService::class)->flushAll();
        } catch (Throwable $e) {
            report($e);
        }
    }
}
