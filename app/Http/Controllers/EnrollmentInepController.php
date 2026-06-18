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

        $this->menu(578); // dd($enrollment->toArray());

        $podePreencherCargaHoraria = $enrollment->schoolClass->permiteCargaHorariaIntegralizada();

        return view('enrollments.enrollmentInep', compact('enrollment', 'podePreencherCargaHoraria'));
    }

    public function update(Request $request, LegacyEnrollment $enrollment)
    {
        $cargaHorariaIntegralizada = $request->input('carga_horaria_integralizada');
        $cargaHorariaIntegralizada = $enrollment->schoolClass->permiteCargaHorariaIntegralizada()
            && is_numeric($cargaHorariaIntegralizada)
            && $cargaHorariaIntegralizada >= 0
            && $cargaHorariaIntegralizada <= 9999
                ? (int) $cargaHorariaIntegralizada
                : null;

        $enrollment->update([
            'desconsiderar_educacenso' => $request->has('desconsiderar_educacenso'),
            'carga_horaria_integralizada' => $cargaHorariaIntegralizada,
        ]);

        EnrollmentInep::query()
            ->updateOrCreate(
                ['matricula_turma_id' => $enrollment->getKey()],
                ['matricula_inep' => is_numeric($request->get('matricula_inep')) ? $request->get('matricula_inep') : null]
            );

        return redirect()->route('enrollments.enrollment-history', $enrollment->registration->getKey());
    }
}
