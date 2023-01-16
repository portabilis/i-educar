<?php

namespace App\Rules\Addressing;

use Illuminate\Contracts\Validation\Rule;

class AddressingCityDistrictRule implements Rule
{
    public function passes($attribute, $city): bool
    {
        return $city->districts()->doesntExist();
    }

    public function message(): string
    {
        return 'Não é possível excluir a cidade, pois possui bairros vinculados!';
    }
}
