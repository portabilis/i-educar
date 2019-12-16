<?php

namespace App\Http\Requests;

use App_Model_MatriculaSituacao;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->getTransferValidations();
        return array_merge($rules, [
            'ano' => [
                'required',
                'date_format:Y',
            ],
            'ref_cod_instituicao' => 'required',
            'situacao' => 'required',
            'nova_situacao' => 'required',
        ]);
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
            'situacao.required' => 'A situação é obrigatória.',
            'nova_situacao.required' => 'A nova situação é obrigatória.',
            'transferencia_tipo.required' => 'O motivo da transferência é obrigatório.',
            'transferencia_data.required' => 'A data da transferência é obrigatória.',
        ];
    }

    private function getTransferValidations()
    {
        if ($this->nova_situacao == App_Model_MatriculaSituacao::TRANSFERIDO) {
            return [
                'transferencia_tipo' => 'required',
                'transferencia_data' => 'required',
            ];
        }

        return [];
    }
}
