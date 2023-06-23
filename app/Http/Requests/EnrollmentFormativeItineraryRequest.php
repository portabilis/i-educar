<?php

namespace App\Http\Requests;

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
            'itinerary_composition' => ['nullable', 'array', 'max:4'],
            'itinerary_composition.*' => ['required', 'integer', Rule::in($itineraryCompositions)],
            'itinerary_course' => ['nullable', 'in:1,2'],
            'concomitant_itinerary' => ['nullable', 'boolean'],
            'technical_course' => ['nullable', 'integer', 'required_if:itinerary_course,1'],
        ];
    }

    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        $validator->sometimes(
            'itinerary_composition',
            'required',
            function ($input) {
                return in_array(TipoItinerarioFormativo::ITINERARIO_INTEGRADO, $input->itinerary_type ?: []);
            }
        );

        $validator->sometimes(
            'itinerary_course',
            'required',
            function ($input) {
                return in_array(TipoItinerarioFormativo::FORMACAO_TECNICA, $input->itinerary_composition ?: []);
            }
        );

        $validator->sometimes(
            'concomitant_itinerary',
            'required',
            function ($input) {
                return in_array(TipoItinerarioFormativo::FORMACAO_TECNICA, $input->itinerary_composition ?: []) &&
                    $input->show_concomitant_itinerary === '1';
            }
        );

        return $validator;
    }

    public function messages()
    {
        return [
            'itinerary_type.max' => 'O campo <b>Tipo do itinerário formativo</b> não pode ter mais de 4 opções selecionadas.',
            'itinerary_composition.max' => 'O campo <b>Composição do itinerário formativo integrado</b> não pode ter mais de 4 opções selecionadas.',
            'itinerary_composition.required' => 'O campo <b>Tipo do curso do itinerário de formação técnica e profissional</b> deve ser preenchido quando o campo <b>Composição do itinerário formativo integrado</b> for <b>Formação técnica profissional</b>.',
            'itinerary_course.required' => 'O campo <b>Tipo do curso do itinerário de formação técnica e profissional</b> deve ser preenchido quando o campo <b>Composição do itinerário formativo integrado</b> for <b>Formação técnica profissional</b>.',
            'concomitant_itinerary.required' => 'O campo <b>Itinerário concomitante intercomplementar à matrícula de formação geral básica</b> deve ser preenchido quando o campo <b>Composição do itinerário formativo integrado</b> for <b>Formação técnica profissional</b>.',
            'technical_course.required_if' => 'O campo <b>Código do curso técnico</b> deve ser preenchido quando o campo <b>Tipo do curso do itinerário de formação técnica e profissional</b> for <b>Curso técnico</b>.',
        ];
    }

    public function attributes()
    {
        return [
            'itinerary_type' => 'Tipo do itinerário formativo',
            'itinerary_composition' => 'Composição do itinerário formativo integrado',
            'itinerary_course' => 'Tipo do curso do itinerário de formação técnica e profissional',
            'concomitant_itinerary' => 'Itinerário concomitante intercomplementar à matrícula de formação geral básica',
            'technical_course' => 'Curso Técnico',
        ];
    }
}
