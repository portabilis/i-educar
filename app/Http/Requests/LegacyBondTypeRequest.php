<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AbbreviationLength;

class LegacyBondTypeRequest extends FormRequest
{
    // possibilidade de restringir o acesso aqui, ou retornar true para liberar
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nm_vinculo' => ['required', 'string', 'max:255'],
            'abreviatura' => ['required', 'string', 'max:255', new AbbreviationLength($this->input('nm_vinculo'))],
        ];
    }

    // Opcional: mensagens personalizadas para os erros de validação
    public function messages()
    {
        return [
            'nm_vinculo.required' => 'O nome da função é obrigatório.',
            'abreviatura.required' => 'A abreviatura é obrigatória.',
            // A mensagem da regra personalizada já está na Rule
        ];
    }
}
