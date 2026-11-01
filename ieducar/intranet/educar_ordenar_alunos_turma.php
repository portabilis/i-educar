<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacySchoolClass;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_turma;

    public $sequencia;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma = $_GET['cod_turma'];

        if (is_numeric(value: $this->cod_turma)) {
            $matriculasTurma = $this->matriculasDaTurma();

            if ($matriculasTurma->isNotEmpty()) {
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = "educar_turma_det.php?cod_turma={$this->cod_turma}";

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $this->cod_turma = $this->cod_turma ?: (request('cod_turma') ?? null);

        if (!is_numeric(value: $this->cod_turma)) {
            return true;
        }

        $this->campoOculto(nome: 'cod_turma', valor: $this->cod_turma);

        $schoolclass = LegacySchoolClass::find($this->cod_turma);

        // Início > Escola > Turmas > Turma (ano) > Sequência dos alunos
        $this->breadcrumb(currentPage: 'Sequência dos alunos', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
            url(path: 'intranet/educar_turma_lst.php') => 'Turmas',
            url(path: "intranet/educar_turma_det.php?cod_turma={$this->cod_turma}") => $schoolclass?->name,
        ]);

        if ($schoolclass) {
            $this->addHtml(view(
                'sequence.schoolclass', ['schoolclass' => $schoolclass]
            )->render());

            $this->campoQuebra();
        }

        $matriculasTurma = $this->matriculasDaTurma();

        if ($matriculasTurma->isNotEmpty()) {
            foreach ($matriculasTurma as $val) {
                $sequencia = $this->sequencia[$val['id']] ?? $val['sequencial_fechamento'];

                $this->campoTexto(nome: 'nome_aluno_' . $val['id'], campo: '', valor: $val['nome'], tamanhovisivel: 60, tamanhomaximo: false, duplo: true, disabled: true);
                $this->campoTexto(nome: 'situacao_' . $val['id'], campo: '', valor: $val['situacao'], tamanhovisivel: 20, tamanhomaximo: false, duplo: true, disabled: true);
                $this->campoTexto(nome: "sequencia[{$val['id']}]", campo: '', valor: $sequencia, tamanhovisivel: 5);
            }
        }

        return true;
    }

    public function Novo()
    {
        return false;
    }

    public function Editar()
    {
        foreach ($this->sequencia as $sequencial) {
            if (!is_numeric($sequencial)) {
                $this->mensagem = 'Edição não realizada. É necessário preencher a sequência de todos os alunos.';

                return false;
            }
        }

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

    private function matriculasDaTurma()
    {
        return LegacyEnrollment::query()
            ->join('pmieducar.matricula', 'matricula.cod_matricula', 'matricula_turma.ref_cod_matricula')
            ->join('pmieducar.aluno', 'aluno.cod_aluno', 'matricula.ref_cod_aluno')
            ->join('cadastro.pessoa', 'pessoa.idpes', 'aluno.ref_idpes')
            ->join('pmieducar.escola', 'escola.cod_escola', 'matricula.ref_ref_cod_escola')
            ->join('pmieducar.instituicao', 'instituicao.cod_instituicao', 'escola.ref_cod_instituicao')
            ->join('relatorio.view_situacao_relatorios', function ($j) {
                $j->on('view_situacao_relatorios.cod_matricula', 'matricula.cod_matricula');
                $j->on('view_situacao_relatorios.cod_turma', 'matricula_turma.ref_cod_turma');
                $j->on('view_situacao_relatorios.sequencial', 'matricula_turma.sequencial');
            })
            ->whereSchoolClass($this->cod_turma)
            ->groupBy(
                'matricula_turma.ref_cod_matricula',
                'matricula_turma.sequencial_fechamento',
                'pessoa.nome',
                'view_situacao_relatorios.texto_situacao',
                'matricula_turma.id',
            )
            ->orderBy('matricula_turma.sequencial_fechamento')
            ->orderBy('pessoa.nome')
            ->get([
                'matricula_turma.id',
                'matricula_turma.ref_cod_matricula',
                'matricula_turma.sequencial_fechamento',
                'pessoa.nome',
                'view_situacao_relatorios.texto_situacao as situacao',
            ]);
    }
};
