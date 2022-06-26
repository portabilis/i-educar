<?php

namespace App\Rules;

use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Illuminate\Contracts\Validation\Rule;

class RequiredEnrollmentItineraryCourse implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (in_array(TipoItinerarioFormativo::FORMACAO_TECNICA, $value->itineraryComposition) && empty($value->itineraryCourse)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O campo <b>Tipo do curso do itinerário de formação técnica e profissional</b> deve ser preenchido quando o campo <b>Composição do itinerário formativo integrado</b> for <b>Formação técnica profissional</b>.';
    }
}
