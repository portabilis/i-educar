<?php

namespace App\Rules\Addressing;

use Illuminate\Contracts\Validation\Rule;

class AddressingStateRule implements Rule
{
    public function passes($attribute, $state): bool
    {
        return $state->cities()->doesntExist();
    }

    public function message(): string
    {
        return 'Não é possível excluir o estado, pois possui cidades vinculadas!';
    }
}
