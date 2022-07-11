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
            'course' => ['required_without:school','nullable', 'integer'],
            'school' => ['nullable', 'integer'],
            'grade_exclude' => ['nullable', 'integer'],
            'school_exclude' => ['nullable', 'integer'],
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

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([]));
    }
}
