<?php

namespace App\Http\Requests\Api\Addressing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressingCountryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', Rule::unique('countries')->ignore($this->route('country'))],
            'ibge_code' => ['nullable', Rule::unique('countries')->ignore($this->route('country'))],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'ibge_code' => 'Código Ibge',
        ];
    }
}
