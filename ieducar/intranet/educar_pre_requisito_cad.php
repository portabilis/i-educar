<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_pre_requisito;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $schema_;
    public $tabela;
    public $nome;
    public $sql;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_pre_requisito=$_GET['cod_pre_requisito'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_pre_requisito_lst.php', super_usuario: true);

        if (is_numeric(value: $this->cod_pre_requisito)) {
            $obj = new clsPmieducarPreRequisito(cod_pre_requisito: $this->cod_pre_requisito);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr(data_original: $this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr(data_original: $this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, super_usuario: true)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_pre_requisito_det.php?cod_pre_requisito={$registro['cod_pre_requisito']}" : 'educar_pre_requisito_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        $db = new clsBanco();

        // primary keys
        $this->campoOculto(nome: 'cod_pre_requisito', valor: $this->cod_pre_requisito);

        // foreign keys
        $opcoes = [ 'Selecione o Schema' ];
        $db->Consulta(consulta: 'SELECT DISTINCT schemaname FROM pg_catalog.pg_tables WHERE schemaname NOT IN (\'pg_catalog\', \'information_schema\', \'pg_toast\') ORDER BY schemaname');
        while ($db->ProximoRegistro()) {
            list($schema) = $db->Tupla();
            $opcoes[$schema] = $schema;
        }
        $this->campoLista(nome: 'schema_', campo: 'Schema', valor: $opcoes, default: $this->schema_, acao: 'buscaTabela( \'tabela\' )');

        $opcoes = [ 'Selecione a Tabela' ];
        $this->campoLista(nome: 'tabela', campo: 'Tabela', valor: $opcoes, default: $this->tabela, desabilitado: true);
        $this->campoTexto(nome: 'nome', campo: 'Nome', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoMemo(nome: 'sql', campo: 'Sql', valor: $this->sql, colunas: 60, linhas: 10);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_pre_requisito_lst.php', super_usuario: true);

        $obj = new clsPmieducarPreRequisito(cod_pre_requisito: $this->cod_pre_requisito, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, schema_: $this->schema_, tabela: $this->tabela, nome: $this->nome, sql: $this->sql, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_pre_requisito_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_pre_requisito_lst.php', super_usuario: true);

        $obj = new clsPmieducarPreRequisito(cod_pre_requisito: $this->cod_pre_requisito, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, schema_: $this->schema_, tabela: $this->tabela, nome: $this->nome, sql: $this->sql, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: $this->ativo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_pre_requisito_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 601, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_pre_requisito_lst.php', super_usuario: true);

        $obj = new clsPmieducarPreRequisito(cod_pre_requisito: $this->cod_pre_requisito, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: $this->pessoa_logada, schema_: $this->schema_, tabela: $this->tabela, nome: $this->nome, sql: $this->sql, data_cadastro: $this->data_cadastro, data_exclusao: $this->data_exclusao, ativo: 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(url: 'educar_pre_requisito_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Pre Requisito';
        $this->processoAp = '601';
    }
};
