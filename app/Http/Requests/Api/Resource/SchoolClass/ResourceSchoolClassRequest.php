<?php

namespace App\Http\Requests\Api\Resource\SchoolClass;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResourceSchoolClassRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'institution' => ['required_without_all:school,grade,course','nullable','integer'],
            'school' => ['nullable', 'integer'],
            'grade' => ['nullable', 'integer'],
            'course'=> ['nullable', 'integer'],
            'year' => ['nullable', 'integer', 'digits:4']
        ];
    }

    public function attributes()
    {
        return [
            'institution' => 'Instituição',
            'school' => 'Escola',
            'grade' => 'Serie',
            'course' => 'Curso',
            'year' => 'Ano'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([]));
    }
}
