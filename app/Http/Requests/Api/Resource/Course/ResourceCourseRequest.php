<?php

namespace App\Http\Requests\Api\Resource\Course;

use App\Http\Requests\Api\Resource\ResourceRequest;

class ResourceCourseRequest extends ResourceRequest
{
    public function rules(): array
    {
        return [
            'institution' => ['required_without_all:school,course', 'nullable', 'integer', 'min:1'],
            'school' => ['nullable', 'integer', 'min:1'],
            'standard_calendar' => ['nullable', 'boolean'],
            'course' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'institution' => 'Instituição',
            'school' => 'Escola',
            'standard_calendar' => 'Sem Padrão Escolar',
            'course' => 'Curso',
        ];
    }
}
