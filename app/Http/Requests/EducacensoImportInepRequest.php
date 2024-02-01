<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducacensoImportInepRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ano' => [
                'required',
                'date_format:Y',
            ],
            'arquivos' => ['required',  'array', 'max:500'],
            'arquivos.*' => ['required', 'file', 'max:5000', 'mimes:txt'],
        ];
    }

    public function attributes()
    {
        return [
            'arquivos' => 'Arquivos',
            'arquivos.*' => 'Arquivos',
        ];
    }

    public function messages()
    {
        return [
            'ano.required' => 'Você precisa informar o ano',
        ];
    }
}
