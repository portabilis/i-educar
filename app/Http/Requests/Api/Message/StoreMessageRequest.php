<?php

namespace App\Http\Requests\Api\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'messageable_type' => 'required|string',
            'messageable_id' => 'required|integer',
            'description' => 'required|string|max:900',
        ];
    }

    public function attributes(): array
    {
        return [
            'messageable_type' => 'Tipo do modelo',
            'messageable_id' => 'ID do modelo',
            'description' => 'Descrição',
        ];
    }
}
