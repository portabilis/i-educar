<?php

namespace App\Http\Controllers;

use App\Process;
use Illuminate\Http\Request;

class IndicatorsController extends Controller
{
    public function panelDimensionSchoolNetwork(Request $request)
    {
        $this->breadcrumb('Indicadores', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::PANEL_DIMENSION_SCHOOL_NETWORK);

        if (!$request->user()->isAdmin()) {
            return back()->withErrors(['Error' => ['Você não tem permissão para acessar este recurso']]);
        }

        return view('indicators.panel-dimension-school-network.index');
    }

    public function studentsSchoolsGeolocation(Request $request)
    {
        $this->breadcrumb('Indicadores', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::STUDENTS_SCHOOLS_GEOLOCATION);

        if (!$request->user()->isAdmin()) {
            return back()->withErrors(['Error' => ['Você não tem permissão para acessar este recurso']]);
        }

        return view('indicators.students-schools-geolocation.index');
    }
 }
