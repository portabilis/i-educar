<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistsPeriod implements Rule
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
        $dataInicio = $value->data_inicio;
        $possuiModulosInformados = (count($dataInicio) > 1 || $dataInicio[0] != '');

        if (!$possuiModulosInformados) {
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
        return 'Etapas não informadas.';
    }
}
