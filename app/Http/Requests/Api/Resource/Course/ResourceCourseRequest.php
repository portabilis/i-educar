<?php

namespace App\Http\Requests\Api\Resource\Course;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ResourceCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'institution' => ['required_without_all:school,course','nullable','integer'],
            'school' => ['nullable','integer'],
            'not_pattern' => ['nullable','boolean','boolean'],
            'course' => ['nullable','integer']
        ];
    }

    public function attributes()
    {
        return [
            'institution' => 'Instituição',
            'school' => 'Escola',
            'not_pattern' => 'Sem Padrão Escolar',
            'course' => 'Curso'
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
