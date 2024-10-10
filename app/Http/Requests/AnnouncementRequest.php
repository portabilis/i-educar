<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'repeat_on_login' => $this->has('repeat_on_login'),
            'show_confirmation' => $this->has('show_confirmation'),
            'show_vacancy' => $this->has('show_vacancy'),
            'active' => $this->has('active'),
            'tipo_usuario' => Arr::flatten($this->get('tipo_usuario', [])),
            'created_by_user_id' => $this->user()->getKey(),
        ]);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:255'],
            'description' => ['required'],
            'repeat_on_login' => ['boolean'],
            'show_confirmation' => ['boolean'],
            'active' => ['boolean', function ($attribute, $value, $fail) {
                if ($value) {
                    $exists = Announcement::query()
                        ->withoutTrashed()
                        ->where('id', '<>', $this->route('announcement'))
                        ->exists();

                    if ($exists) {
                        $fail('Já existe um aviso ativo.');
                    }
                }
            }],
            'show_vacancy' => ['boolean'],
            'tipo_usuario' => ['required', 'array'],
            'tipo_usuario.*' => ['integer', Rule::exists('tipo_usuario', 'cod_tipo_usuario')],
        ];
    }

    public function attributes()
    {
        return [
            'description' => 'Conteúdo do aviso',
            'tipo_usuario' => 'Tipos de usuários que serão notificados',
        ];
    }
}
