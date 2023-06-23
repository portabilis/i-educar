<?php

namespace App\Http\Requests\Api\Resource\SchoolAcademicYear;

use App\Http\Requests\Api\Resource\ResourceRequest;

class ResourceSchoolAcademicYearRequest extends ResourceRequest
{
    public function rules(): array
    {
        return [
            'school' => ['required', 'integer', 'min:1'],
            'year_gte' => ['nullable', 'integer', 'digits:4'],
            'limit' => ['nullable', 'integer'],
        ];
    }

    public function attributes()
    {
        return [
            'school' => 'Escola',
            'year_gte' => 'Ano',
        ];
    }
}
