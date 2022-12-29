<?php
use App\Models\Frequencia;
use App\Models\ComponenteCurricularTurma;
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
            $componentes = ComponenteCurricularTurma::where('componente_curricular_id', $ComponenteId)->where('turma_id', $turmaId)->get(); 
            foreach($componentes as $componente){
                $carga_horaria = $componente->carga_horaria;
                $carga_horaria = round($carga_horaria, 3);
            } 
            $aula_restante = $carga_horaria-$total;
           
                $options[
                    '__' . 1
                ] = [
                    'value' => "Aulas ministradas: ".$carga_horaria." - Aulas restantes: ".$aula_restante ,
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
