<?php

namespace App\Http\Requests;

use App\Models\LegacyStudent;
use Illuminate\Foundation\Http\FormRequest;

class PostLaudoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'aluno_id' => [
                'required',
                'integer',
                'exists:' . LegacyStudent::class . ',cod_aluno',
            ],
            'file' => [
                'mimes:jpeg,pdf,png,doc,jpg',
                'max:2000',
            ],
        ];
    }

    public function messages()
    {
        return [
            'file.mimes' => 'Deve ser enviado um arquivo do tipo jpg, png, jpeg ou pdf.',
            'file.max' => 'Não são permitidos arquivos com mais de 2MB.',
        ];
    }
}
