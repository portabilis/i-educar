<?php

namespace App\Http\Requests\Api\Resource\Grade;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResourceGradeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course' => ['required_without:school','nullable', 'integer','min:1'],
            'school' => ['nullable', 'integer','min:1'],
            'grade_exclude' => ['nullable', 'integer','min:1'],
            'school_exclude' => ['nullable', 'integer','min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'course' => 'Curso',
            'school' => 'Escola',
            'grade_exclude' => 'Curso Excluído',
            'school_exclude' => 'Escola Excluída'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['data'=>[]]));
    }
}
