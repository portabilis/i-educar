<?php

return new class extends clsCadastro
{
    public $cod_matricula;

    public $ref_cod_aluno;

    public $matricula;

    public $modalidade_ensino;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 684);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Formular()
    {
        $this->title = 'Modalidade de ensino';
        $this->processoAp = 578;
    }

    public function Inicializar()
    {
        $this->cod_matricula = $_GET['ref_cod_matricula'];

        $this->validaPermissao();
        $this->validaParametros();

        return 'Editar';
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'cod_matricula', valor: $this->cod_matricula);

        $this->nome_url_cancelar = 'Voltar';
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        $this->breadcrumb(currentPage: 'Modalidade de ensino', breadcrumbs: [
            url(path: '/') => 'Início',
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: $this->matricula['nome']);

        $this->campoLista(
            nome: 'modalidade_ensino',
            campo: 'Modalidade de ensino:',
            valor: clsPmieducarMatricula::MODELOS_DE_ENSINO,
            default: (int) $this->matricula['modalidade_ensino'],
            obrigatorio: false
        );
    }

    public function Editar()
    {
        $this->validaPermissao();

        try {
            $matricula = (new clsPmieducarMatricula(cod_matricula: $this->cod_matricula));
            $matricula->modalidade_ensino = (int) $this->modalidade_ensino;
            $matricula->edita();
        } catch (Exception) {
            $this->mensagem = 'Erro ao atualizar a modalidade de ensino.';

            return false;
        }

        $this->mensagem = 'Modalidade de ensino atualizado com sucesso.';

        $this->matricula = $matricula->detalhe();

        return true;
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $matricula = (new clsPmieducarMatricula(cod_matricula: $this->cod_matricula))->detalhe();

        if (empty($matricula)) {
            $this->simpleRedirect(url: "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
        }

        $this->matricula = $matricula;
    }
};
