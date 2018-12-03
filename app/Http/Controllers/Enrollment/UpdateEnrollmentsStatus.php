<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;

class UpdateEnrollmentsStatus extends Controller
{
    public function index()
    {
        $this->topMenu(578);

        $this->breadcrumb('Alterar situação de matrículas', [
            route('settings') => 'Configurações'
        ]);

        return view('update-enrollments-status.index', [
            'title' => 'Alterar situação de matrículas'
        ]);
    }
}