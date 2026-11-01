<?php

namespace App\Http\Requests;

use App\Models\ComponentBatchOperation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostComponentBatchCallbackRequest extends FormRequest
{
    public function rules()
    {
        return [
            'operation_id' => [
                'required',
                'integer',
                Rule::exists(ComponentBatchOperation::class, 'id'),
            ],
            'deleted' => [
                'nullable',
                'integer',
            ],
            'error' => [
                'nullable',
            ],
        ];
    }

    public function messages()
    {
        return [
            'operation_id.required' => 'Parâmetro operation_id é obrigatório.',
            'operation_id.exists' => 'Operação não encontrada.',
        ];
    }
}
