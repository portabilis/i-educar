<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostNotasRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nota' => [
                'required',
                'numeric',
            ],
            'recuperacao' => [
                'nullable',
                'numeric',
            ],
            'etapa' => [
                'required',
                Rule::in([1, 2, 3, 4, 'Rc']),
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
                'nullable',
                'integer',
            ],
        ];
    }
}
