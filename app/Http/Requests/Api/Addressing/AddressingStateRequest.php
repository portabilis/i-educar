<?php

namespace App\Http\Requests\Api\Addressing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressingStateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', Rule::unique('states')->ignore($this->route('state'))],
            'abbreviation' => ['required', 'min:2', 'max:3', Rule::unique('states')->ignore($this->route('state'))],
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'ibge_code' => ['nullable', 'integer', Rule::unique('states')->ignore($this->route('state'))],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'abbreviation' => 'Abreviatura',
            'country_id' => 'País',
            'ibge_code' => 'Código Ibge',
        ];
    }
}
