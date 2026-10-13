<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;

class PostPareceresPorEtapaComponenteRequest extends FormRequest
{
    public function rules()
    {
        return [
            // Parecer vazio limpa o parecer lançado, como no resource legado.
            'parecer' => [
                'present',
                'nullable',
                'string',
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
