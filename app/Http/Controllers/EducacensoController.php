<?php

namespace App\Http\Controllers;

use App\Models\LegacyInstitution;
use App\Repositories\EducacensoRepository;
use ComponenteCurricular_Model_CodigoEducacenso;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducacensoController extends Controller
{
    /**
     * @param LegacyInstitution $institution
     * @param array             $records
     *
     * @return View
     */
    private function view(LegacyInstitution $institution, $records = [])
    {
        $this->breadcrumb('Consulta 1ª fase - Matrícula inicial', [
            url('intranet/educar_educacenso_index.php') => 'Educacenso',
        ]);

        $this->menu(70);

        $schools = $institution->schools()->with('person')->get()->sortBy(function ($school) {
            return $school->name;
        });

        return view('educacenso.consult', [
            'institution' => $institution,
            'schools' => $schools,
            'record20' => $records['record20'] ?? null,
            'record40' => $records['record40'] ?? null,
            'record50' => $records['record50'] ?? null,
            'record60' => $records['record60'] ?? null,
        ]);
    }

    /**
     * @param LegacyInstitution $institution
     *
     * @return View
     */
    public function show(LegacyInstitution $institution)
    {
        return $this->view($institution);
    }

    /**
     * @param Request              $request
     * @param EducacensoRepository $repository
     * @param LegacyInstitution    $institution
     *
     * @return View
     */
    public function consult(
        Request $request,
        EducacensoRepository $repository,
        LegacyInstitution $institution
    ) {
        $record = $request->input('record');
        $school = $request->input('ref_cod_escola');
        $year = $request->input('year');

        $records = [];

        if ($record == '20') {
            $records['record20'] = collect($repository->getDataForRecord20($school, $year))
                ->sortBy('nomeTurma')
                ->values();
        }

        if ($record == '40') {
            $records['record40'] = collect($repository->getDataForRecord40($school))
                ->sortBy('nomePessoa')
                ->values();
        }

        if ($record == '50') {
            require_once base_path('ieducar/modules/ComponenteCurricular/Model/CodigoEducacenso.php');

            $records['record50'] = $repository->getDataForRecord50($year, $school);

            $records['record50'] = collect($records['record50'])
                ->map(function ($item) {
                    $disciplines = explode(',', substr($item->componentes, 1, -1));

                    $item->componentes = collect($disciplines)->unique()->map(function ($discipline) {
                        $data = ComponenteCurricular_Model_CodigoEducacenso::getDescription($discipline);

                        return $data;
                    })->toArray();

                    return $item;
                })
                ->sortBy(function ($data) {
                    return "{$data->nomeDocente}{$data->nomeTurma}";
                })
                ->values();
        }

        if ($record == '60') {
            $records['record60'] = collect($repository->getDataForRecord60($school, $year))
                ->sortBy('nomeAluno')
                ->values();
        }

        return $this->view($institution, $records);
    }
}
