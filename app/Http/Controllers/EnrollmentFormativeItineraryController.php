<?php

namespace App\Http\Controllers;

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Services\EnrollmentFormativeItineraryService;
use iEducar\Modules\Educacenso\Model\TipoCursoItinerario;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use iEducar\Modules\ValueObjects\EnrollmentFormativeItineraryValueObject;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

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
     * @return Application|Factory|View
     */
    public function viewFormativeItinerary($id)
    {
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
     * @param Request $request
     *
     * @return JsonResponse|Redirector|RedirectResponse|Application
     */
    public function storeFormativeItinerary(Request $request): JsonResponse|Redirector|RedirectResponse|Application
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
            return response()->json(
                ['message' => $th->getMessage()],
                400
            );
        }

        return response()->json(
            [
                'registration_id' => $enrollment->registration_id,
                'message' => 'Itinerário formativo salvo com sucesso.'
            ]
        );
    }
}
