<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ReligionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'unique:App\Models\Religion,name',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
        ];
    }
}
