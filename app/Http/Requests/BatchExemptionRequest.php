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
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'ano.required' => 'O ano é obrigatório.',
            'ano.date_format' => 'O campo Ano deve ser um ano válido.',
            'ref_cod_instituicao.required' => 'A instituição é obrigatória.',
            'ref_cod_componente_curricular.required' => 'Você precisa selecionar pelo menos um componente curricular.',
            'exemption_type.required' => 'O tipo de dispensa é obrigatório.',
            'stage.required' => 'Você precisa selecionar pelo menos uma etapa.',
        ];
    }
}
