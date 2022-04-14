<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchExemptionRequest extends FormRequest
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
            'ref_cod_componente_curricular' => 'required',
            'exemption_type' => 'required',
            'stage' => 'required',
            'ref_cod_curso' => 'required',
            'ref_cod_escola' => 'required',
            'stage_type' => 'required'
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
            'ref_cod_componente_curricular.required' => 'Você precisa selecionar pelo menos um componente curricular.',
            'exemption_type.required' => 'Você precisa selecionar o tipo de dispensa.',
            'stage.required' => 'Você precisa selecionar pelo menos uma etapa.',
            'ref_cod_curso.required' => 'Você precisa selecionar um curso.',
            'ref_cod_escola.required' => 'Você precisa selecionar uma escola.',
            'stage_type.required' => 'Você precisa selecionar o tipo de etapa.',
        ];
    }
}
