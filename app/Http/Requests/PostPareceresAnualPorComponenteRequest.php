<?php

namespace App\Http\Requests;

use App\Rules\RegistrationExistsInSchoolClass;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Http\FormRequest;

class PostPareceresAnualPorComponenteRequest extends FormRequest
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
