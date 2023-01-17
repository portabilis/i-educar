<?php
use App\Models\Frequencia;
use App\Models\Pessoa;
use App\Models\ProfessorTurma;
use App\Models\ProfessorDisciplina;
use App\Models\SerieTurma;
use App\Models\Serie;
use App\Models\Turma;

class Portabilis_View_Helper_DynamicInput_ProfessorComponente extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_professor_componente';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $serieId = $this->getSerieId($options['serieId'] ?? null);
        $turmaId = $this->getTurmaId($options['turmaId'] ?? null);
        $ComponenteId =  $this->getComponenteCurricularId($options['componenteId'] ?? null);
        $anoLetivo = $this->getAno($options['ano'] ?? null);

        $userId = $this->getCurrentUserId();

      
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

                    $resources[$pessoas->idpes] = $pessoas->idpes.' - '.$pessoas->nome;
                  

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

    $ultimo_nome = 'Selecione um professor';
             
         

        return $this->insertOption(null,  $ultimo_nome, $resources);
    }

    protected function defaultOptions()
    {
        return [
            'id' => null,
            'turmaId' => null,
            'options' => [],
            'resources' => []
        ];
    }

    public function professorComponente($options = [])
    {
        parent::select($options);
    }

    private function agrupaComponentesCurriculares($componentesCurriculares)
    {
        $options = [];

        foreach ($componentesCurriculares as $componenteCurricular) {
            $areaConhecimento = (($componenteCurricular['secao_area_conhecimento'] != '') ? $componenteCurricular['secao_area_conhecimento'] . ' - ' : '') . $componenteCurricular['area_conhecimento'];
            $options[
                '__' . $componenteCurricular['id']
            ] = [
                'value' => mb_strtoupper($componenteCurricular['nome'], 'UTF-8'),
                'group' => mb_strtoupper($areaConhecimento, 'UTF-8')
            ];
        }

        return $options;
    }
}
