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
     * @param null              $paginate
     *
     * @return View
     */
    private function view(LegacyInstitution $institution, $records = [], $paginate = null)
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
            'paginate' => $paginate,
            'record20' => $records['record20'] ?? null,
            'record40' => $records['record40'] ?? null,
            'record50' => $records['record50'] ?? null,
            'record60' => $records['record60'] ?? null,
        ]);
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
        $record = $request->query('record');
        $school = $request->query('ref_cod_escola');
        $year = $request->query('year');

        if (empty($record) || empty($school) || empty($year)) {
            return $this->view($institution);
        }

        $records = [];

        if ($record == '20') {
            $paginate = $repository->getBuilderForRecord20($year, $school)
                ->orderBy('nomeTurma')
                ->paginate();

            $records['record20'] = $paginate->items();
        }

        if ($record == '40') {
            $paginate = $repository->getBuilderForRecord40($school)
                ->orderBy('nomePessoa')
                ->paginate();

            $records['record40'] = $paginate->items();
        }

        if ($record == '50') {
            require_once base_path('ieducar/modules/ComponenteCurricular/Model/CodigoEducacenso.php');

            $paginate = $repository->getBuilderForRecord50($year, $school)
                ->paginate();

            $records['record50'] = collect($paginate->items())
                ->map(function ($item) {
                    $disciplines = explode(',', substr($item->componentes, 1, -1));

                    $item->componentes = collect($disciplines)->unique()->map(function ($discipline) {
                        return ComponenteCurricular_Model_CodigoEducacenso::getDescription($discipline);
                    })->toArray();

                    return $item;
                })
                ->sortBy(function ($data) {
                    return "{$data->nomeDocente}{$data->nomeTurma}";
                })
                ->values();
        }

        if ($record == '60') {
            $paginate = $repository->getBuilderForRecord60($year, $school)
                ->orderBy('nomeAluno')
                ->paginate();

            $records['record60'] = $paginate->items();
        }

        return $this->view($institution, $records, $paginate);
    }
}
