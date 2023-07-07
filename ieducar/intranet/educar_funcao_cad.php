<?php

use App\Models\LegacyRole;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_funcao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_funcao;

    public $abreviatura;

    public $professor;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_funcao = $_GET['cod_funcao'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 634, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_funcao_lst.php');

        if (is_numeric($this->cod_funcao)) {
            $registro = LegacyRole::find($this->cod_funcao)?->getAttributes();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 634, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }

            if ($this->professor == '0') {
                $this->professor = 'N';
            } elseif ($this->professor == '1') {
                $this->professor = 'S';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}" : 'educar_funcao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' função', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'cod_funcao', valor: $this->cod_funcao);

        $obrigatorio = true;
        include 'include/pmieducar/educar_campo_lista.php';

        // text
        $this->campoTexto(nome: 'nm_funcao', campo: 'Funcão', valor: $this->nm_funcao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoTexto(nome: 'abreviatura', campo: 'Abreviatura', valor: $this->abreviatura, tamanhovisivel: 30, tamanhomaximo: 30, obrigatorio: true);
        $opcoes = [
            '' => 'Selecione',
            'S' => 'Sim',
            'N' => 'Não',
        ];

        $this->campoLista(nome: 'professor', campo: 'Professor', valor: $opcoes, default: $this->professor);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 634, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_funcao_lst.php');

        if ($this->professor == 'N') {
            $this->professor = '0';
        } elseif ($this->professor == 'S') {
            $this->professor = '1';
        }

        $obj = new LegacyRole();
        $obj->nm_funcao = $this->nm_funcao;
        $obj->abreviatura = $this->abreviatura;
        $obj->professor = $this->professor;
        $obj->ref_cod_instituicao = $this->ref_cod_instituicao;
        $obj->ref_usuario_cad = $this->pessoa_logada;

        if ($obj->save()) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        if ($this->professor == 'N') {
            $this->professor = '0';
        } elseif ($this->professor == 'S') {
            $this->professor = '1';
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 634, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_funcao_lst.php');

        $obj = LegacyRole::find($this->cod_funcao);
        $obj->nm_funcao = $this->nm_funcao;
        $obj->abreviatura = $this->abreviatura;
        $obj->professor = $this->professor;
        $obj->ref_cod_instituicao = $this->ref_cod_instituicao;
        $obj->ref_usuario_exc = $this->pessoa_logada;

        if ($obj->save()) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 634, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_funcao_lst.php');

        $obj = LegacyRole::find($this->cod_funcao);

        if ($obj->delete()) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Servidores -  Funções do servidor';
        $this->processoAp = '634';
    }
};
