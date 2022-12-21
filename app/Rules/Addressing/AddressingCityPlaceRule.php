<?php

namespace App\Rules\Addressing;

use Illuminate\Contracts\Validation\Rule;

class AddressingCityPlaceRule implements Rule
{
    public function passes($attribute, $city): bool
    {
        return $city->places()->doesntExist();
    }

    public function message(): string
    {
        return 'Não é possível excluir a cidade, pois possui endereços vinculados!';
    }
}
