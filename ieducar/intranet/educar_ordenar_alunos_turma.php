<?php

use App\Models\LegacySchoolClass;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_turma;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma = $_GET['cod_turma'];

        if (is_numeric(value: $this->cod_turma)) {

            $schoolclass = LegacySchoolClass::find(($this->cod_turma));

            if ($schoolclass) {
                $this->addHtml(view(
                    'sequence.schoolclass', ['schoolclass' => $schoolclass]
                )->render());

                $this->campoQuebra();
            }

            $matriculasTurma = new clsPmieducarMatriculaTurma();
            $matriculasTurma = $matriculasTurma->listaPorSequencial(codTurma: $this->cod_turma);

            if ($matriculasTurma) {
                foreach ($matriculasTurma as $val) {
                    $this->campoTexto(nome: 'nome_aluno_' . $val['id'], campo: '', valor: $val['nome'], tamanhovisivel: 60, tamanhomaximo: false, duplo: true, disabled: true);
                    $this->campoTexto(nome: 'situacao_' . $val['id'], campo: '', valor: $val['situacao'], tamanhovisivel: 20, tamanhomaximo: false, duplo: true, disabled: true);
                    $matriculaTurmaId = $val['id'];
                    $this->campoTexto(nome: "sequencia[$matriculaTurmaId]", campo: '', valor: ($val['sequencial_fechamento']), tamanhovisivel: 5);
                }
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = "educar_turma_det.php?cod_turma={$this->cod_turma}";

        $this->breadcrumb(currentPage: 'Sequência manual dos alunos na turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
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
                sql: 'UPDATE pmieducar.matricula_turma
                       SET sequencial_fechamento = $1
                     WHERE id = $2',
                options: ['params' => [$sequencial, $matriculaTurmaId]]
            );
        }

        $this->simpleRedirect(url: "educar_turma_det.php?cod_turma={$_GET['cod_turma']}");
    }

    public function Excluir()
    {
        return false;
    }

    public function Formular()
    {
        $this->title = 'Sequência de alunos na turma';
        $this->processoAp = '586';
    }
};
