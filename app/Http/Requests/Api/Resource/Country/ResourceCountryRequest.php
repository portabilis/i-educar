<?php

namespace App\Http\Requests\Api\Resource\Country;

use Illuminate\Foundation\Http\FormRequest;

class ResourceCountryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'min:1', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
        ];
    }
}
