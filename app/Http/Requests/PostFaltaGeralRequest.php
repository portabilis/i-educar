<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostFaltaGeralRequest extends FormRequest
{
    public function rules()
    {
        return [
            'faltas' => [
                'required',
                'integer',
                'min:0',
            ],
            'etapa' => [
                'required',
                'integer',
                'min:1',
                'max:4',
            ],
            'aluno_id' => [
                'required',
                'integer',
            ],
            'turma_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
