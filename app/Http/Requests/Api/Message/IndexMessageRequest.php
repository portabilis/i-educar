<?php

namespace App\Http\Requests\Api\Message;

use Illuminate\Foundation\Http\FormRequest;

class IndexMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'messageable_type' => 'required|string',
            'messageable_id' => 'required|integer|min:1',
        ];
    }

    public function attributes(): array
    {
        return [
            'messageable_type' => 'Tipo do modelo',
            'messageable_id' => 'ID do modelo',
        ];
    }
}
