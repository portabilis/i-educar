<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockEnrollmentRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'bloquear_enturmacao_sem_vagas' => $this->has('bloquear_enturmacao_sem_vagas') ? 1 : 0,
        ]);
    }

    public function rules()
    {
        return [
            'ano' => ['required', 'integer'],
            'ref_cod_instituicao' => ['required', 'integer'],
            'bloquear_enturmacao_sem_vagas' => ['required', 'in:0,1'],
        ];
    }
}
