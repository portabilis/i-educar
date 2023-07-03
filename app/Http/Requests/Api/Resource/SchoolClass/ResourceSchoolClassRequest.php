<?php

namespace App\Http\Requests\Api\Resource\SchoolClass;

use App\Http\Requests\Api\Resource\ResourceRequest;

class ResourceSchoolClassRequest extends ResourceRequest
{
    public function rules(): array
    {
        return [
            'institution' => ['required_without_all:school,grade,course', 'nullable', 'integer', 'min:1'],
            'school' => ['nullable', 'integer', 'min:1'],
            'grade' => ['nullable', 'integer', 'min:1'],
            'course' => ['nullable', 'integer', 'min:1'],
            'in_progress_year' => ['nullable', 'integer', 'digits:4'],
        ];
    }

    public function attributes()
    {
        return [
            'institution' => 'Instituição',
            'school' => 'Escola',
            'grade' => 'Serie',
            'course' => 'Curso',
            'in_progress_year' => 'Ano',
        ];
    }
}
