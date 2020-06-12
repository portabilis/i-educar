<?php

namespace App\Http\Requests;

use App_Model_MatriculaSituacao;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationDateRequest extends FormRequest
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
            'nova_data' => 'required|date_format:d/m/Y',
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
            'nova_data.required' => 'A nova data é obrigatória.',
            'nova_data.date_format' => 'A nova data é inválida.',
        ];
    }
}
