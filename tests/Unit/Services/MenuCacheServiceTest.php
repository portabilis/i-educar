<?php

namespace Tests\Unit\Services;

use App\Menu;
use App\Services\MenuCacheService;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuCacheServiceTest extends TestCase
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
        $branch = MenuFactory::new()->create(['parent_id' => $root->getKey()]);

        return MenuFactory::new()->create([
            'parent_id' => $branch->getKey(),
            'process' => $process,
        ]);
    }

    public function test_process_menu_data_matches_root_and_ancestors()
    {
        $menu = $this->createMenuTree(987001);

        $data = app(MenuCacheService::class)->getProcessMenuData(987001);

        $this->assertSame($menu->getKey(), $data['attributes']['id']);
        $this->assertSame($menu->root()->getKey(), $data['root_id']);
        $this->assertSame($menu->ancestors()->get()->pluck('id')->all(), $data['ancestor_ids']);
    }

    public function test_process_menu_data_when_menu_is_root()
    {
        $root = MenuFactory::new()->create(['parent_id' => null, 'process' => 987005]);

        $data = app(MenuCacheService::class)->getProcessMenuData(987005);

        $this->assertSame($root->getKey(), $data['root_id']);
        $this->assertSame([], $data['ancestor_ids']);
    }

    public function test_empty_process_returns_false_without_querying()
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(null));
        $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(0));
        $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(''));
        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_process_menu_data_is_cached()
    {
        $this->createMenuTree(987002);

        app(MenuCacheService::class)->getProcessMenuData(987002);

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData(987002);

        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_process_without_menu_caches_absence()
    {
        $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(987003));

        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(987003));
        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_flush_all_clears_process_menu()
    {
        $this->createMenuTree(987004);

        app(MenuCacheService::class)->getProcessMenuData(987004);
        app(MenuCacheService::class)->flushAll();

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData(987004);

        $this->assertNotCount(0, DB::getQueryLog());
    }
}
