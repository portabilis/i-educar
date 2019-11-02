<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'enrollment_date' => [
                'required',
                'date_format:d/m/Y',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'enrollment_date.required' => 'A data de enturmação é obrigatória.',
            'enrollment_date.date_format' => 'A data de enturmação deve ser uma data válida.',
        ];
    }
}
