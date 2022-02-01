<?php

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_turma;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma=$_GET['cod_turma'];

        if (is_numeric($this->cod_turma)) {
            $matriculasTurma = new clsPmieducarMatriculaTurma();
            $matriculasTurma = $matriculasTurma->listaPorSequencial($this->cod_turma);

            if ($matriculasTurma) {
                foreach ($matriculasTurma as $campo => $val) {
                    $this->campoTexto('nome_aluno_' . $val['id'], '', $val['nome'], 60, false, false, false, true, '', '', '', '', true);
                    $this->campoTexto('situacao_' . $val['id'], '', $val['situacao'], 20, false, false, false, true, '', '', '', '', true);
                    $matriculaTurmaId = $val['id'];
                    $this->campoTexto("sequencia[$matriculaTurmaId]", '', ($val['sequencial_fechamento']), 5, null, false, false, false);
                }
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = "educar_turma_det.php?cod_turma={$this->cod_turma}";

        $this->breadcrumb('Sequência manual dos alunos na turma', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        return true;
    }

    public function Novo()
    {
        return false;
    }

    public function Editar()
    {

        foreach ($this->sequencia as $matriculaTurmaId => $sequencial) {
            Portabilis_Utils_Database::fetchPreparedQuery(
                'UPDATE pmieducar.matricula_turma
                       SET sequencial_fechamento = $1
                     WHERE id = $2',
                ['params' => [$sequencial, $matriculaTurmaId]]
            );
        }

        $this->simpleRedirect("educar_turma_det.php?cod_turma={$_GET['cod_turma']}");
    }

    public function Excluir()
    {
        return false;
    }

    public function Formular()
    {
        $this->title = 'Série';
        $this->processoAp = '586';
    }
};
