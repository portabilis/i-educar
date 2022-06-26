<?php

namespace App\Rules;

use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Illuminate\Contracts\Validation\Rule;

class RequiredEnrollmentItineraryComposition implements Rule
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
        if (in_array(TipoItinerarioFormativo::ITINERARIO_INTEGRADO, $value->itineraryType) && count($value->itineraryComposition) == 0) {
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
        return 'O campo <b>Composição do itinerário formativo integrado</b> deve ser preenchido quando o campo <b>Tipo do itinerário formativo</b> for <b>Itinerário formativo integrado</b>.';
    }
}
