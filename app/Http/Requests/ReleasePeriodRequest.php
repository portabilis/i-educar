<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleasePeriodRequest extends FormRequest
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
            'escola' => 'required',
            'stage_type' => 'required',
            'stage' => 'required',
            'start_date.*' => 'required|date_format:d/m/Y|distinct|min:1',
            'end_date.*' => 'required|date_format:d/m/Y|distinct|min:1',
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
            'escola.required' => 'A escola é obrigatória.',
            'stage_type.required' => 'O tipo de etapa é obrigatório.',
            'stage.required' => 'A etapa é obrigatória.',
            'start_date.*.required' => 'Informe pelo menos uma data de início',
            'end_date.*.required' => 'Informe pelo menos uma data fim',
            'start_date.*.distinct' => 'A data :input já foi usada',
            'end_date.*.distinct' => 'A data :input já foi usada',
            'start_date.*.date_format' => 'A data :input é inválida',
            'end_date.*.date_format' => 'A data :input é inválida',
        ];
    }
}
