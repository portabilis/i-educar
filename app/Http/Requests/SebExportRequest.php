<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SebExportRequest extends FormRequest
{
    public function rules() : array
    {
        return [
            'ano' => [
                'required',
                'date_format:Y',
            ],
            'ref_cod_instituicao' => 'required',
        ];
    }

    public function messages() : array
    {
        return [
            'ano.required' => 'O ano é obrigatório.',
            'ano.date_format' => 'O campo ano deve ser um ano válido.',
            'ref_cod_instituicao.required' => 'A instituição é obrigatória.',
        ];
    }
}
