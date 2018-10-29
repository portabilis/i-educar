<?php

namespace App\Http\Controllers;

use iEducar\Modules\Navigation\Breadcrumb;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getBreadCrumb()
    {
        $breadCrumb = new Breadcrumb();
        return $breadCrumb;
    }
}
