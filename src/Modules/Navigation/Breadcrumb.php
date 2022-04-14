<?php

namespace iEducar\Modules\Navigation;

use iEducar\Support\Navigation\Breadcrumb as SupportBreadcrumb;

class Breadcrumb
{
    public function makeBreadcrumb($currentPage, $breadcrumbs)
    {
        app(SupportBreadcrumb::class)->current($currentPage, $breadcrumbs);
    }
}
