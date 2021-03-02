<?php


return new class extends clsCadastro
{
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
                    $this->campoTexto('nome_aluno_' . $val['ref_cod_matricula'], '', $val['nome'], 60, false, false, false, true, '', '', '', '', true);
                    $matricula = $val['ref_cod_matricula'];
                    $this->campoTexto("sequencia[$matricula]", '', ($val['sequencial_fechamento']), 5, null, false, false, false);
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
        $cod_turma = $_GET['cod_turma'];
        foreach ($this->sequencia as $matricula => $sequencial) {
            $retorno = Portabilis_Utils_Database::fetchPreparedQuery(
                'UPDATE pmieducar.matricula_turma
                                                                   SET sequencial_fechamento = $1
                                                                 WHERE ref_cod_matricula = $2
                                                                   AND ref_cod_turma = $3',
                ['params' => [$sequencial, $matricula, $cod_turma]]
            );
        }
        $this->simpleRedirect("educar_turma_det.php?cod_turma={$cod_turma}");
    }

    public function Excluir()
    {
        return false;

    }

    public function Formular()
    {
        $this->title = "i-Educar - S&eacute;rie";
        $this->processoAp = '586';
    }
};

