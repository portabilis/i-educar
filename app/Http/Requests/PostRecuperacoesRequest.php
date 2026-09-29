<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;

class PostRecuperacoesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'recuperacao' => [
                'required',
                'numeric',
            ],
            'nota' => [
                'nullable',
                'numeric',
            ],
            // O boletim valida a etapa pelo calendário da matrícula.
            'etapa' => [
                'required',
                'integer',
                'min:1',
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
