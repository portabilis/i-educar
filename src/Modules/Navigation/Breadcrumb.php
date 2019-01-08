<?php

namespace iEducar\Modules\Navigation;

class Breadcrumb
{
    public function makeBreadcrumb($currentPage, $breadcrumbs)
    {
        app(\iEducar\Support\Navigation\Breadcrumb::class)->current($currentPage, $breadcrumbs);
    }
}
