<?php

namespace App\Http\Controllers;

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Services\EnrollmentFormativeItineraryService;
use iEducar\Modules\Educacenso\Model\TipoCursoItinerario;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use iEducar\Modules\ValueObjects\EnrollmentFormativeItineraryValueObject;
use Illuminate\Http\Request;

class EnrollmentFormativeItineraryController extends Controller
{
    /**
     * Lista enturmações da matrícula para definir itinerário formativo.
     *
     * @param int $id matrícula
     *
     * @return View
     */
    public function list($id)
    {
        $this->breadcrumb('Itinerário formativo do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $registration = LegacyRegistration::find($id);

        return view('enrollments.enrollmentFormativeItineraryList', ['registration' => $registration]);
    }

    /**
     * Lista enturmações da matrícula para definir itinerário formativo.
     *
     * @param int $id enturmação
     *
     * @return View
     */
    public function viewFormativeItinerary($id) {
        $this->breadcrumb('Itinerário formativo do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $enrollment = LegacyEnrollment::find($id);
        return view('enrollments.enrollmentFormativeItinerary', [
            'enrollment' => $enrollment,
            'itineraryType' => TipoItinerarioFormativo::getDescriptiveValues(),
            'itineraryComposition' => TipoItinerarioFormativo::getDescriptiveValuesOfItineraryComposition(),
            'itineraryCourse' => TipoCursoItinerario::getDescriptiveValues(),
        ]);
    }

    /**
     * @param Request  $request
     *
     * @return RedirectResponse
     */
    public function storeFormativeItinerary(Request $request)
    {
        $fields = $request->all();
        $enrollment = LegacyEnrollment::find($fields['enrollment_id']);

        if (!isset($fields['itinerary_type'])) {
            $fields['itinerary_type'] = [];
        }
        if (!isset($fields['itinerary_composition'])) {
            $fields['itinerary_composition'] = [];
        }
        if (!isset($fields['itinerary_course'])) {
            $fields['itinerary_course'] = null;
        }
        if (!isset($fields['concomitant_itinerary'])) {
            $fields['concomitant_itinerary'] = null;
        }

        $itineraryData = new EnrollmentFormativeItineraryValueObject();
        $itineraryData->enrollmentId = $fields['enrollment_id'];
        $itineraryData->itineraryType = $fields['itinerary_type'];
        $itineraryData->itineraryComposition = $fields['itinerary_composition'];
        $itineraryData->itineraryCourse = $fields['itinerary_course'];
        $itineraryData->concomitantItinerary = $fields['concomitant_itinerary'];

        $service = new EnrollmentFormativeItineraryService();

        try {
            $service->saveFormativeItinerary($enrollment, $itineraryData);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $enrollment->registration->id)->with('success', 'Itinerário formativo salvo com sucesso.');
    }
}
