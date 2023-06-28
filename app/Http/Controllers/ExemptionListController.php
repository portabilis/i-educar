<?php

namespace App\Http\Controllers;

use App\Models\LegacyDisciplineExemption;
use App\Process;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExemptionListController extends Controller
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Consulta de dispensas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::EXEMPTION_LIST);

        $schools[] = $request->get('ref_cod_escola');

        if ($request->user()->isSchooling()) {
            $schools = $request->user()->schools->pluck('cod_escola')->all();
        }
        $exemptions = LegacyDisciplineExemption::filter([
            'schools' => array_filter($schools),
            'yearEq' => $request->get('ano'),
            'grade' => $request->get('ref_cod_serie'),
            'course' => $request->get('ref_cod_curso'),
            'discipline' => $request->get('ref_cod_componente_curricular'),
        ])->with([
            'type:cod_tipo_dispensa,nm_tipo',
            'registration:cod_matricula,ano,ref_cod_aluno',
            'registration.student:cod_aluno,ref_idpes',
            'registration.student.person:idpes,nome',
            'discipline:id,nome',
            'createdBy:cod_usuario',
        ])->withoutTrashed()
            ->orderByCreatedAt()
            ->paginate(20)
            ->appends($request->except('page'));

        return view('exemption.index', compact('exemptions'));
    }
}
