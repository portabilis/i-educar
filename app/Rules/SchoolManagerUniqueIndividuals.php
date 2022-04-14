<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SchoolManagerUniqueIndividuals implements Rule
{
    /**
     * Verifica se a mesma pessoa foi selecionada mais de uma vez como gestor
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (count(array_unique($value)) < count($value)) {
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
        return 'Não é possível selecionar 2 gestores iguais';
    }
}
