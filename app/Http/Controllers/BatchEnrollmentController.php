<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelBatchEnrollmentRequest;
use App\Models\LegacySchoolClass;
use App\Services\EnrollmentService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Throwable;

class BatchEnrollmentController extends Controller
{
    /**
     * Renderiza a view.
     *
     * @param LegacySchoolClass $schoolClass
     * @param Collection        $enrollments
     * @param MessageBag        $fails
     * @param MessageBag        $success
     *
     * @return View
     */
    public function view(
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
     * Lista as enturmações da turma e possibilita a desenturmação em lote.
     *
     * @param LegacySchoolClass $schoolClass
     * @param EnrollmentService $enrollmentService
     *
     * @return View
     */
    public function index(
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $enrollments = $enrollmentService->getBySchoolClass(
            $schoolClass->id, $schoolClass->year
        );

        return $this->view($schoolClass, $enrollments);
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

        $enrollments = $enrollmentService->getBySchoolClass(
            $schoolClass->id, $schoolClass->year
        );

        foreach ($items as $enrollment) {
            try {
                $enrollmentService->cancelEnrollment($enrollment, $date);
                $success->add($enrollment, 'Aluno desenturmado.');
            } catch (Throwable $throwable) {
                $fails->add($enrollment, $throwable->getMessage());
            }
        }

        return $this->view($schoolClass, $enrollments, $fails, $success);
    }
}
