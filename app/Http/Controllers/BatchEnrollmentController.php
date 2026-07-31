<?php

namespace App\Http\Controllers;

use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Exceptions\Registration\RegistrationException;
use App\Http\Requests\BatchEnrollmentRequest;
use App\Http\Requests\CancelBatchEnrollmentRequest;
use App\Http\Requests\CancelBatchRegistrationRequest;
use App\Models\LegacySchoolClass;
use App\Process;
use App\Services\EnrollmentService;
use App\Services\RegistrationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Throwable;

class BatchEnrollmentController extends Controller
{
    private const SCHOOL_INDEX_URL = 'intranet/educar_index.php';

    /**
     * Renderiza a view da desenturmação em lote.
     *
     * @return View
     */
    public function viewCancelEnrollments(
        LegacySchoolClass $schoolClass,
        Collection $enrollments,
        ?MessageBag $fails = null,
        ?MessageBag $success = null
    ) {
        $this->breadcrumb('Desenturmar em lote', [
            url(self::SCHOOL_INDEX_URL) => 'Escola',
        ]);

        $this->menu(659); // Código: ieducar/intranet/educar_matriculas_turma_lst.php

        $this->setMessages($fails, $success, 'cancel');

        return view('enrollments.batch.cancel', [
            'schoolClass' => $schoolClass,
            'enrollments' => $enrollments,
            'fails' => $fails ?? new MessageBag,
            'success' => $success ?? new MessageBag,
            'canCancelRegistration' => $this->canCancelRegistration(),
        ]);
    }

    /**
     * Renderiza a view da enturmação em lote.
     *
     * @return View
     */
    public function viewEnroll(
        LegacySchoolClass $schoolClass,
        Collection $registrations,
        ?MessageBag $fails = null,
        ?MessageBag $success = null
    ) {
        $this->breadcrumb('Enturmar em lote', [
            url(self::SCHOOL_INDEX_URL) => 'Escola',
        ]);

        $this->menu(659); // Código: ieducar/intranet/educar_matriculas_turma_lst.php

        $this->setMessages($fails, $success, 'enroll');

        return view('enrollments.batch.enroll', [
            'schoolClass' => $schoolClass,
            'registrations' => $registrations,
            'fails' => $fails ?? new MessageBag,
            'success' => $success ?? new MessageBag,
            'canCancelRegistration' => $this->canCancelRegistration(),
        ]);
    }

    /**
     * Lista as enturmações da turma e possibilita a desenturmação em lote.
     *
     *
     * @return View
     */
    public function indexCancelEnrollments(
        LegacySchoolClass $schoolClass
    ) {
        return $this->viewCancelEnrollments($schoolClass, $schoolClass->getActiveEnrollments());
    }

    /**
     * Desenturma as enturmações enviadas e renderiza a view.
     *
     *
     * @return View
     */
    public function cancelEnrollments(
        CancelBatchEnrollmentRequest $request,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $date = Carbon::createFromFormat('d/m/Y', $request->input('date'));
        $enrollmentsIds = $request->input('enrollments', []);

        $fails = new MessageBag;
        $success = new MessageBag;

        $enrollments = $schoolClass->getActiveEnrollments();

        foreach ($enrollmentService->findAll($enrollmentsIds) as $enrollment) {
            try {
                $enrollmentService->cancelEnrollment($enrollment, $date);
                $success->add($enrollment->id, 'Aluno desenturmado.');
            } catch (Throwable $throwable) {
                $fails->add($enrollment->id, $throwable->getMessage());
            }
        }

        return $this->viewCancelEnrollments($schoolClass, $enrollments, $fails, $success);
    }

    /**
     * @return View
     */
    public function indexEnroll(
        LegacySchoolClass $schoolClass,
        RegistrationService $registrationService
    ) {
        $registrations = $registrationService->getRegistrationsNotEnrolled($schoolClass);

        $registrations = $registrations->sortBy(function ($registration) {
            return $registration->student->person->name;
        });

        return $this->viewEnroll($schoolClass, $registrations);
    }

    /**
     * @return View
     */
    public function enroll(
        BatchEnrollmentRequest $request,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService,
        RegistrationService $registrationService
    ) {
        $date = Carbon::createFromFormat('d/m/Y', $request->input('date'));
        $registrationsIds = $request->input('registrations', []);

        $fails = new MessageBag;
        $success = new MessageBag;

        $registrations = $registrationService->getRegistrationsNotEnrolled($schoolClass);

        foreach ($registrationService->findAll($registrationsIds) as $registration) {
            try {
                $enrollmentService->enroll($registration, $schoolClass, $date);
                $success->add($registration->id, 'Aluno enturmado.');
            } catch (ExistsActiveEnrollmentException $throwable) {
                $registrations->push($registration);
                $fails->add($registration->id, $throwable->getMessage());
            } catch (Throwable $throwable) {
                $fails->add($registration->id, $throwable->getMessage());
            }
        }

        $registrations = $registrations->sortBy(function ($registration) {
            return $registration->student->person->name;
        });

        return $this->viewEnroll($schoolClass, $registrations, $fails, $success);
    }

    /**
     * Renderiza a view do cancelamento de matrícula em lote.
     *
     * @return View
     */
    public function viewCancelRegistrations(
        LegacySchoolClass $schoolClass,
        Collection $enrollments,
        ?MessageBag $fails = null,
        ?MessageBag $success = null
    ) {
        $this->breadcrumb('Cancelar matrícula em lote', [
            url(self::SCHOOL_INDEX_URL) => 'Escola',
        ]);

        $this->menu(659);

        $this->setMessages($fails, $success, 'cancel-registration');

        return view('enrollments.batch.cancel-registration', [
            'schoolClass' => $schoolClass,
            'enrollments' => $enrollments,
            'fails' => $fails ?? new MessageBag,
            'success' => $success ?? new MessageBag,
        ]);
    }

