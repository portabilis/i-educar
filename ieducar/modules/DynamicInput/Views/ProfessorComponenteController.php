<?php
use App\Models\Frequencia;
use App\Models\Pessoa;
use App\Models\ProfessorTurma;
use App\Models\ProfessorDisciplina;
use App\Models\SerieTurma;
use App\Models\Serie;
use App\Models\Turma;
use App\Services\SchoolGradeDisciplineService;

class ProfessorComponenteController extends ApiCoreController
{
    
    protected function getProfessorComponente()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $instituicaoId = $this->getRequest()->instituicao_id || 1;
        $turmaId = $this->getRequest()->turma_id;
        $ComponenteId = $this->getRequest()->componente_id;
        $ano = $this->getRequest()->ano;
        
        
            $options = [];
       
            $pessoa_id = 0;
            if(empty($ComponenteId) and !empty($turmaId)){

            $professor_turma = ProfessorTurma::where('turma_id', $turmaId)->get(); 
            foreach($professor_turma as $professores_turma){

            
            $pessoa = Pessoa::where('idpes', $professores_turma->servidor_id)->get(); 
            foreach($pessoa as $pessoas){

                $options[
                    '__' . $pessoas->idpes
                ] = [
                    'value' => mb_strtoupper($pessoas->idpes." - ".$pessoas->nome, 'UTF-8'),
                    'checked' => "checked",
                    'group' => ''
                ];

            }
                
            }

        } elseif(!empty($ComponenteId) and !empty($turmaId)){

            $professor_turma = ProfessorTurma::where('turma_id', $turmaId)->get(); 
            foreach($professor_turma as $professores_turma){

                $professor_disciplina = ProfessorDisciplina::where('ref_cod_disciplina',$ComponenteId)->where('ref_cod_servidor',$professores_turma->servidor_id)->get(); 
                foreach($professor_disciplina as $professores_disciplina){

                
                    $pessoa = Pessoa::where('idpes', $professores_disciplina->ref_cod_servidor)->get(); 
                    foreach($pessoa as $pessoas){

                        $options[
                            '__' . $pessoas->idpes
                        ] = [
                            'value' => mb_strtoupper($pessoas->idpes." - ".$pessoas->nome, 'UTF-8'),
                            'checked' => "checked",
                            'group' => ''
                        ];

                    }
                    
                }
            }

           

        }
        else{

            $professor_turma = ProfessorTurma::where('turma_id', $turmaId)->get(); 
            foreach($professor_turma as $professores_turma){

            
            $pessoa = Pessoa::where('idpes', $professores_turma->servidor_id)->get(); 
            foreach($pessoa as $pessoas){

                $options[
                    '__' . $pessoas->idpes
                ] = [
                    'value' => mb_strtoupper($pessoas->idpes." - ".$pessoas->nome, 'UTF-8'),
                    'checked' => "checked",
                    'group' => ''
                ];

            }
                
            }
            

        }

        return ['options' => $options];
        
    }


    public function Gerar()
    {
        if ($this->isRequestFor('get', 'professoresComponente')) {

            $this->appendResponse($this->getProfessorComponente());

        } 
         else {
            
        }
    }
}
