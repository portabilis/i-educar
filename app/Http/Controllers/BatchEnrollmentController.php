<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchEnrollmentRequest;
use App\Http\Requests\CancelBatchEnrollmentRequest;
use App\Models\LegacySchoolClass;
use App\Services\EnrollmentService;
use App\Services\RegistrationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
     * @param EnrollmentService $enrollmentService
     *
     * @return View
     */
    public function indexCancelEnrollments(
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $enrollments = $enrollmentService->getBySchoolClass($schoolClass);

        return $this->viewCancelEnrollments($schoolClass, $enrollments);
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
        $items = $request->input('enrollments', []);

        $fails = new MessageBag();
        $success = new MessageBag();

        $enrollments = $enrollmentService->getBySchoolClass($schoolClass);

        foreach ($items as $enrollment) {
            try {
                $enrollmentService->cancelEnrollment($enrollment, $date);
                $success->add($enrollment, 'Aluno desenturmado.');
            } catch (Throwable $throwable) {
                $fails->add($enrollment, $throwable->getMessage());
            }
        }

        return $this->viewCancelEnrollments($schoolClass, $enrollments, $fails, $success);
    }

    /**
     * @param LegacySchoolClass $schoolClass
     * @param EnrollmentService $enrollmentService
     *
     * @return View
     */
    public function indexEnroll(
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $registrations = $enrollmentService->getRegistrationsNotEnrolled($schoolClass);

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
        $items = $request->input('registrations', []);

        $fails = new MessageBag();
        $success = new MessageBag();

        $registrations = $enrollmentService->getRegistrationsNotEnrolled($schoolClass);

        $registrations = $registrations->sortBy(function ($registration) {
            return $registration->student->person->name;
        });

        foreach ($registrationService->findAll($items) as $registration) {
            try {
                $enrollmentService->enroll($registration, $schoolClass, $date);
                $success->add($registration->id, 'Aluno enturmado.');
            } catch (Throwable $throwable) {
                $fails->add($registration->id, $throwable->getMessage());
            }
        }

        return $this->viewEnroll($schoolClass, $registrations, $fails, $success);
    }
}
