<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSchoolClassReportCardRequest;
use App\Jobs\BatchExemptionJob;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStageType;
use App\Process;
use App\Services\Exemption\Exemption\BatchExemptionService;
use App\Services\Exemption\ExemptionService;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Portabilis_Model_Report_TipoBoletim;

class BatchExemptionController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        if(!$request->user()->isAdmin() && !$request->user()->isInstitutional()) {
            return redirect('/intranet/educar_configuracoes_index.php');
        }

        $this->breadcrumb('Dispensa em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::BATCH_EXEMPTION);

        return view('exemption.batch', [
            'stageTypes' => LegacyStageType::active()->get()->keyBy('cod_modulo')->toJson(),
        ]);
    }

    public function exempt(Request $request)
    {
        $query = LegacyRegistration::active();

        $registrations = $this->addFilters($request, $query);

        $exemptionService = new ExemptionService($request->user());
        $batchExemptionService = new BatchExemptionService($exemptionService);

        foreach($registrations as $registration) {
            $batchExemptionService->addRegistration($registration, );
        }

        $exemptionService->createExemptionByDisciplineArray($registration, $this->componentecurricular, $this->ref_cod_tipo_dispensa, $this->observacao, $this->etapa);
        $job = new BatchExemptionJob($exemptionService, DB::getDefaultConnection());
        app(Dispatcher::class)->dispatch($job);
    }

    private function addFilters(Request $request, $query)
    {
        if ($request->get('ano')) {
            $query->where('ano', $request->get('ano'));
        }

        if ($request->get('ref_cod_escola')) {
            $query->where('ref_ref_cod_escola', $request->get('ref_cod_escola'));
        }

        if ($request->get('ref_cod_curso')) {
            $query->where('ref_cod_curso', $request->get('ref_cod_curso'));
        }

        if ($request->get('ref_cod_serie')) {
            $query->where('ref_ref_cod_serie', $request->get('ref_cod_serie'));
        }

        if ($request->get('ref_cod_turma')) {
            $schoolClassId = $request->get('ref_cod_turma');
            $query->whereHas('enrollments', function ($enrollmentQuery) use ($schoolClassId) {
                $enrollmentQuery->where('ref_cod_turma', $schoolClassId);
            });
        }

        if ($request->get('situacao')) {
            $query->where('aprovado', $request->get('situacao'));
        }

        return $query->get();
    }
}
