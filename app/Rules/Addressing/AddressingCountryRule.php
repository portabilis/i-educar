<?php

namespace App\Rules\Addressing;

use Illuminate\Contracts\Validation\Rule;

class AddressingCountryRule implements Rule
{
    public function passes($attribute, $country): bool
    {
        return $country->states()->doesntExist();
    }

    public function message(): string
    {
        return 'Não é possível excluir o país, pois possui estados vinculados!';
    }
}
