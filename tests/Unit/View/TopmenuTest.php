<?php

namespace Tests\Unit\View;

use App\Menu;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class TopmenuTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function render(Menu $root, array $children): string
    {
        $menu = $root->newCollection([$root]);
        $root->setRelation('children', $root->newCollection($children));

        return View::make('layout.topmenu', [
            'mainmenu' => $root->getKey(),
            'menu' => $menu,
            'currentMenu' => $root,
            'menuPaths' => [],
        ])->render();
    }

    public function test_topmenu_is_not_shared_between_user_types()
    {
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

        $this->assertStringContainsString('Item Restrito', $this->render($root, [$restrito, $comum]));

        $semRestrito = $this->render($root->fresh(), [$comum]);

        $this->assertStringNotContainsString('Item Restrito', $semRestrito);
        $this->assertStringContainsString('Item Comum', $semRestrito);
    }
}
