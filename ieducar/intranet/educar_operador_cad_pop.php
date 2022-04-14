<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_operador;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nome;
    public $valor;
    public $fim_sentenca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_operador=$_GET['cod_operador'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(589, $this->pessoa_logada, 0, 'educar_operador_lst.php', true);

        /*if( is_numeric( $this->cod_operador ) )
        {

            $obj = new clsPmieducarOperador( $this->cod_operador );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

                $obj_permissoes = new clsPermissoes();
                if( $obj_permissoes->permissao_excluir( 589, $this->pessoa_logada, 0, null, true ) )
                {
                    $this->fexcluir = true;
                }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_operador_det.php?cod_operador={$registro["cod_operador"]}" : "educar_operador_lst.php";*/
        $this->script_cancelar = 'window.parent.fechaExpansivel("div_dinamico_"+(parent.DOM_divs.length-1));';
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_operador', $this->cod_operador);

        // foreign keys

        // text
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, true);
        $this->campoMemo('valor', 'Valor', $this->valor, 60, 10, true);
        $opcoes = [ 'Não', 'Sim' ];
        $this->campoLista('fim_sentenca', 'Fim Sentenca', $opcoes, $this->fim_sentenca);

        // data
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(589, $this->pessoa_logada, 0, 'educar_operador_lst.php', true);

        $obj = new clsPmieducarOperador($this->cod_operador, $this->pessoa_logada, $this->pessoa_logada, $this->nome, $this->valor, $this->fim_sentenca, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            echo "<script>
                        parent.document.getElementById('ref_cod_operador').options[parent.document.getElementById('ref_cod_operador').options.length] = new Option('$this->nome', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_operador').value = '$cadastrou';
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();

            return true;
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

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
        $this->title = 'Operador';
        $this->processoAp = '589';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
