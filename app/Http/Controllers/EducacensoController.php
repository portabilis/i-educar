<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EducacensoController extends Controller
{
    public function consult()
    {
        $this->breadcrumb('Consulta', [
            '' => 'Educacenso',
        ]);

        $this->menu(70);

        return view('educacenso.consult');
    }
}
