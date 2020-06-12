<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRegistrationDateRequest;
use App\Models\LegacyRegistration;
use App\Process;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UpdateRegistrationDateController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Atualização da data de entrada e enturmação em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::UPDATE_REGISTRATION_DATE);

        return view('registration.update-registration-date.index', ['user' => $request->user()]);
    }

    /**
     * Atualiza a data de entrada e enturmação de acordo com o filtro
     *
     * @param UpdateRegistrationDateRequest $request
     * @param RegistrationService $registrationService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(UpdateRegistrationDateRequest $request, RegistrationService $registrationService)
    {
        $query = LegacyRegistration::active();

        if ($request->get('ano')) {
            $query->where('ano', $request->get('ano'));
        }

        if ($request->get('ref_cod_escola')) {
            $query->where('ref_ref_cod_escola', $request->get('ref_cod_escola'));
        }

        if ($request->get('ref_cod_curso')) {
            $query->where('ref_cod_curso', $request->get('ref_cod_curso'));
        }

        if ($request->get('ref_cod_turma')) {
            $schoolClassId = $request->get('ref_cod_turma');
            $query->whereHas('enrollments', function ($enrollmentQuery) use ($schoolClassId) {
                $enrollmentQuery->where('ref_cod_turma', $schoolClassId);
            });
        }

        if ($request->get('ref_cod_serie')) {
            $query->where('ref_ref_cod_serie', $request->get('ref_cod_serie'));
        }

        $oldData = $request->get('data_antiga') ? \DateTime::createFromFormat('d/m/Y', $request->get('data_antiga')) : null;
        if ($request->get('data_antiga')) {
            $query->where('data_matricula', $oldData->format('Y-m-d'));
        }

        if ($request->get('situacao')) {
            $query->where('aprovado', $request->get('situacao'));
        }

        $registrations = $query->get();

        if (count($registrations) == 0) {
            return redirect()->route('update-registration-date.index')->with('error', 'Nenhuma matrícula encontrada com os filtros selecionados');
        }

        if (empty($request->get('confirmation'))) {
            return redirect()->route('update-registration-date.index')->withInput()->with('show-confirmation', ['count' => count($registrations)]);
        }

        DB::beginTransaction();

        $newDate = \DateTime::createFromFormat('d/m/Y', $request->get('nova_data'));

        foreach ($registrations as $registration) {
            $registrationService->updateRegistrationDate($registration, $newDate);
            $registrationService->updateEnrollmentsDate($registration, $newDate, $oldData, !empty($request->get('remanejadas')));
        }

        DB::commit();

        return redirect()->route('update-registration-date.index')->with('success', count($registrations) . ' matrículas atualizadas com sucesso.');
    }
}
