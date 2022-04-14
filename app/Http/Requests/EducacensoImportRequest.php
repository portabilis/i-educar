<?php

namespace App\Http\Requests;

use App\Rules\EducacensoImportRegistrationDate;
use Illuminate\Foundation\Http\FormRequest;

class EducacensoImportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data_entrada_matricula' => [
                'required',
                new EducacensoImportRegistrationDate($this->get('ano')),
                'date_format:d/m/Y'
            ],
            'arquivo' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'data_entrada_matricula.required' => 'O campo: Data de entrada das matrículas é obrigatório.',
        ];
    }
}
