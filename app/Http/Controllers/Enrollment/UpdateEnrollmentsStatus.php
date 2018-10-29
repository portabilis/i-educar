<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use iEducar\Modules\Navigation\Breadcrumb;

class UpdateEnrollmentsStatus extends Controller
{
    public function index()
    {
        return view('update-enrollments-status.index');
    }

    public function getBreadCrumb()
    {
        $breadCrumb = new Breadcrumb();
        $breadCrumb->makeBreadcrumb('Alterar situação de matrículas', ['educar_configuracoes_index.php' => 'Configurações']);
        return $breadCrumb;
    }

}