<?php
use App\Models\Frequencia;
use App\Models\ComponenteCurricularTurma;
use App\Models\ComponenteCurricularAno;
use App\Models\SerieTurma;
use App\Models\Serie;
use App\Models\Turma;
use App\Services\SchoolGradeDisciplineService;

class DiaLetivoController extends ApiCoreController
{
   
    protected function getDiaLetivo()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $instituicaoId = $this->getRequest()->instituicao_id || 1;
        $turmaId = $this->getRequest()->turma_id;
        $ComponenteId = $this->getRequest()->componente_id;
        $ano = $this->getRequest()->ano;

            $options = [];
          
           
           
           
        
            $total_dias_letivos_realizados = 0;
            $frequencias = Frequencia::where('ref_cod_turma', $turmaId)->get(); 
            $total_aulas = '';
            foreach($frequencias as $aulas){
                $total_dias_letivos_realizados++;
                
            }

           
            $serie_id = 0;
            $serie_turma = SerieTurma::where('cod_turma', $turmaId)->get(); 
            foreach($serie_turma as $serie){

                $serie_id = $serie->ref_ref_cod_serie;
              
                
            }
            $etapa_curso_serie = 0;
            $total_dias_letivos_turma = 0;
            $dias_series = Serie::where('cod_serie', $serie_id)->get(); 
            foreach($dias_series as $dia){

                $total_dias_letivos_turma = $dia->dias_letivos;
                $etapa_curso_serie = $dia->etapa_curso;
                
            }
            $restante =  $total_dias_letivos_turma-$total_dias_letivos_realizados;
            
            if($etapa_curso_serie<6){
         
           
                $options[
                    '__' . 1
                ] = [
                    'value' => mb_strtoupper("dias letivos: ".$total_dias_letivos_turma." | dias realizados: ".$total_dias_letivos_realizados." | dias a realizar: ".$restante, 'UTF-8'),
                    'checked' => "checked",
                    'group' => ''
                ];

            }else{

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
                            'value' => mb_strtoupper("ch : ".$carga_horaria." | aulas realizadas: ".$total." | aulas a realizar: ".$aula_restante, 'UTF-8'),
                            'checked' => "checked",
                            'group' => ''
                        ];

            }

            return ['options' => $options];
        
    }


    public function Gerar()
    {
        if ($this->isRequestFor('get', 'diasLetivos')) {

            $this->appendResponse($this->getDiaLetivo());

        } 
         else {
            
        }
    }
}
