<?php

use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $idesco;

    public $descricao;

    public $escolaridade;

    public $findUsage;

    protected function loadAssets()
    {
        $jsFile = '/vendor/legacy/Cadastro/Assets/Javascripts/ModalExclusaoEscolaridade.js';
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $jsFile);
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->idesco = $_GET['idesco'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 632, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_escolaridade_lst.php');

        if (is_numeric($this->idesco)) {
            $obj = new clsCadastroEscolaridade($this->idesco);
            $registro = $obj->detalhe();
            $this->findUsage = $obj->findUsages();

            if ($this->findUsage) {
                $this->script_excluir = 'modalOpen();';
            }

            if ($registro) {
                // Passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 632, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ?
            'educar_escolaridade_det.php?idesco=' . $registro['idesco'] :
            'educar_escolaridade_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' escolaridade', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        $this->loadAssets();

        return $retorno;
    }

    public function Gerar()
    {
        // Primary keys
        $this->campoOculto(nome: 'idesco', valor: $this->idesco);

        // Outros campos
        $this->campoTexto(nome: 'descricao', campo: 'Descrição', valor: $this->descricao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

        $options = ['label' => 'Escolaridade educacenso', 'resources' => SelectOptions::escolaridades(), 'value' => $this->escolaridade];
        $this->inputsHelper()->select(attrName: 'escolaridade', inputOptions: $options);
    }

    public function Novo()
    {
        $tamanhoDesc = strlen($this->descricao);
        if ($tamanhoDesc > 60) {
            $this->mensagem = 'A descrição deve conter no máximo 60 caracteres.<br>';

            return false;
        }

        $obj = new clsCadastroEscolaridade(idesco: null, descricao: $this->descricao, escolaridade: $this->escolaridade);
        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_escolaridade_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsCadastroEscolaridade(idesco: $this->idesco, descricao: $this->descricao, escolaridade: $this->escolaridade);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escolaridade_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsCadastroEscolaridade(idesco: $this->idesco, descricao: $this->descricao);

        if ($obj->findUsages()) {
            $this->mensagem = 'Exclusão não realizada - Ainda existe vínculos.<br>';

            return false;
        }

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escolaridade_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores - Escolaridade';
        $this->processoAp = '632';
    }
};
