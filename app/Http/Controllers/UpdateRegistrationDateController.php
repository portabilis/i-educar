<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRegistrationDateRequest;
use App\Jobs\UpdateRegistrationDateJob;
use App\Models\LegacyRegistration;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationService;
use App\Services\RegistrationService;
use App\User;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UpdateRegistrationDateController extends Controller
{
    private const ASYNC_THRESHOLD = 500;

    /**
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
     *
     * @return RedirectResponse
     */
    public function updateStatus(UpdateRegistrationDateRequest $request)
    {
        $query = LegacyRegistration::active();

        $registrations = $this->addFilters($request, $query);

        if (count($registrations) == 0) {
            return redirect()->route('update-registration-date.index')->with('error', 'Nenhuma matrícula encontrada com os filtros selecionados');
        }

        if (empty($request->get('confirmation'))) {
            return redirect()->route('update-registration-date.index')->withInput()->with('show-confirmation', ['count' => count($registrations)]);
        }

        $newDateRegistration = \DateTime::createFromFormat('d/m/Y', $request->get('nova_data_entrada'));
        $newDateEnrollment = $request->get('nova_data_enturmacao')
            ? \DateTime::createFromFormat('d/m/Y', $request->get('nova_data_enturmacao'))
            : null;
        $remanejadas = !empty($request->get('remanejadas'));

        if (count($registrations) <= self::ASYNC_THRESHOLD) {
            return $this->processSync($registrations, $newDateRegistration, $newDateEnrollment, $remanejadas, $request->user());
        }

        return $this->processAsync($registrations, $newDateRegistration, $newDateEnrollment, $remanejadas, $request->user());
    }

    private function processSync(Collection $registrations, \DateTime $newDateRegistration, ?\DateTime $newDateEnrollment, bool $remanejadas, User $user)
    {
        $registrationService = new RegistrationService($user);

        DB::beginTransaction();

        $result = [];
        foreach ($registrations as $registration) {
            $result[] = $registrationService->updateRegistrationDate($registration, $newDateRegistration);

            if ($newDateEnrollment) {
                $registrationService->updateEnrollmentsDate($registration, $newDateEnrollment, $remanejadas);
            }
        }

        DB::commit();

        return redirect()->route('update-registration-date.index')
            ->with('success', count($registrations) . ' matrículas atualizadas com sucesso.')
            ->with('registrations', $result);
    }

    private function processAsync(Collection $registrations, \DateTime $newDateRegistration, ?\DateTime $newDateEnrollment, bool $remanejadas, User $user)
    {
        $databaseConnection = DB::getDefaultConnection();
        $userId = $user->id;

        $jobs = $registrations->pluck('cod_matricula')
            ->chunk(self::ASYNC_THRESHOLD)
            ->map(fn ($chunk) => new UpdateRegistrationDateJob(
                registrationIds: $chunk->values()->toArray(),
                newDateRegistration: $newDateRegistration->format('Y-m-d'),
                newDateEnrollment: $newDateEnrollment?->format('Y-m-d'),
                ignoreRelocation: $remanejadas,
                databaseConnection: $databaseConnection,
                user: $user,
            ))
            ->toArray();

        $total = count($registrations);

        Bus::batch($jobs)
            ->finally(function (Batch $batch) use ($userId, $total) {
                $message = "Atualização de datas de matrícula concluída. {$total} matrículas atualizadas.";
                (new NotificationService)->createByUser($userId, $message, '/atualiza-data-entrada', NotificationType::OTHER);
            })
            ->dispatch();

        return redirect()->route('update-registration-date.index')
            ->with('success', sprintf('Serão atualizadas %s matrículas. Você será notificado no final do processo', $total));
    }

    private function addFilters(UpdateRegistrationDateRequest $request, $query)
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

        if ($request->get('ref_cod_turma')) {
            $schoolClassId = $request->get('ref_cod_turma');
            $query->whereHas('enrollments', function ($enrollmentQuery) use ($schoolClassId) {
                $enrollmentQuery->where('ref_cod_turma', $schoolClassId);
            });
            $query->with(['enrollments' => function ($enrollmentQuery) use ($schoolClassId) {
                $enrollmentQuery->where('ref_cod_turma', $schoolClassId);
            }]);
        }

        if ($request->get('ref_cod_serie')) {
            $query->where('ref_ref_cod_serie', $request->get('ref_cod_serie'));
        }

        $oldDataRegistration = $request->get('data_entrada_antiga') ? \DateTime::createFromFormat('d/m/Y', $request->get('data_entrada_antiga')) : null;
        if ($request->get('data_entrada_antiga')) {
            $query->where('data_matricula', $oldDataRegistration->format('Y-m-d'));
        }

        if ($request->get('data_enturmacao_antiga')) {
            $oldDataEnrollment = \DateTime::createFromFormat('d/m/Y', $request->get('data_enturmacao_antiga'));
            $query->whereHas('lastEnrollment', function ($enrollmentQuery) use ($oldDataEnrollment) {
                $enrollmentQuery->where('data_enturmacao', $oldDataEnrollment);
            });
        }

        if ($request->get('situacao')) {
            $query->where('aprovado', $request->get('situacao'));
        }

        return $query->get();
    }
}
