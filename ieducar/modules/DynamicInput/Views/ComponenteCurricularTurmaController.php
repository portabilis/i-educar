<?php
use App\Models\ComponenteCurricularTurma;
use App\Services\SchoolGradeDisciplineService;

class ComponenteCurricularTurmaController extends ApiCoreController
{
   
    protected function getComponentesCurricularesTurma()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $instituicaoId = $this->getRequest()->instituicao_id || 1;
        $turmaId = $this->getRequest()->turma_id;
        $ComponenteId = $this->getRequest()->componente_id;
        $ano = $this->getRequest()->ano;

            $options = [];
          
           
           
           

           // foreach ($componentesCurriculares as $componenteCurricular) {
            $carga_horaria = 0;
            $componentes = ComponenteCurricularTurma::where('componente_curricular_id', $ComponenteId)->where('turma_id', $turmaId)->get(); 
            foreach($componentes as $componente){
                $carga_horaria = $componente->carga_horaria;
                $carga_horaria = round($carga_horaria, 3);
            }
                $options[
                    '__' . 1
                ] = [
                    'value' => mb_strtoupper($carga_horaria.'h', 'UTF-8'),
                    'checked' => "checked",
                    'group' => ''
                ];
           // }

            return ['options' => $options];
        
    }


    public function Gerar()
    {
        if ($this->isRequestFor('get', 'componentesCurriculares')) {
        }elseif ($this->isRequestFor('get', 'componentesCurricularesTurma')) {

            $this->appendResponse($this->getComponentesCurricularesTurma());

        } elseif ($this->isRequestFor('get', 'componentesCurricularesForDiario')) {
          
        } elseif ($this->isRequestFor('get', 'componentesCurricularesEscolaSerie')) {

        } else {
            
        }
    }
}
