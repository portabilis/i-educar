<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolClassReportCardRequest extends FormRequest
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
            'new_report_card' => 'required_without:new_alternative_report_card',
            'new_alternative_report_card' => 'required_without:new_report_card',
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
            'new_report_card.required_without' => 'O novo tipo de boletim é obrigatório.',
            'new_alternative_report_card.required_without' => 'O novo tipo de boletim diferenciado é obrigatório.',
        ];
    }
}
