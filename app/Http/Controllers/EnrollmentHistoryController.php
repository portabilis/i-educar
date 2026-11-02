<?php

namespace App\Http\Controllers;

use App\Models\LegacyRegistration;

class EnrollmentHistoryController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param int $id
     * @return View
     */
    public function show($id)
    {
        $this->breadcrumb('Histórico de enturmações da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $registration = LegacyRegistration::query()->findOrFail((int) $id);

        $user = auth()->user();

        abort_if(
            $user->isSchooling() && !$user->schools()->where('cod_escola', $registration->ref_ref_cod_escola)->exists(),
            403,
            'A matrícula informada não pertence às escolas do usuário.'
        );

        return view('enrollments.enrollmentHistory', ['registration' => $registration]);
    }
}
