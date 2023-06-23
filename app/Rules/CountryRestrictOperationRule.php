<?php

namespace App\Rules;

use App\Models\Country;
use App\Models\District;
use App_Model_NivelTipoUsuario;
use Illuminate\Contracts\Validation\Rule;

class CountryRestrictOperationRule implements Rule
{
    private int $accessLevel;

    public function __construct(int $accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string   $attribute
     * @param District $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $id = $value->getOriginal('id') ?? $value->id;

        if ($this->accessLevel === App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL) {
            return true;
        }

        return $id !== Country::BRASIL;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Não é permitido exclusão do Brasil, pois já está previamente cadastrado.';
    }
}
