<?php

namespace App\Http\Requests\Api\Addressing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressingDistrictRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')],
            'name' => ['required', 'max:255'],
            'ibge_code' => ['nullable', 'integer', Rule::unique('districts')->ignore($this->route('district'))],
        ];
    }

    public function attributes(): array
    {
        return [
            'city_id' => 'Cidade',
            'name' => 'Nome',
            'ibge_code' => 'Código Ibge',
        ];
    }
}
