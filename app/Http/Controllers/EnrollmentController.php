<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentRequest;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Services\EnrollmentService;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Renderiza a view da enturmação.
     *
     * @param LegacyRegistration $registration
     * @param LegacySchoolClass  $schoolClass
     * @param EnrollmentService  $enrollmentService
     *
     * @return View
     */
    private function viewEnroll(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $this->breadcrumb('Enturmações da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $enableCancelButton = $enrollmentService->isEnrolled($schoolClass, $registration);
        $anotherClassroomEnrollments = $enrollmentService->anotherClassroomEnrollments($schoolClass, $registration);

        return view('enrollments.enroll', [
            'registration' => $registration,
            'schoolClass' => $schoolClass,
            'enableCancelButton' => $enableCancelButton,
            'anotherClassroomEnrollments' => $anotherClassroomEnrollments,
        ]);
    }

    /**
     * @param LegacyRegistration $registration
     * @param LegacySchoolClass  $schoolClass
     * @param EnrollmentService  $enrollmentService
     *
     * @return View
     */
    public function createEnroll(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        return $this->viewEnroll($registration, $schoolClass, $enrollmentService);
    }

    /**
     * @param EnrollmentRequest $request
     * @param LegacySchoolClass $schoolClass
     * @param EnrollmentService $enrollmentService
     *
     * @return View
     */
    public function enroll(
        EnrollmentRequest $request,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        return $this->viewEnroll($schoolClass, $registrations, $fails, $success);
    }
}
