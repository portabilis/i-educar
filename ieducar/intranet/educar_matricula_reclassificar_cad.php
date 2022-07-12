<?php

use App\Process;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_matricula;
    public $ref_cod_reserva_vaga;
    public $ref_ref_cod_escola;
    public $ref_ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_aluno;
    public $aprovado;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ano;
    public $data_cancel;

    public $ref_cod_instituicao;
    public $ref_cod_curso;
    public $ref_cod_escola;
    public $modalidade_ensino;

    public $ref_ref_cod_serie_antiga;

    public $descricao_reclassificacao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_matricula=$_GET['ref_cod_matricula'];
        $this->ref_cod_aluno=$_GET['ref_cod_aluno'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(Process::RECLASSIFY_REGISTRATION, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula || $det_matricula['aprovado'] != 3) {
            $this->simpleRedirect("educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        foreach ($det_matricula as $key => $value) {
            $this->$key = $value;
        }

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        $this->breadcrumb('Registro da reclassificação da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        // primary keys
        $this->campoOculto('cod_matricula', $this->cod_matricula);
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
        $this->campoOculto('ref_cod_escola', $this->ref_ref_cod_escola);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoRotulo('nm_aluno', 'Aluno', $this->nm_aluno);
        }

        $cursos = [];

        $escolaAluno = $this->ref_ref_cod_escola;

        $objEscolaCurso = new clsPmieducarEscolaCurso();

        $listaEscolaCurso = $objEscolaCurso->lista($escolaAluno);

        if ($listaEscolaCurso) {
            foreach ($listaEscolaCurso as $escolaCurso) {
                $objCurso = new clsPmieducarCurso($escolaCurso['ref_cod_curso']);
                $detCurso = $objCurso->detalhe();
                $nomeCurso = $detCurso['nm_curso'];
                $cursos[$escolaCurso['ref_cod_curso']] = $nomeCurso;
            }
        }

        $this->campoOculto('serie_matricula', $this->ref_ref_cod_serie);
        $this->campoLista('ref_cod_curso', 'Curso', $cursos, $this->ref_cod_curso, 'getSerie();');
        $this->campoLista('ref_ref_cod_serie', 'Série', ['' => 'Selecione uma série'], '');
        $this->inputsHelper()->date('data_cancel', ['label' => 'Data da reclassificação', 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y')]);
        $this->campoMemo('descricao_reclassificacao', 'Descrição', $this->descricao_reclassificacao, 100, 10, true);

        $this->acao_enviar = 'if(confirm("Deseja reclassificar está matrícula?"))acao();';
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(Process::RECLASSIFY_REGISTRATION, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");

        $this->data_cancel = Portabilis_Date_Utils::brToPgSQL($this->data_cancel);

        if ($this->ref_ref_cod_serie == $this->ref_ref_cod_serie_antiga) {
            $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->cod_matricula}");
        }

        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if (is_null($det_matricula['data_matricula'])) {
            if (substr($det_matricula['data_cadastro'], 0, 10) > $this->data_cancel) {
                $this->mensagem = 'Data de abandono não pode ser inferior a data da matrícula.<br>';

                return false;
            }
        } elseif (substr($det_matricula['data_matricula'], 0, 10) > $this->data_cancel) {
            $this->mensagem = 'Data de abandono não pode ser inferior a data da matrícula.<br>';

            return false;
        }

        if (!$det_matricula || $det_matricula['aprovado'] != 3) {
            $this->simpleRedirect("educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $obj_matricula = new clsPmieducarMatricula(
            $this->cod_matricula,
            null,
            null,
            null,
            $this->pessoa_logada,
            null,
            null,
            5,
            null,
            null,
            1,
            null,
            0,
            null,
            null,
            $this->descricao_reclassificacao,
            $det_matricula['modalidade_ensino']
        );

        $obj_matricula->data_cancel = $this->data_cancel;
        if (!$obj_matricula->edita()) {
            echo "<script>alert('Erro ao reclassificar matrícula'); window.location='educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';</script>";
            die('Erro ao reclassificar matrícula');
        }
        $obj_serie = new clsPmieducarSerie($this->ref_ref_cod_serie);
        $det_serie = $obj_serie->detalhe();

        $obj_matricula = new clsPmieducarMatricula(
            null,
            null,
            $this->ref_cod_escola,
            $this->ref_ref_cod_serie,
            null,
            $this->pessoa_logada,
            $this->ref_cod_aluno,
            3,
            null,
            null,
            1,
            $det_matricula['ano'],
            1,
            null,
            null,
            null,
            1,
            $det_serie['ref_cod_curso'],
            null,
            null,
            null,
            null,
            null,
            null,
            $det_matricula['modalidade_ensino']
        );

        $obj_matricula->data_matricula = $this->data_cancel;
        $cadastrou = $obj_matricula->cadastra();

        if (!$cadastrou) {
            echo "<script>alert('Erro ao reclassificar matrícula'); window.location='educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';</script>";
            die('Erro ao reclassificar matrícula');
        }

        /**
         * desativa todas as enturmacoes da matricula anterior
         */
        $obj_matricula_turma = new clsPmieducarMatriculaTurma($this->cod_matricula);
        if (!$obj_matricula_turma->reclassificacao($this->data_cancel)) {
            echo "<script>alert('Erro ao desativar enturmações da matrícula: {$this->cod_matricula}\nContate o administrador do sistema informando a matrícula!');</script>";
        }

        $notaAluno = (new Avaliacao_Model_NotaAlunoDataMapper())
            ->findAll(['id'], ['matricula_id' => $this->cod_matricula])[0];

        if (!is_null($notaAluno)) {
            $notaAlunoId = $notaAluno->get('id');
            (new Avaliacao_Model_NotaComponenteMediaDataMapper())
                ->updateSituation($notaAlunoId, App_Model_MatriculaSituacao::RECLASSIFICADO);
        }

        echo "<script>alert('Reclassificação realizada com sucesso!\\nO Código da nova matrícula é: $cadastrou.');
        window.location='educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}';
        </script>";
        die('Reclassificação realizada com sucesso!');
    }

    public function Excluir()
    {
        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/matricula-reclassificar-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Reclassificar Matrícula';
        $this->processoAp = Process::RECLASSIFY_REGISTRATION;
    }
};
