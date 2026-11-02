<?php

use App\Menu;
use App\Services\MenuCacheService;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->createMenuTree = function (int $process): Menu {
        $root = MenuFactory::new()->create(['parent_id' => null]);
        $branch = MenuFactory::new()->create(['parent_id' => $root->getKey()]);

        return MenuFactory::new()->create([
            'parent_id' => $branch->getKey(),
            'process' => $process,
        ]);
    };

    Cache::flush();
});

test('process menu data matches root and ancestors', function () {
    $menu = ($this->createMenuTree)(987001);

    $data = app(MenuCacheService::class)->getProcessMenuData(987001);

    $this->assertSame($menu->getKey(), $data['attributes']['id']);
    $this->assertSame($menu->root()->getKey(), $data['root_id']);
    $this->assertSame($menu->ancestors()->get()->pluck('id')->all(), $data['ancestor_ids']);
});

test('process menu data when menu is root', function () {
    $root = MenuFactory::new()->create(['parent_id' => null, 'process' => 987005]);

    $data = app(MenuCacheService::class)->getProcessMenuData(987005);

    $this->assertSame($root->getKey(), $data['root_id']);
    $this->assertSame([], $data['ancestor_ids']);
});

test('empty process returns false without querying', function () {
    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(null));
    $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(0));
    $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(''));
    $this->assertCount(0, DB::getQueryLog());
});

test('process menu data is cached', function () {
    ($this->createMenuTree)(987002);

    app(MenuCacheService::class)->getProcessMenuData(987002);

    DB::enableQueryLog();
    DB::flushQueryLog();

    app(MenuCacheService::class)->getProcessMenuData(987002);

    $this->assertCount(0, DB::getQueryLog());
});

test('process without menu caches absence', function () {
    $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(987003));

    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->assertFalse(app(MenuCacheService::class)->getProcessMenuData(987003));
    $this->assertCount(0, DB::getQueryLog());
});

test('flush all clears process menu', function () {
    ($this->createMenuTree)(987004);

    app(MenuCacheService::class)->getProcessMenuData(987004);
    app(MenuCacheService::class)->flushAll();

    DB::enableQueryLog();
    DB::flushQueryLog();

    app(MenuCacheService::class)->getProcessMenuData(987004);

    $this->assertNotCount(0, DB::getQueryLog());
});
