<?php

namespace App\Http\Controllers;

use App\Menu;
use App\Services\MenuCacheService;
use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $beta = false;

    /**
     * @return Breadcrumb
     */
    private function getBreadcrumbInstance(): Breadcrumb
    {
        return app(Breadcrumb::class);
    }

    /**
     * @return MenuCacheService
     */
    private function getMenuCacheServiceInstance(): MenuCacheService
    {
        return app(MenuCacheService::class);
    }

    /**
     * Set the breadcrumbs of the action
     *
     * @param       $currentPage
     * @param array $pages
     *
     * @return $this
     */
    public function breadcrumb($currentPage, $pages = [])
    {
        $breadcrumb = $this->getBreadcrumbInstance()
            ->current($currentPage, $pages);

        if ($this->beta) {
            $breadcrumb->addBetaFlag();
        }

        return $this;
    }

    /**
     * Share with view, title, mainmenu and menu links.
     *
     * @param int $process
     *
     * @return $this
     */
    public function menu($process)
    {
        $user = Auth::user();
        $menu = $this->getMenuCacheServiceInstance()->getMenuByUser($user);

        $topmenu = Menu::query()
            ->where('process', $process)
            ->first();

        if ($topmenu) {
            View::share('mainmenu', $topmenu->root()->getKey());
        }

        View::share('menu', $menu);
        View::share('title', $this->getPageTitle());

        return $this;
    }

    /**
     * @return string
     */
    private function getPageTitle()
    {
        if (isset($this->title)) {
            return $this->title;
        }

        if (isset($this->_title)) {
            return $this->_title;
        }

        if (isset($this->titulo)) {
            return $this->titulo;
        }

        if (isset($this->_titulo)) {
            return $this->_titulo;
        }
    }
}
