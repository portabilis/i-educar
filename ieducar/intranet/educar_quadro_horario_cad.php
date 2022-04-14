<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $ref_cod_turma;
    public $ref_cod_serie;
    public $ref_cod_curso;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $cod_quadro_horario;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastra;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_turma       = $_GET['ref_cod_turma'];
        $this->ref_cod_serie       = $_GET['ref_cod_serie'];
        $this->ref_cod_curso       = $_GET['ref_cod_curso'];
        $this->ref_cod_escola      = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $this->cod_quadro_horario  = $_GET['ref_cod_quadro_horario'];
        $this->ano                 = $_GET['ano'];

        if (is_numeric($this->cod_quadro_horario)) {
            $obj_quadro_horario = new clsPmieducarQuadroHorario($this->cod_quadro_horario);
            $det_quadro_horario = $obj_quadro_horario->detalhe();
            if ($det_quadro_horario) {
                // Passa todos os valores obtidos no registro para atributos do objeto
                foreach ($det_quadro_horario as $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}"
        );

        $this->url_cancelar = $retorno == 'Editar' ?
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}" :
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}";

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' quadro de horários', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($this->retorno == 'Editar') {
            $this->Excluir();
        }

        // primary keys
        $this->campoOculto('cod_quadro_horario', $this->cod_quadro_horario);

        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola', 'curso', 'serie', 'turma']);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}"
        );

        $obj = new clsPmieducarQuadroHorario();
        $lista = $obj->lista(null, null, $this->pessoa_logada, $this->ref_cod_turma, null, null, null, null, 1, $this->ano);
        if ($lista) {
            echo '<script>alert(\'Quadro de Horário já cadastrado para esta turma\');</script>';

            return false;
        }

        $obj = new clsPmieducarQuadroHorario(
            null,
            null,
            $this->pessoa_logada,
            $this->ref_cod_turma,
            null,
            null,
            1,
            $this->ano
        );

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect("educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}&busca=S");
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            641,
            $this->pessoa_logada,
            7,
            "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}"
        );

        if (is_numeric($this->cod_quadro_horario)) {
            $obj_horarios = new clsPmieducarQuadroHorarioHorarios(
                $this->cod_quadro_horario,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1
            );

            if ($obj_horarios->excluirTodos()) {
                $obj_quadro = new clsPmieducarQuadroHorario(
                    $this->cod_quadro_horario,
                    $this->pessoa_logada
                );

                if ($obj_quadro->excluir()) {
                    $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
                    $this->simpleRedirect("educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}");
                }
            }
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-quadro-horario-horarios-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Quadro de Hor&aacute;rios';
        $this->processoAp = '641';
    }
};
