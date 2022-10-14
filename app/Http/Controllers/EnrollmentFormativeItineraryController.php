<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentFormativeItineraryRequest;
use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use iEducar\Modules\Educacenso\Model\TipoCursoItinerario;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class EnrollmentFormativeItineraryController extends Controller
{

    public function index(int $id):View
    {
        $this->breadcrumb('Itinerário formativo do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $registration = LegacyRegistration::query()->with([
                'enrollments:id,ref_cod_matricula,ref_cod_turma,sequencial,ativo,data_enturmacao,data_exclusao',
                'enrollments.schoolClass:cod_turma,nm_turma,ano'
            ])
            ->findOrFail($id, ['cod_matricula']);

        return view('enrollments.enrollmentFormativeItineraryList', ['registration' => $registration]);
    }

    public function edit(int $registration, LegacyEnrollment $enrollment):View
    {
        $this->breadcrumb('Itinerário formativo do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        return view('enrollments.enrollmentFormativeItinerary', [
            'enrollment' => $enrollment,
            'itineraryType' => TipoItinerarioFormativo::getDescriptiveValues(),
            'itineraryComposition' => TipoItinerarioFormativo::getDescriptiveValuesOfItineraryComposition(),
            'itineraryCourse' => TipoCursoItinerario::getDescriptiveValues(),
        ]);
    }

    public function update(int $registration, LegacyEnrollment $enrollment, EnrollmentFormativeItineraryRequest $request):JsonResponse
    {
        $enrollment->tipo_itinerario = $request->get('itinerary_type');
        $enrollment->composicao_itinerario = $request->get('itinerary_composition');
        $enrollment->curso_itinerario = $request->get('itinerary_course');
        $enrollment->itinerario_concomitante = $request->get('concomitant_itinerary');

        $enrollment->save();

        session()->flash('success', 'Itinerário formativo salvo com sucesso.');

        return response()->json(
            [
                'registration_id' => $registration,
            ]
        );
    }
}
