<?php

namespace App\Http\Requests\Api\Resource\Discipline;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResourceDisciplineRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course' => ['nullable','integer','min:1'],
            'grade' => ['required_without_all:course,school','required_with:school','nullable','integer','min:1'],
            'school'=> ['nullable','integer','min:1']
        ];
    }

    public function attributes()
    {
        return [
            'grade' => 'Série',
            'course' => 'Curso',
            'school' => 'Escola'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['data'=>[]]));
    }
}
