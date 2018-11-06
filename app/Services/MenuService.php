<?php

namespace App\Services;

use App\Entities\Submenu;
use App\Entities\User;
use iEducar\Support\Repositories\MenuRepository;
use iEducar\Support\Repositories\SubmenuRepository;
use iEducar\Support\Repositories\UserRepository;

class MenuService
{
    /**
     * @var UserRepository
     */

    private $userRepository;
    /**
     * @var MenuRepository
     */
    private $menuRepository;

    /**
     * @var SubmenuRepository
     */
    private $submenuRepository;

    public function __construct(UserRepository $userRepository, MenuRepository $menuRepository, SubmenuRepository $submenuRepository)
    {
        $this->userRepository = $userRepository;
        $this->menuRepository = $menuRepository;
        $this->submenuRepository = $submenuRepository;
    }

    public function getByUser(User $user)
    {
        $typeUser = $user->type->cod_tipo_usuario;

        /** @var Submenu[] $submenus */
        $submenus = $this->submenuRepository->whereHas('typeUsers', function($query) use ($typeUser) {
            $query->where('ref_cod_tipo_usuario', $typeUser);
        })->get();

        if ($this->isSuperUser($submenus)) {
            return $this->menuRepository->orderBy('ord_menu')->findWhere(['ativo' => 1]);
        }

        $menus = [];
        foreach ($submenus as $submenu) {
            if (!$submenu->menu->ativo) {
                continue;
            }

            $menus[$submenu->menu->ord_menu] = $submenu->menu;
        }

        ksort($menus);

        return collect($menus);
    }

    private function isSuperUser($submenus)
    {
        $arraySubmenuId = $submenus->pluck('cod_menu_submenu')->all();

        return in_array(Submenu::SUPER_USER_MENU_ID, $arraySubmenuId);
    }
}