<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;

class PostFaltaGeralRequest extends FormRequest
{
    public function rules()
    {
        return [
            'faltas' => [
                'required',
                'integer',
                'min:0',
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
        ];
    }
}
