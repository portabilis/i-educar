<?php

namespace iEducar\Support\Navigation;

use App\Models\Menu;
use App\Models\User;
use App\Services\MenuService;
use iEducar\Support\Repositories\MenuRepository;
use iEducar\Support\Repositories\SubmenuRepository;
use iEducar\Support\Repositories\SystemMenuRepository;
use Illuminate\Support\Facades\Cache;

class TopMenu
{
    /**
     * @var Menu
     */
    private $currentMenu;

    /**
     * @var SubmenuRepository
     */
    private $submenuRepository;

    /**
     * @var MenuRepository
     */
    private $menuRepository;

    /**
     * @var SystemMenuRepository
     */
    private $systemMenuRepository;

    /**
     * @var MenuService
     */
    private $menuService;

    /**
     * @var string
     */
    private $currentUri;

    public function __construct(
        SubmenuRepository $submenuRepository,
        MenuRepository $menuRepository,
        SystemMenuRepository $systemMenuRepository,
        MenuService $menuService
    ) {
        $this->submenuRepository = $submenuRepository;
        $this->menuRepository = $menuRepository;
        $this->systemMenuRepository = $systemMenuRepository;
        $this->menuService = $menuService;
    }

    public function current($currentSubmenuId, $currentUri)
    {
        $submenu = $this->submenuRepository->find($currentSubmenuId);
        $this->currentUri = $currentUri;
        $this->currentMenu = $submenu->menu;
    }

    public function getTopMenuArray(User $user)
    {
        $cacheKey = $this->getCacheKey();

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        if (!$this->currentMenu) {
            return;
        }

        $submenuArray = $this->getSubmenuArray()->pluck('cod_menu_submenu')->all();
        $tutorMenuId = $this->getTutorMenuId($submenuArray);

        if (!$tutorMenuId) {
            return;
        }

        $submenuArrayByUser = $this->menuService->getSubmenusByUser($user);
        $submenuArrayByUser = $submenuArrayByUser->merge($this->submenuRepository->findWhere(['nivel' => 2]));
        $submenuIdArray = $submenuArrayByUser->pluck('cod_menu_submenu')->all();

        $menuArray = $this->getItemsAndLevels($submenuIdArray, $tutorMenuId);
        $menuArray = $this->filter($menuArray);
        $menuArray = $this->getPath($menuArray);

        Cache::add($cacheKey, $menuArray, 60);

        return $menuArray->all();
    }

    private function getSubmenuArray()
    {
        return $this->submenuRepository->findWhere(['ref_cod_menu_menu' => $this->currentMenu->id()],
            ['cod_menu_submenu']);
    }

    private function getTutorMenuId($submenuArray)
    {
        return $this->systemMenuRepository->findWhereIn('ref_cod_menu_submenu',
            $submenuArray)->first()->ref_cod_tutormenu;
    }

    private function getItemsAndLevels(array $submenuIdArray, $tutorMenuId)
    {
        /** todo Implementar recursividade para pegar níveis dinamicamente  */
        $menuArray = $this->systemMenuRepository->getNoParentBySubMenusAndTutorMenu($submenuIdArray, $tutorMenuId, 1);

        $menuArray = $menuArray->merge($this->systemMenuRepository->getByParentsAndSubMenusAndTutorMenu(
            $menuArray->pluck('cod_menu')->all(),
            $submenuIdArray,
            $tutorMenuId,
            2)
        );

        $menuArray = $menuArray->merge($this->systemMenuRepository->getByParentsAndSubMenusAndTutorMenu(
            $menuArray->pluck('cod_menu')->all(),
            $submenuIdArray,
            $tutorMenuId,
            3)
        );

        $menuArray = $menuArray->merge($this->systemMenuRepository->getByParentsAndSubMenusAndTutorMenu(
            $menuArray->pluck('cod_menu')->all(),
            $submenuIdArray,
            $tutorMenuId,
            4)
        );

        return $menuArray;
    }

    private function filter($menuArray)
    {
        return $menuArray->filter(function ($item) use ($menuArray) {
            foreach ($menuArray as $menu) {
                if ($item->ref_cod_menu_submenu) {
                    return true;
                }

                if ($item->cod_menu == $menu->ref_cod_menu_pai) {
                    return true;
                }
            }

            return false;
        });

    }

    /**
     * @param $menuArray
     * @return mixed
     * TODO: Refactor
     */
    private function getPath($menuArray)
    {
        $uri = explode('/', $this->currentUri);

        foreach ($menuArray as &$menu) {
            $menu['alvo'] = $menu['alvo'] ? $menu['alvo'] : '_self';

            if ($uri[1] == 'module') {
                if (0 === strpos($menu['caminho'], 'module')) {
                    $menu['caminho'] = '../../' . $menu['caminho'];
                } else {
                    $menu['caminho'] = '../../intranet/' . $menu['caminho'];
                }
            } elseif ($uri[2] == 'filaunica' || $uri[2] == 'reservavaga') {
                if (0 === strpos($menu['caminho'], 'module')) {
                    $menu['caminho'] = '../../' . $menu['caminho'];
                } else {
                    $menu['caminho'] = '../' . $menu['caminho'];
                }
            } elseif (0 === strpos($menu['caminho'], 'module')) {
                $menu['caminho'] = '../../' . $menu['caminho'];
            } elseif(!empty($menu['caminho'])) {
                $menu['caminho'] = '/intranet/' . $menu['caminho'];
            }
        }

        return $menuArray;
    }

    private function getCacheKey()
    {
        return md5($this->currentUri . config('app.name') . session('id_pessoa'));
    }
}