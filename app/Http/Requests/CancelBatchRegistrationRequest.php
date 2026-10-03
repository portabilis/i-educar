<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelBatchRegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'registrations' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'registrations.required' => 'Ao menos uma matrícula deve ser selecionada.',
            'registrations.array' => 'Deve ser informado uma lista de matrículas para cancelar.',
        ];
    }
}
