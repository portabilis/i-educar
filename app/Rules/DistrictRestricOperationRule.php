<?php

namespace App\Rules;

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App_Model_NivelTipoUsuario;
use clsPermissoes;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\This;

class DistrictRestricOperationRule implements Rule
{

    private int $accessLevel;

    public function __construct(int $accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  District  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $city = $value->getOriginal('city_id') ?? $value->city_id;

        $city = City::query()->find($city);

        return !($city->state->country_id === Country::BRASIL &&
            $this->accessLevel === App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Não é permitido exclusão de distritos brasileiros, pois já estão previamente cadastrados.';
    }
}
