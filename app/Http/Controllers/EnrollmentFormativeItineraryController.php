<?php

namespace App\Http\Controllers;

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use iEducar\Modules\Educacenso\Model\TipoCursoItinerario;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;

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
}
