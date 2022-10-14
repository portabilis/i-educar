<?php

namespace App\Http\Requests;

use App\Rules\RequiredEnrollmentConcomitantItinerary;
use App\Rules\RequiredEnrollmentItineraryComposition;
use App\Rules\RequiredEnrollmentItineraryCourse;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentFormativeItineraryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $itineraryTypes = array_keys(TipoItinerarioFormativo::getDescriptiveValues());
        $itineraryCompositions = array_keys(TipoItinerarioFormativo::getDescriptiveValuesOfItineraryComposition());

        return [
            'itinerary_type' => 'nullable|array|max:4',
            'itinerary_type.*' => ['required', 'integer', Rule::in($itineraryTypes)],
            'itinerary_composition' => [new RequiredEnrollmentItineraryComposition(), 'array', 'max:4'],
            'itinerary_composition.*' => ['required', 'integer', Rule::in($itineraryCompositions)],
            'itinerary_course' => [new RequiredEnrollmentItineraryCourse($this->get('itinerary_composition')), 'in:1,2'],
            'concomitant_itinerary' => [new RequiredEnrollmentConcomitantItinerary($this->get('itinerary_composition')), 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'itinerary_type.max' => 'O campo <b>Tipo do itinerário formativo</b> não pode ter mais de 4 opções selecionadas.',
            'itinerary_composition.max' => 'O campo <b>Composição do itinerário formativo integrado</b> não pode ter mais de 4 opções selecionadas.',
        ];
    }
}
