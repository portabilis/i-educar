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
            'institution' => ['required_without_all:school,course','nullable','integer','min:1'],
            'school' => ['nullable','integer','min:1'],
            'not_pattern' => ['nullable','boolean'],
            'course' => ['nullable','integer','min:1']
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['data'=>[]]));
    }
}
