<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;

class PostFaltaComponenteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'faltas' => [
                'required',
                'integer',
                'min:0',
            ],
            'etapa' => [
                'required',
                'integer',
                'min:1',
                'max:4',
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
            'area_do_conhecimento_id' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
