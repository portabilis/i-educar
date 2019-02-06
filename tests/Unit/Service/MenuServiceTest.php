<?php

namespace Tests\Unit\Service;

use App\Models\Menu;
use App\Models\Submenu;
use App\Models\User;
use App\Services\MenuService;
use iEducar\Support\Repositories\MenuRepository;
use iEducar\Support\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MenuServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var MenuService
     */
    private $service;

    /**
     * @var MenuRepository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(MenuService::class);
        $this->repository = app(MenuRepository::class);
        Menu::query()->truncate();
        Submenu::query()->truncate();
    }

    public function testWithoutPermissionUserShouldReturnEmpty()
    {
        $user = factory(User::class)->create();
        $result = $this->service->getByUser($user);
        $this->assertEmpty($result);
    }
    
    public function testCommonUserShouldReturnByPermission()
    {
        $user = factory(User::class)->create();
        $submenu = factory(Submenu::class)->create();
        $submenu->typeUsers()->attach($user->type->cod_tipo_usuario);

        $result = $this->service->getByUser($user);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($submenu->menu));
    }

    public function testReturnOnlyActives()
    {
        $user = factory(User::class)->create();
        $activeSubmenu = factory(Submenu::class)->create();
        $activeSubmenu->typeUsers()->attach($user->type->cod_tipo_usuario);

        $inactiveSubmenu = factory(Submenu::class)->create([
            'ref_cod_menu_menu' => factory(Menu::class)->create(['ativo' => false])
        ]);
        $inactiveSubmenu->typeUsers()->attach($user->type->cod_tipo_usuario);

        $result = $this->service->getByUser($user);
        $this->assertTrue($result->contains($activeSubmenu->menu));
        $this->assertFalse($result->contains($inactiveSubmenu->menu));
    }

}
