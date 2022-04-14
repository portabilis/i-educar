<?php

return new class extends clsDetalhe
{
    public $pessoa_logada;
    public $cod_turma;

    public function Gerar(){

        $this->cod_turma = $_GET['cod_turma'];

        if(is_numeric($this->cod_turma)){
            $matriculasTurma = new clsPmieducarMatriculaTurma();
            $matriculasTurma = $matriculasTurma->listaPorSequencial($this->cod_turma);
            
            if ($matriculasTurma) {
                foreach ($matriculasTurma as $campo => $val) {
                    $this->addDetalhe(
                        [
                            'Nome',
                            $val['nome']
                        ]
                    );
                    $matriculaTurmaId = $val['id'];
                }
                $retorno = 'Editar';
                
            }
            $this->largura = '100%';
        }
    
        $this->url_cancelar = "educar_turma_det.php?cod_turma={$this->cod_turma}";

        $this->breadcrumb('Lista de alunos na turma',[
        url('intranet/educar_index.php') => 'Escola',]);
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
     
     
    }
    public function Formular()
    {
        $this->title = 'Lista de alunos na turma';
        $this->processoAp = 586;
    }
};
