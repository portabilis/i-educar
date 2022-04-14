<?php

namespace App\Http\Controllers;

use App\Models\LegacyRegistration;

class EnrollmentHistoryController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param int $id
     *
     * @return View
     */
    public function show($id)
    {
        $this->breadcrumb('Histórico de enturmações da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $registration = LegacyRegistration::find($id);

        return view('enrollments.enrollmentHistory', ['registration' => $registration]);
    }
}
