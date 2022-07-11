<?php

namespace App\Http\Requests\Api\Resource\School;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResourceSchoolRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'institution' => ['required','integer']
        ];
    }

    public function attributes()
    {
        return [
            'institution' => 'Instituição'
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
