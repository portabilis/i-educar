<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileExportRequest extends FormRequest
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
            'ref_cod_instituicao' => 'required',
            'ref_cod_escola' => 'required',
            'ref_cod_curso' => 'required',
            'ref_cod_serie' => 'required',
            'ref_cod_turma' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'ano.required' => 'Você precisa informar o ano',
            'ano.date_format' => 'Você precisa informar um ano válido.',
            'ref_cod_instituicao.required' => 'Você precisa selecionar a instituição.',
            'ref_cod_curso.required' => 'Você precisa selecionar um curso.',
            'ref_cod_escola.required' => 'Você precisa selecionar uma escola.',
            'ref_cod_serie.required' => 'Você precisa selecionar uma série.',
            'ref_cod_turma.required' => 'Você precisa selecionar uma turma.',
        ];
    }
}
