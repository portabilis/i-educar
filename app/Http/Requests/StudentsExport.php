<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentsExport extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (int) session('id_pessoa') !== 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'busca' => 'nullable',
            'filtros_matricula' => 'nullable',
            'cod_aluno' => 'nullable|numeric',
            'cod_inep' => 'nullable|numeric',
            'aluno_estado_id' => 'nullable|regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{1}$/',
            'nome_aluno' => 'nullable',
            'data_nascimento' => 'nullable|date',
            'nome_pai' => 'nullable',
            'nome_mae' => 'nullable',
            'nome_responsavel' => 'nullable',
            'idsetorbai' => 'nullable|numeric',
            'ano' => 'nullable|regex:/^[0-9]{4}$/',
            'ref_cod_instituicao' => 'nullable|numeric',
            'ref_cod_escola' => 'nullable|numeric',
            'ref_cod_curso' => 'nullable|numeric',
            'ref_cod_serie' => 'nullable|numeric',
        ];
    }
}
