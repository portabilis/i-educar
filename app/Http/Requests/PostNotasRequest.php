<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;

class PostNotasRequest extends FormRequest
{
    public function rules()
    {
        return [
            // Só número decimal comum; o numeric aceitaria notação científica, como 1e1.
            'nota' => [
                'required',
                'numeric',
                'regex:/^-?\d+(\.\d+)?$/',
            ],
            'recuperacao' => [
                'nullable',
                'numeric',
                'regex:/^-?\d+(\.\d+)?$/',
            ],
            // O boletim valida a etapa pelo calendário da matrícula.
            'etapa' => [
                'required',
                'regex:/^([1-9][0-9]*|Rc)$/',
            ],
            'aluno_id' => [
                'required',
                'integer',
                new RegistrationExistsInSchoolClass(
                    service: app(DiarioControllerService::class),
                    schoolClassId: $this->integer('turma_id'),
                ),
            ],
            'turma_id' => [
                'required',
                'integer',
            ],
            'componente_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
