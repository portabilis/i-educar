<?php
 
 namespace App\Http\Requests;
 
 use Illuminate\Foundation\Http\FormRequest;
 
 class ResponsavelTurmaExport extends FormRequest
 {
     /**
      * Determine if the user is authorized to make this request.
      *
      * @return bool
      */
     public function authorize()
     {
         return (bool) session('id_pessoa');
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
            'ref_cod_turma' => 'nullable|numeric',

         ];
     }
 
     public function allWithTranslatedKeys()
     {
         $paramMap = [
            
            'cod_aluno' => 'id',
            'cod_inep' => 'inep_code',
            'aluno_estado_id' => 'registry_code',
            'nome_aluno' => 'student_name',
            'data_nascimento' => 'birthdate',
            'nome_pai' => 'father_name',
            'nome_mae' => 'mother_name',
            'nome_responsavel' => 'guardian_name',
            'ano' => 'year',
            'ref_cod_escola' => 'school_id',
            'ref_cod_curso' => 'course_id',
            'ref_cod_serie' => 'level_id',
            'ref_cod_turma' => 'school_class_id',
         ];
 
         $params = $this->all();
         $newParams = [];
 
         foreach ($params as $k => $v) {
             if (!in_array($k, array_keys($paramMap))) {
                 continue;
             }
 
             $newParams[$paramMap[$k]] = $v;
         }
 
         return $newParams;
     }
 }
 