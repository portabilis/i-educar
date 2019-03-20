<?php

namespace App\Http\Controllers;

use iEducar\Support\Navigation\Breadcrumb;
use iEducar\Support\Navigation\TopMenu;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Set the breadcrumbs of the action
     *
     * @param $currentPage
     * @param array $pages
     */
    public function breadcrumb($currentPage, $pages = [])
    {
        app(Breadcrumb::class)->current($currentPage, $pages);
    }

    /**
     * Set the top menu of the action
     *
     * @param $currentSubmenuId
     * @param null $currentUri
     */
    public function topMenu($currentSubmenuId, $currentUri = null)
    {
        if (empty($currentUri)) {
            $currentUri = request()->getRequestUri();
        }

        app(TopMenu::class)->current($currentSubmenuId, $currentUri);
    }
}