    /**
     * Lista as enturmações da turma e possibilita o cancelamento de matrícula em lote.
     *
     * @return View
     */
    public function indexCancelRegistrations(
        LegacySchoolClass $schoolClass
    ) {
        $this->authorizeSchoolClassAccess($schoolClass);

        return $this->viewCancelRegistrations($schoolClass, $schoolClass->getActiveEnrollmentsWithCancellableRegistration());
    }

    /**
     * Cancela as matrículas selecionadas e renderiza a view.
     *
     * @return View
     */
    public function cancelRegistrations(
        CancelBatchRegistrationRequest $request,
        LegacySchoolClass $schoolClass,
        RegistrationService $registrationService
    ) {
        $this->authorizeSchoolClassAccess($schoolClass);

        $registrationIds = $request->input('registrations', []);

        $fails = new MessageBag;
        $success = new MessageBag;

        // A listagem é carregada antes do cancelamento para que as matrículas
        // canceladas continuem visíveis na tabela com o resultado de cada uma.
        $enrollments = $schoolClass->getActiveEnrollmentsWithCancellableRegistration();

        // Cancela apenas as matrículas efetivamente listadas para a turma, para
        // que um POST com identificadores alheios à listagem não alcance
        // matrículas de outra turma ou fora do critério de cancelamento.
        $registrations = $enrollments
            ->pluck('registration')
            ->unique('cod_matricula')
            ->whereIn('cod_matricula', $registrationIds);

        foreach ($registrations as $registration) {
            try {
                $registrationService->cancelRegistration($registration, reorderSchoolClasses: false);
                $success->add($registration->getKey(), 'Matrícula cancelada.');
            } catch (RegistrationException $exception) {
                $fails->add($registration->getKey(), $exception->getMessage());
            } catch (Throwable $throwable) {
                report($throwable);

                $fails->add($registration->getKey(), 'Não foi possível cancelar a matrícula.');
            }
        }

        // A reordenação dos sequenciais é feita uma única vez por turma, ao
        // final, evitando renumerar a mesma turma a cada matrícula cancelada.
        if ($success->isNotEmpty()) {
            $registrationService->reorderSchoolClassesForRegistrations($success->keys());
        }

        return $this->viewCancelRegistrations(
            $schoolClass,
            $enrollments,
            $fails,
            $success
        );
    }

    /**
     * Garante que usuários de nível escola só operem turmas das próprias escolas.
     */
    private function authorizeSchoolClassAccess(LegacySchoolClass $schoolClass): void
    {
        $user = auth()->user();

        if (! $user?->isSchooling()) {
            return;
        }

        abort_unless(
            $user->schools()->where('cod_escola', $schoolClass->ref_ref_cod_escola)->exists(),
            403,
            'A turma informada não pertence às escolas do usuário.'
        );
    }

    /**
     * Indica se o usuário pode cancelar matrículas, com as mesmas permissões
     * que o cancelamento individual exige para executar: cadastrar em alunos,
     * verificada na abertura de `educar_matricula_cad.php`, e excluir em
     * cancelar matrícula, verificada antes de efetivar o cancelamento.
     */
    private function canCancelRegistration(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->can('modify', Process::REGISTRATIONS)
            && $user->can('remove', Process::CANCEL_REGISTRATION);
    }

    /**
     * @return void
     */
    protected function setMessages(
        ?MessageBag $fail,
        ?MessageBag $success,
        string $type = 'enroll'
    ) {
        $fail = $fail ?? new MessageBag;
        $success = $success ?? new MessageBag;

        switch ($type) {
            case 'enroll':
                if ($fail->count() === 1) {
                    Session::now('error', 'Não foi possível enturmar 1 aluno.');
                } elseif ($fail->count() > 1) {
                    Session::now('error', sprintf('Não foi possível enturmar %d alunos.', $fail->count()));
                }

                if ($success->count() === 1) {
                    Session::now('success', 'Foi enturmado 1 aluno.');
                } elseif ($success->count() > 1) {
                    Session::now('success', sprintf('Foram enturmados %s alunos.', $success->count()));
                }

                break;
            case 'cancel':
                if ($fail->count() === 1) {
                    Session::now('error', 'Não foi possível desenturmar 1 aluno.');
                } elseif ($fail->count() > 1) {
                    Session::now('error', sprintf('Não foi possível desenturmar %d alunos.', $fail->count()));
                }

                if ($success->count() === 1) {
                    Session::now('success', 'Foi desenturmado 1 aluno.');
                } elseif ($success->count() > 1) {
                    Session::now('success', sprintf('Foram desenturmados %s alunos.', $success->count()));
                }

                break;
            case 'cancel-registration':
                if ($fail->count() === 1) {
                    Session::now('error', 'Não foi possível cancelar 1 matrícula.');
                } elseif ($fail->count() > 1) {
                    Session::now('error', sprintf('Não foi possível cancelar %d matrículas.', $fail->count()));
                }

                if ($success->count() === 1) {
                    Session::now('success', 'Foi cancelada 1 matrícula.');
                } elseif ($success->count() > 1) {
                    Session::now('success', sprintf('Foram canceladas %s matrículas.', $success->count()));
                }

                break;
        }
    }
}
