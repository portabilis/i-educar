<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelBatchEnrollmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date_format:d/m/Y',
            'enrollments' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'A data de saída é obrigatória.',
            'date.date_format' => 'A data de saída deve ser uma data válida.',
            'enrollments.required' => 'Ao menos uma matrícula deve ser selecionada.',
            'enrollments.array' => 'Deve ser informado uma lista de matrículas para desenturmar.',
        ];
    }
}
