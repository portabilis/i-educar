<?php

namespace Tests\Unit\Services;

use App\Models\LegacyMenu;
use App\Models\LegacySubmenu;
use App\Models\LegacyUser;
use App\Services\MenuService;
use iEducar\Support\Repositories\MenuRepository;
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
        LegacyMenu::query()->truncate();
        LegacySubmenu::query()->truncate();
    }

    public function testWithoutPermissionUserShouldReturnEmpty()
    {
        $user = factory(LegacyUser::class)->create();
        $result = $this->service->getByUser($user);
        $this->assertEmpty($result);
    }

    public function testCommonUserShouldReturnByPermission()
    {
        $user = factory(LegacyUser::class)->create();
        $submenu = factory(LegacySubmenu::class)->create();
        $submenu->typeUsers()->attach($user->type->cod_tipo_usuario);

        $result = $this->service->getByUser($user);
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($submenu->menu));
    }

    public function testReturnOnlyActives()
    {
        $user = factory(LegacyUser::class)->create();
        $activeSubmenu = factory(LegacySubmenu::class)->create();
        $activeSubmenu->typeUsers()->attach($user->type->cod_tipo_usuario);

        $inactiveSubmenu = factory(LegacySubmenu::class)->create([
            'ref_cod_menu_menu' => factory(LegacyMenu::class)->create(['ativo' => false])
        ]);
        $inactiveSubmenu->typeUsers()->attach($user->type->cod_tipo_usuario);

        $result = $this->service->getByUser($user);
        $this->assertTrue($result->contains($activeSubmenu->menu));
        $this->assertFalse($result->contains($inactiveSubmenu->menu));
    }
}
