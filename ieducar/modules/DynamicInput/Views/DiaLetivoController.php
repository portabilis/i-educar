<?php
use App\Models\Frequencia;
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
            $serie_turma = SerieTurma::where('turma_id', $turmaId)->get(); 
            foreach($serie_turma as $serie){

                $serie_id = $serie->serie_id;
                
            }
          
          
            $total_dias_letivos_turma = 0;
            $dias_series = Serie::where('cod_serie', $serie_id)->get(); 
            foreach($dias_series as $dia){

                $total_dias_letivos_turma = $dia->dias_letivos;
                
            }
            $restante =  $total_dias_letivos_turma-$total_dias_letivos_realizados;
            
            
         
           
                $options[
                    '__' . 1
                ] = [
                    'value' => mb_strtoupper("Total: ".$total_dias_letivos_turma." | Realizados: ".$total_dias_letivos_realizados." | Restantes: ".$aula_restante, 'UTF-8'),
                    'checked' => "checked",
                    'group' => ''
                ];
         

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
