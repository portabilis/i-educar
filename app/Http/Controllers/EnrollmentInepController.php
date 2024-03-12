<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentInep;
use App\Models\LegacyEnrollment;
use Illuminate\Http\Request;

class EnrollmentInepController extends Controller
{
    public function edit(LegacyEnrollment $enrollment)
    {
        $this->breadcrumb('Educacenso Matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578); //dd($enrollment->toArray());

        return view('enrollments.enrollmentInep', compact('enrollment'));
    }

    public function update(Request $request, LegacyEnrollment $enrollment)
    {
        if ($request->has('desconsiderar_educacenso')) {
            $enrollment->update([
                'desconsiderar_educacenso' => true,
            ]);
        } else {
            $enrollment->update([
                'desconsiderar_educacenso' => false,
            ]);
        }

        EnrollmentInep::query()
            ->updateOrCreate(
                ['matricula_turma_id' => $enrollment->getKey()],
                ['matricula_inep' => is_numeric($request->get('matricula_inep')) ? $request->get('matricula_inep') : null]
            );

        return redirect()->route('enrollments.enrollment-history', $enrollment->registration->getKey());
    }
}
