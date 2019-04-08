<?php

namespace App\Services;

use App\Models\LegacySubmenu;
use App\Models\LegacyUser;
use iEducar\Support\Repositories\MenuRepository;
use iEducar\Support\Repositories\SubmenuRepository;
use iEducar\Support\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * @param UserRepository $userRepository
     * @param MenuRepository $menuRepository
     * @param SubmenuRepository $submenuRepository
     */
    public function __construct(UserRepository $userRepository, MenuRepository $menuRepository, SubmenuRepository $submenuRepository)
    {
        $this->userRepository = $userRepository;
        $this->menuRepository = $menuRepository;
        $this->submenuRepository = $submenuRepository;
    }

    /**
     * @param LegacyUser $user
     * @return Collection
     */
    public function getByUser(LegacyUser $user)
    {
        $submenus = $this->getSubmenusByUser($user);

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

    /**
     * @param LegacyUser $user
     * @return Collection
     */
    public function getSubmenusByUser(LegacyUser $user)
    {
        $typeUser = $user->type->cod_tipo_usuario;

        /** @var Submenu[] $submenus */
        return $this->submenuRepository->with('menu')->whereHas('typeUsers', function($query) use ($typeUser) {
            $query->where('ref_cod_tipo_usuario', $typeUser);
        })->get();
    }

    /**
     * @param $submenus
     * @return bool
     */
    private function isSuperUser($submenus)
    {
        $arraySubmenuId = $submenus->pluck('cod_menu_submenu')->all();

        return in_array(LegacySubmenu::SUPER_USER_MENU_ID, $arraySubmenuId);
    }
}
