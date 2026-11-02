<?php

use App\Menu;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->render = function (Menu $root, array $children): string {
        $menu = $root->newCollection([$root]);
        $root->setRelation('children', $root->newCollection($children));

        return View::make('layout.topmenu', [
            'mainmenu' => $root->getKey(),
            'menu' => $menu,
            'currentMenu' => $root,
            'menuPaths' => [],
        ])->render();
    };

    Cache::flush();
});

test('topmenu is not shared between user types', function () {
    $root = MenuFactory::new()->create(['parent_id' => null]);

    $restrito = MenuFactory::new()->create([
        'parent_id' => $root->getKey(),
        'title' => 'Item Restrito',
        'link' => 'restrito.php',
    ]);

    $comum = MenuFactory::new()->create([
        'parent_id' => $root->getKey(),
        'title' => 'Item Comum',
        'link' => 'comum.php',
    ]);

    $this->assertStringContainsString('Item Restrito', ($this->render)($root, [$restrito, $comum]));

    $semRestrito = ($this->render)($root->fresh(), [$comum]);

    $this->assertStringNotContainsString('Item Restrito', $semRestrito);
    $this->assertStringContainsString('Item Comum', $semRestrito);
});
