<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;

class UpdateEnrollmentsStatus extends Controller
{
    public function index()
    {
        $this->breadcrumb('Alterar situação de matrículas', [
            route('settings') => 'Configurações'
        ]);

        return view('update-enrollments-status.index');
    }
}