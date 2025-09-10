<?php

namespace App\Http\Requests\Api\Message;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:900',
        ];
    }

    public function attributes(): array
    {
        return [
            'description' => 'Descrição',
        ];
    }
}
