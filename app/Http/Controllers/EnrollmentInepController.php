<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentInep;
use App\Models\LegacyEnrollment;
use Illuminate\Http\Request;

class EnrollmentInepController extends Controller
{
    public function edit(LegacyEnrollment $enrollment)
    {
        $this->breadcrumb('Código INEP da Matrícula do Aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        return view('enrollments.enrollmentInep', compact('enrollment'));
    }

    public function update(Request $request, LegacyEnrollment $enrollment)
    {
        $this->validate($request, [
            'matricula_inep' => [
                'required',
                'numeric',
                'digits:12',
            ],
        ], [
            'matricula_inep.required' => 'O campo Código INEP da Matrícula é obrigatório.',
            'matricula_inep.numeric' => 'O campo Código INEP da Matrícula deve conter apenas números.',
            'matricula_inep.digits' => 'O campo Código INEP da Matrícula deve conter 12 dígitos.',
        ]);

        EnrollmentInep::query()
            ->updateOrCreate(
                ['matricula_turma_id' => $enrollment->getKey()],
                ['matricula_inep' => $request->get('matricula_inep')]
            );

        return redirect()->route('enrollments.enrollment-history', $enrollment->registration->getKey());
    }
}
