<?php

namespace App\Services;

use App\Models\LegacyEnrollment;
use iEducar\Modules\ValueObjects\EnrollmentFormativeItineraryValueObject;

class EnrollmentFormativeItineraryService
{
    public function saveFormativeItinerary(
        LegacyEnrollment $enrollment,
        EnrollmentFormativeItineraryValueObject $itineraryData
    )
    {
        $enrollment->tipo_itinerario = $this->convertArrayToDBField($itineraryData->itineraryType);
        $enrollment->composicao_itinerario = $this->convertArrayToDBField($itineraryData->itineraryComposition);
        $enrollment->curso_itinerario = $itineraryData->itineraryCourse;
        $enrollment->itinerario_concomitante = $itineraryData->concomitantItinerary;

        $enrollment->save();
    }

    private function convertArrayToDBField($field)
    {
        if (is_array($field)) {
            return '{' . implode(',', $field) . '}';
        }

        return null;
    }
}
