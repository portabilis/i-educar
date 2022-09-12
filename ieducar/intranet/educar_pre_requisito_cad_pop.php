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
        $obj_permissoes->permissao_cadastra(601, $this->pessoa_logada, 3, 'educar_pre_requisito_lst.php', true);

        if (is_numeric($this->cod_pre_requisito)) {
            $obj = new clsPmieducarPreRequisito($this->cod_pre_requisito);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(601, $this->pessoa_logada, 3, null, true)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }

        $this->nome_url_cancelar = 'Cancelar';
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';

        return $retorno;
    }

    public function Gerar()
    {
        $db = new clsBanco();

        // primary keys
        $this->campoOculto('cod_pre_requisito', $this->cod_pre_requisito);

        // foreign keys
        $opcoes = [ 'Selecione o Schema' ];
        $db->Consulta('SELECT DISTINCT schemaname FROM pg_catalog.pg_tables WHERE schemaname NOT IN (\'pg_catalog\', \'information_schema\', \'pg_toast\') ORDER BY schemaname');
        while ($db->ProximoRegistro()) {
            list($schema) = $db->Tupla();
            $opcoes[$schema] = $schema;
        }
        $this->campoLista('schema_', 'Schema', $opcoes, $this->schema_, 'buscaTabela( \'tabela\' )');

        $opcoes = [ 'Selecione a Tabela' ];
        $this->campoLista('tabela', 'Tabela', $opcoes, $this->tabela, '', false, '', '', true);

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, true);
        $this->campoMemo('sql', 'Sql', $this->sql, 60, 10, false);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(601, $this->pessoa_logada, 3, 'educar_pre_requisito_lst.php', true);

        $obj = new clsPmieducarPreRequisito($this->cod_pre_requisito, $this->pessoa_logada, $this->pessoa_logada, $this->schema_, $this->tabela, $this->nome, $this->sql, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        parent.document.getElementById('ref_cod_pre_requisito').options[parent.document.getElementById('ref_cod_pre_requisito').options.length] = new Option('$this->nome', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_pre_requisito').value = '$cadastrou';
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();

            return true;
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
    }

    public function Excluir()
    {
    }

    public function Formular()
    {
        $this->title = 'Pre Requisito';
        $this->processoAp = '601';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
