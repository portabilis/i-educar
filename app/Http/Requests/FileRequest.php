<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => [
                'mimes:jpeg,pdf,png,xls,doc,jpg',
                'max:2000'
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'file.mimes' => 'Deve ser enviado um arquivo do tipo jpg, png, jpeg ou pdf.',
            'file.max' => 'Não são permitidos arquivos com mais de 2MB.',
        ];
    }
}
