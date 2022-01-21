<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_cliente;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_cod_biblioteca;
    public $ref_cod_cliente_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $login;
    public $senha;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $del_cod_cliente;
    public $del_cod_cliente_tipo;
    public $acao_status;
    public $cod_motivo_suspensao;
    public $descricao;
    public $dias;
    public $sequencial;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_cliente        = $_GET['cod_cliente'];
        $this->acao_status        = $_GET['status'];
        $this->ref_cod_biblioteca = $_GET['ref_cod_biblioteca'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_det.php');

        if (is_numeric($this->cod_cliente)) {
            $obj = new clsPmieducarCliente($this->cod_cliente);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                if ($this->acao_status == 'liberar') {
                    $retorno = 'Editar';
                }
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_cliente_det.php?cod_cliente={$registro['cod_cliente']}" : 'educar_cliente_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Motivo de suspensão do cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        if ($this->acao_status == 'suspender') {
            $this->campoOculto('cod_cliente', $this->cod_cliente);
            $this->campoOculto('ref_cod_biblioteca', $this->ref_cod_biblioteca);

            if ($this->ref_idpes) {
                $objTemp = new clsPessoaFisica($this->ref_idpes);
                $detalhe = $objTemp->detalhe();

                $this->campoRotulo('nm_cliente', 'Cliente', $detalhe['nome']);
            }
            $this->campoNumero('dias', 'Dias', $this->dias, 9, 9, true);
            echo "<script> descricao = new Array();\n </script>";
            $opcoes[''] = 'Selecione um motivo';
            $todos_motivos = '';
            $obj_motivo_suspensao = new clsPmieducarMotivoSuspensao();
            $lst_motivo_suspensao = $obj_motivo_suspensao->listaClienteBiblioteca($this->cod_cliente);
            if ($lst_motivo_suspensao) {
                foreach ($lst_motivo_suspensao as $motivo_suspensao) {
                    $todos_motivos .= "descricao[descricao.length] = new Array( {$motivo_suspensao['cod_motivo_suspensao']}, '{$motivo_suspensao['descricao']}' );\n";
                    $opcoes["{$motivo_suspensao['cod_motivo_suspensao']}"] = "{$motivo_suspensao['nm_motivo']}";
                }
                echo "<script>{$todos_motivos}</script>";
                $this->campoLista('cod_motivo_suspensao', 'Motivo da Suspensão', $opcoes, $this->cod_motivo_suspensao, '', false, '', '', false, true);
                $this->campoMemo('descricao', 'Descrição', $this->descricao, 50, 5, false, '', '', false, false, 'onClick', true);
                echo '<script>
                            var before_getDescricao = function(){}
                            var after_getDescricao  = function(){}

                            function getDescricao()
                            {
                                before_getDescricao();

                                var campoMotivoSuspensao = document.getElementById( \'cod_motivo_suspensao\' ).value;
                                var campoDescricao       = document.getElementById( \'descricao\' );
                                for ( var j = 0; j < descricao.length; j++ )
                                {
                                    if ( descricao[j][0] == campoMotivoSuspensao )
                                    {
                                        campoDescricao.value = descricao[j][1];
                                    }
                                    else if ( campoMotivoSuspensao == \'\' )
                                    {
                                        campoDescricao.value = \'Sem descrição...\';
                                    }
                                }
                                if ( campoDescricao.length == 0 && campoMotivoSuspensao != \'\' ) {
                                    campoDescricao.value = \'Sem descrição...\';
                                }

                                after_getDescricao();
                            }
                         </script>';
            } else {
                $this->campoLista('cod_motivo_suspensao', 'Motivo da Suspensão', ['' => 'Não há motivo cadastrado'], '', '', false, '', '', true, true);
            }
        } elseif ($this->acao_status == 'liberar') {
            $db               = new clsBanco();
            $this->sequencial = $db->CampoUnico("SELECT MAX( sequencial ) FROM pmieducar.cliente_suspensao WHERE ref_cod_cliente = {$this->cod_cliente} AND data_liberacao IS NULL");
            $this->campoOculto('sequencial', $this->sequencial);

            $this->Editar();
        }
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

        $obj = new clsPmieducarClienteSuspensao(null, $this->cod_cliente, $this->cod_motivo_suspensao, null, $this->pessoa_logada, $this->dias, null, null);

        // Caso suspensão tenha sido efetuada, envia para página de detalhes
        if ($sequencial = $obj->cadastra()) {
            $obj->sequencial = $sequencial;

            $this->mensagem .= 'Suspensão efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_cliente_det.php?cod_cliente={$this->cod_cliente}&ref_cod_biblioteca={$this->ref_cod_biblioteca}");
        }

        $this->mensagem = 'Suspensão não realizada.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

        $obj_suspensao = new clsPmieducarClienteSuspensao($this->sequencial, $this->cod_cliente, null, $this->pessoa_logada, null, null, null, null);
        if ($obj_suspensao->edita()) {
            $this->mensagem .= 'Liberação efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_cliente_lst.php?cod_cliente={$this->cod_cliente}");
        }
        $obj = new clsPmieducarCliente($this->cod_cliente, $this->pessoa_logada, $this->pessoa_logada, $this->ref_idpes, $this->login, $senha, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        $this->mensagem = 'Liberação não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

        $obj = new clsPmieducarCliente($this->cod_cliente, $this->ref_cod_cliente_tipo, $this->pessoa_logada, $this->pessoa_logada, $this->ref_idpes, $this->login, $this->senha, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_cliente_det.php?cod_cliente={$this->cod_cliente}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/define-status-cliente-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Cliente';
        $this->processoAp = '603';
    }
};
