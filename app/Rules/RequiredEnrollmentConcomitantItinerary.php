<?php

namespace App\Rules;

use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Illuminate\Contracts\Validation\Rule;

class RequiredEnrollmentConcomitantItinerary implements Rule
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
        if (in_array(TipoItinerarioFormativo::FORMACAO_TECNICA, $value->itineraryComposition) && $value->concomitantItinerary === null) {
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
        return 'O campo <b>Itinerário concomitante intercomplementar à matrícula de formação geral básica</b> deve ser preenchido quando o campo <b>Composição do itinerário formativo integrado</b> for <b>Formação técnica profissional</b>.';
    }
}
