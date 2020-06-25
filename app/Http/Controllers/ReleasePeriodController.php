<?php

namespace App\Http\Controllers;

use App\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReleasePeriodController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function new(Request $request)
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

        return view('release-period.index', ['user' => $request->user()]);
    }

    public function create(Request $request)
    {

    }
}
