<?php

use App\Models\LegacyProject;
use App\Models\LegacyStudentProject;

return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_projeto;

    public $nome;

    public $observacao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_projeto = $_GET['cod_projeto'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 21250, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_projeto_lst.php');

        if (is_numeric($this->cod_projeto)) {
            $registro = LegacyProject::find($this->cod_projeto)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 21250, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
                //**

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_projeto_det.php?cod_projeto={$registro['cod_projeto']}" : 'educar_projeto_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' projeto', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_projeto', valor: $this->cod_projeto);

        $this->campoTexto(nome: 'nome', campo: 'Nome do projeto', valor: $this->nome, tamanhovisivel: 50, tamanhomaximo: 50, obrigatorio: true);
        $this->campoMemo(nome: 'observacao', campo: 'Observação', valor: $this->observacao, colunas: 52, linhas: 5);
    }

    public function Novo()
    {
        $project = new LegacyProject();
        $project->nome = $this->nome;
        $project->observacao = $this->observacao;

        if ($project->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }
        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $project = LegacyProject::find($this->cod_projeto);
        $project->nome = $this->nome;
        $project->observacao = $this->observacao;

        if ($project->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $count = LegacyStudentProject::query()
            ->where(column: 'ref_cod_projeto', operator: $this->cod_projeto)
            ->count();

        if ($count > 0) {
            $this->mensagem = 'Você não pode excluir esse projeto, pois ele possui alunos vinculados.<br>';

            return false;
        }

        $project = LegacyProject::find($this->cod_projeto);

        if ($project->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_projeto_lst.php');
        }
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Projeto';
        $this->processoAp = '21250';
    }
};
