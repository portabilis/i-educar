<?php

namespace App\Http\Controllers;

use App\Menu;
use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $beta = false;

    /**
     * Set the breadcrumbs of the action
     *
     * @param $currentPage
     * @param array $pages
     *
     * @return $this
     */
    public function breadcrumb($currentPage, $pages = [])
    {
        $breadcrumb = app(Breadcrumb::class)
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
        $menu = Menu::user($user);

        $topmenu = Menu::query()
            ->where('process', $process)
            ->first();

        if ($topmenu) {
            View::share('mainmenu', $topmenu->root()->getKey());
        }

        View::share('menu', $menu);
        View::share('title', '');

        return $this;
    }
}
