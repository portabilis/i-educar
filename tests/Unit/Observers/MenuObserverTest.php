<?php

namespace Tests\Unit\Observers;

use App\Menu;
use App\Services\MenuCacheService;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuObserverTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function createMenuTree(int $process): Menu
    {
        $root = MenuFactory::new()->create(['parent_id' => null]);

        return MenuFactory::new()->create([
            'parent_id' => $root->getKey(),
            'process' => $process,
        ]);
    }

    private function warmCache(int $process): void
    {
        app(MenuCacheService::class)->getProcessMenuData($process);

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData($process);

        $this->assertCount(0, DB::getQueryLog());
    }

    private function assertCacheWasCleared(int $process): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData($process);

        $this->assertNotCount(0, DB::getQueryLog());
    }

    public function test_menu_cache_is_cleared_when_menu_is_created()
    {
        $this->createMenuTree(988001);

        $this->warmCache(988001);

        MenuFactory::new()->create(['parent_id' => null]);

        $this->assertCacheWasCleared(988001);
    }

    public function test_menu_cache_is_cleared_when_menu_is_updated()
    {
        $menu = $this->createMenuTree(988002);

        $this->warmCache(988002);

        $menu->update(['title' => 'Outro título']);

        $this->assertCacheWasCleared(988002);
    }

    public function test_menu_cache_is_cleared_when_menu_is_deleted()
    {
        $menu = $this->createMenuTree(988003);

        $this->warmCache(988003);

        $menu->delete();

        $this->assertCacheWasCleared(988003);
    }

    public function test_menu_cache_is_cleared_by_connection_database_not_legacy_config()
    {
        $this->createMenuTree(988004);

        $this->warmCache(988004);

        Config::set('legacy.app.database.dbname', 'outro_municipio');

        MenuFactory::new()->create(['parent_id' => null]);

        $this->assertCacheWasCleared(988004);
    }

    public function test_menu_cache_is_cleared_only_after_transaction_commits()
    {
        $this->createMenuTree(988005);

        $this->warmCache(988005);

        DB::transaction(function () {
            MenuFactory::new()->create(['parent_id' => null]);

            DB::enableQueryLog();
            DB::flushQueryLog();

            app(MenuCacheService::class)->getProcessMenuData(988005);

            $this->assertCount(0, DB::getQueryLog());
        });

        $this->assertCacheWasCleared(988005);
    }
}
