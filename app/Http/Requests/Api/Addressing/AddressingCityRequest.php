<?php

namespace App\Http\Requests\Api\Addressing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressingCityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'state_id' => ['required', 'integer', Rule::exists('states', 'id')],
            'name' => ['required', 'max:255', Rule::unique('cities')->where('state_id', $this->get('state_id'))->ignore($this->route('city'))],
            'ibge_code' => ['nullable', Rule::unique('cities')->ignore($this->route('city'))],
        ];
    }

    public function attributes(): array
    {
        return [
            'state_id' => 'Estado',
            'name' => 'Nome',
            'ibge_code' => 'Código Ibge',
        ];
    }
}
