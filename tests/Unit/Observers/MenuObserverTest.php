<?php

use App\Menu;
use App\Services\MenuCacheService;
use Database\Factories\MenuFactory;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->createMenuTree = function (int $process): Menu {
        $root = MenuFactory::new()->create(['parent_id' => null]);

        return MenuFactory::new()->create([
            'parent_id' => $root->getKey(),
            'process' => $process,
        ]);
    };

    $this->warmCache = function (int $process) {
        app(MenuCacheService::class)->getProcessMenuData($process);

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData($process);

        $this->assertCount(0, DB::getQueryLog());
    };

    $this->assertCacheWasCleared = function (int $process) {
        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData($process);

        $this->assertNotCount(0, DB::getQueryLog());
    };

    Cache::flush();
});

test('menu cache is cleared when menu is created', function () {
    ($this->createMenuTree)(988001);

    ($this->warmCache)(988001);

    MenuFactory::new()->create(['parent_id' => null]);

    ($this->assertCacheWasCleared)(988001);
});

test('menu cache is cleared when menu is updated', function () {
    $menu = ($this->createMenuTree)(988002);

    ($this->warmCache)(988002);

    $menu->update(['title' => 'Outro título']);

    ($this->assertCacheWasCleared)(988002);
});

test('menu cache is cleared when menu is deleted', function () {
    $menu = ($this->createMenuTree)(988003);

    ($this->warmCache)(988003);

    $menu->delete();

    ($this->assertCacheWasCleared)(988003);
});

test('menu cache is cleared by connection database not legacy config', function () {
    ($this->createMenuTree)(988004);

    ($this->warmCache)(988004);

    Config::set('legacy.app.database.dbname', 'outro_municipio');

    MenuFactory::new()->create(['parent_id' => null]);

    ($this->assertCacheWasCleared)(988004);
});

test('menu cache is cleared only after transaction commits', function () {
    ($this->createMenuTree)(988005);

    ($this->warmCache)(988005);

    DB::transaction(function () {
        MenuFactory::new()->create(['parent_id' => null]);

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData(988005);

        $this->assertCount(0, DB::getQueryLog());
    });

    ($this->assertCacheWasCleared)(988005);
});

test('falha ao limpar o cache e reportada sem impedir a gravacao', function () {
    $cache = Mockery::mock(MenuCacheService::class);
    $cache->shouldReceive('flushAll')->andThrow(new RuntimeException('redis fora do ar'));
    $this->app->instance(MenuCacheService::class, $cache);
    $reported = [];
    $this->app->make(ExceptionHandler::class)->reportable(function (RuntimeException $e) use (&$reported) {
        $reported[] = $e->getMessage();

        return false;
    });

    $menu = MenuFactory::new()->create();

    $this->assertTrue($menu->exists);
    $this->assertContains('redis fora do ar', $reported);
});
