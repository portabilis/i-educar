<?php

namespace App\Http\Controllers;

use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Http\Requests\BatchEnrollmentRequest;
use App\Http\Requests\CancelBatchEnrollmentRequest;
use App\Models\LegacySchoolClass;
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
    /**
     * Renderiza a view da desenturmação em lote.
     *
     * @param LegacySchoolClass $schoolClass
     * @param Collection        $enrollments
     * @param MessageBag        $fails
     * @param MessageBag        $success
     *
     * @return View
     */
    public function viewCancelEnrollments(
        LegacySchoolClass $schoolClass,
        Collection $enrollments,
        MessageBag $fails = null,
        MessageBag $success = null
    ) {
        $this->breadcrumb('Desenturmar em lote', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(659); // Código: ieducar/intranet/educar_matriculas_turma_lst.php

        $this->setMessages($fails, $success, 'cancel');

        return view('enrollments.batch.cancel', [
            'schoolClass' => $schoolClass,
            'enrollments' => $enrollments,
            'fails' => $fails ?? new MessageBag(),
            'success' => $success ?? new MessageBag(),
        ]);
    }

    /**
     * Renderiza a view da enturmação em lote.
     *
     * @param LegacySchoolClass $schoolClass
     * @param Collection        $registrations
     * @param MessageBag        $fails
     * @param MessageBag        $success
     *
     * @return View
     */
    public function viewEnroll(
        LegacySchoolClass $schoolClass,
        Collection $registrations,
        MessageBag $fails = null,
        MessageBag $success = null
    ) {
        $this->breadcrumb('Enturmar em lote', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(659); // Código: ieducar/intranet/educar_matriculas_turma_lst.php

        $this->setMessages($fails, $success, 'enroll');

        return view('enrollments.batch.enroll', [
            'schoolClass' => $schoolClass,
            'registrations' => $registrations,
            'fails' => $fails ?? new MessageBag(),
            'success' => $success ?? new MessageBag(),
        ]);
    }

    /**
     * Lista as enturmações da turma e possibilita a desenturmação em lote.
     *
     * @param LegacySchoolClass $schoolClass
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
     * @param CancelBatchEnrollmentRequest $request
     * @param LegacySchoolClass            $schoolClass
     * @param EnrollmentService            $enrollmentService
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

        $fails = new MessageBag();
        $success = new MessageBag();

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
     * @param LegacySchoolClass   $schoolClass
     * @param RegistrationService $registrationService
     *
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
     * @param BatchEnrollmentRequest $request
     * @param LegacySchoolClass      $schoolClass
     * @param EnrollmentService      $enrollmentService
     * @param RegistrationService    $registrationService
     *
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

        $fails = new MessageBag();
        $success = new MessageBag();

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
     * @param MessageBag $fail
     * @param MessageBag $success
     * @param string     $type
     *
     * @return void
     */
    protected function setMessages(
        ?MessageBag $fail,
        ?MessageBag $success,
        string $type = 'enroll'
    ) {
        $fail = $fail ?? new MessageBag();
        $success = $success ?? new MessageBag();

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
        }
    }
}
