<?php
use App\Models\Frequencia;
use App\Models\ComponenteCurricularTurma;
use App\Models\ComponenteCurricularAno;
use App\Services\SchoolGradeDisciplineService;

class FrequenciaComponenteController extends ApiCoreController
{
   
    protected function getFrequenciaComponente()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $instituicaoId = $this->getRequest()->instituicao_id || 1;
        $turmaId = $this->getRequest()->turma_id;
        $ComponenteId = $this->getRequest()->componente_id;
        $ano = $this->getRequest()->ano;

            $options = [];
          
           if(empty($ComponenteId)){
            echo "
            <script>
          
                $('#tr_ref_cod_frequencia_componente').hide();
                $('#tr_ref_cod_dia_letivo').show();
        

            </script>";
           }else{

            echo "
            <script>
        
              
                    $('#tr_ref_cod_frequencia_componente').show();
                    $('#tr_ref_cod_dia_letivo').hide();

            </script>";

           }
           
           

            $carga_horaria = 0;
            $frequencias = Frequencia::where('ref_componente_curricular', $ComponenteId)->where('ref_cod_turma', $turmaId)->get(); 
            $total_aulas = '';
            foreach($frequencias as $aulas){
                $total_aulas .= $aulas->ordens_aulas.",";
                
            }
            $str_arr = preg_split ("/\,/", $total_aulas);
            $total = count($str_arr);

             // foreach ($componentesCurriculares as $componenteCurricular) {
            $carga_horaria = 0;
            $componentes = ComponenteCurricularAno::where('componente_curricular_id', $ComponenteId)->get(); 
            foreach($componentes as $componente){
                $carga_horaria = $componente->carga_horaria;
                $carga_horaria = round($carga_horaria, 3);
            } 
            $aula_restante = $carga_horaria-$total;
            if($carga_horaria ==0){
                $aula_restante = " - ";
            }
           
                $options[
                    '__' . 1
                ] = [
                    'value' => mb_strtoupper("CH: ".$carga_horaria." | Aulas ministradas: ".$total." | Aulas restantes: ".$aula_restante, 'UTF-8'),
                    'checked' => "checked",
                    'group' => ''
                ];
         

            return ['options' => $options];
        
    }


    public function Gerar()
    {
        if ($this->isRequestFor('get', 'frequenciasComponente')) {

            $this->appendResponse($this->getFrequenciaComponente());

        } 
         else {
            
        }
    }
}
