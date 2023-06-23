<?php

namespace App\Rules;

use App\Models\Country;
use App\Models\State;
use App_Model_NivelTipoUsuario;
use Illuminate\Contracts\Validation\Rule;

class CityRestrictOperationRule implements Rule
{
    private $accessLevel;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $stateId = $value->getOriginal('state_id') ?? $value->state_id;

        $state = State::query()->find($stateId);

        if ($this->accessLevel === App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL) {
            return true;
        }

        return $state->country_id !== Country::BRASIL;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Não é permitido edição de municípios brasileiros, pois já estão previamente cadastrados.';
    }
}
