<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchExemptionRequest;
use App\Jobs\BatchExemptionJob;
use App\Models\LegacyRegistration;
use App\Models\LegacyStageType;
use App\Process;
use App\Services\Exemption\BatchExemptionService;
use App\Services\Exemption\ExemptionService;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BatchExemptionController extends Controller
{
    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin() && !$request->user()->isInstitutional()) {
            return back()->withErrors(['Error' => ['Você não tem permissão para acessar este recurso']]);
        }

        $this->breadcrumb('Dispensa em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::BATCH_EXEMPTION);

        return view('exemption.batch', [
            'stageTypes' => LegacyStageType::active()->get()->keyBy('cod_modulo')->toJson(),
        ]);
    }

    public function exempt(BatchExemptionRequest $request)
    {
        $query = LegacyRegistration::active();

        $registrations = $this->addFilters($request, $query);

        if (count($registrations) == 0) {
            return redirect()->route('batch-exemption.index')->with('error', 'Nenhuma matrícula encontrada com os filtros selecionados');
        }

        $exemptionService = new ExemptionService($request->user());
        $exemptionService->isBatch = true;
        $exemptionService->keepAbsences = (bool) $request->get('manter_frequencias', false);
        $batchExemptionService = new BatchExemptionService($exemptionService);

        foreach ($registrations as $registration) {
            $batchExemptionService->addRegistration(
                $registration,
                $request->get('ref_cod_componente_curricular'),
                $request->get('exemption_type'),
                $request->get('observacoes'),
                $request->get('stage'),
            );
        }

        $job = new BatchExemptionJob($batchExemptionService, DB::getDefaultConnection(), $request->user());
        app(Dispatcher::class)->dispatch($job);

        return redirect()
            ->route('batch-exemption.index')
            ->with('success', sprintf('Serão criadas dispensas para %s matrículas. Você será notificado no final do processo', count($registrations)));
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

        $query->whereHas('enrollments', function ($enrollmentQuery) {
            $enrollmentQuery->where('ativo', 1);
        });

        return $query->get();
    }
}
