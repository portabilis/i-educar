<?php

namespace App\Http\Controllers;

use App\Models\LegacyDisciplineExemption;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExemptionListController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Consulta de dispensas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(999847);

        $query = LegacyDisciplineExemption::active()->with('registration.student.person');

        if ($request->get('ano')) {
            $ano = $request->get('ano');
            $query->whereHas('registration', function ($registrationQuery) use ($ano) {
                $registrationQuery->where('ano', $ano);
            });
        }

        if ($request->get('ref_cod_escola')) {
            $query->where('ref_cod_escola', $request->get('ref_cod_escola'));
        }

        if ($request->get('ref_cod_serie')) {
            $query->where('ref_cod_serie', $request->get('ref_cod_serie'));
        }

        if ($request->get('ref_cod_curso')) {
            $courseId = $request->get('ref_cod_curso');
            $query->whereHas('registration', function ($registrationQuery) use ($courseId) {
                $registrationQuery->where('ref_cod_curso', $courseId);
            });
        }

        if ($request->get('ref_cod_componente_curricular')) {
            $query->where('ref_cod_disciplina', $request->get('ref_cod_componente_curricular'));
        }

        $exemptions = $query->paginate(20);

        return view('exemption.index', compact('exemptions'));
    }
}
