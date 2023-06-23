<?php

namespace App\Http\Requests\Api\Resource\Discipline;

use App\Http\Requests\Api\Resource\ResourceRequest;

class ResourceDisciplineRequest extends ResourceRequest
{
    public function rules(): array
    {
        return [
            'course' => ['nullable', 'integer', 'min:1'],
            'grade' => ['required_without_all:course,school', 'required_with:school', 'nullable', 'integer', 'min:1'],
            'school' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'grade' => 'Série',
            'course' => 'Curso',
            'school' => 'Escola',
        ];
    }
}
