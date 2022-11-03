<?php
 
 namespace App\Http\Requests;
 
 use Illuminate\Foundation\Http\FormRequest;
 
 class ResponsavelExport extends FormRequest
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
          
             'cod_pessoa_fj' => 'nullable|numeric',
         ];
     }
 
     public function allWithTranslatedKeys()
     {
         $paramMap = [
            
             'cod_pessoa_fj' => 'id',
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
 