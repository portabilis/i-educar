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
            'itinerary_composition' => [new RequiredEnrollmentItineraryComposition(), 'nullable', 'array', 'max:4'],
            'itinerary_composition.*' => ['required', 'integer', Rule::in($itineraryCompositions)],
            'itinerary_course' => [new RequiredEnrollmentItineraryCourse($this->get('itinerary_composition')), 'nullable', 'in:1,2'],
            'concomitant_itinerary' => [new RequiredEnrollmentConcomitantItinerary($this->get('itinerary_composition')), 'nullable', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'itinerary_type.max' => 'O campo <b>Tipo do itinerário formativo</b> não pode ter mais de 4 opções selecionadas.',
            'itinerary_composition.max' => 'O campo <b>Composição do itinerário formativo integrado</b> não pode ter mais de 4 opções selecionadas.',
        ];
    }

    public function attributes()
    {
        return [
            'itinerary_type' => 'Tipo do itinerário formativo',
            'itinerary_composition' => 'Composição do itinerário formativo integrado',
            'itinerary_course' => 'Tipo do curso do itinerário de formação técnica e profissional',
            'concomitant_itinerary' => 'Itinerário concomitante intercomplementar à matrícula de formação geral básica',
        ];
    }


}
