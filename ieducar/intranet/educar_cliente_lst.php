<?php

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_cliente;
    public $ref_cod_cliente_tipo;
    public $ref_cod_biblioteca;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $login;
    public $senha;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $status;

    public function Gerar()
    {
        $this->titulo = 'Cliente - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Código',
            'Cliente',
            'Tipo',
            'Status'
        ]);

        $this->inputsHelper()->text('nome_cliente', ['required' => false, 'value' => $this->nome_cliente, 'label' => 'Nome']);
        $this->inputsHelper()->integer('codigo_cliente', ['required' => false, 'value' => $this->codigo_cliente, 'label' => 'Código']);

        $this->campoLista('status', 'Status', [ '' => 'Selecione', 'R' => 'Regular', 'S' => 'Suspenso' ], $this->status, '', false, '', '', false, false);

        $instituicao_obrigatorio  = true;
        $escola_obrigatorio       = false;
        $biblioteca_obrigatorio   = true;
        $cliente_tipo_obrigatorio = true;
        $get_instituicao          = true;
        $get_escola               = true;
        $get_biblioteca           = true;
        $get_cliente_tipo         = true;

        include('include/pmieducar/educar_campo_lista.php');

        $this->inputsHelper()->dynamic('instituicao', ['required' => true, 'instituicao' => $this->ref_cod_instituicao]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_cliente = new clsPmieducarCliente();
        $obj_cliente->setOrderby('nome ASC');
        $obj_cliente->setLimite($this->limite, $this->offset);

        if ($this->status != 'S') {
            $this->status = null;
        }

        $cod_biblioteca = $this->ref_cod_biblioteca;
        if (!is_numeric($this->ref_cod_biblioteca)) {
            $db = new clsBanco();
            $db->Consulta("SELECT ref_cod_biblioteca FROM pmieducar.biblioteca_usuario WHERE ref_cod_usuario = '$this->pessoa_logada' ");
            if ($db->numLinhas()) {
                $cod_biblioteca = [];
                while ($db->ProximoRegistro()) {
                    list($ref_cod) = $db->Tupla();
                    $cod_biblioteca[] = $ref_cod;
                }
            }
        }
        $lista = $obj_cliente->listaCompleta(
            $this->codigo_cliente,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->nome_cliente,
            $this->status,
            $this->ref_cod_cliente_tipo,
            null,
            $cod_biblioteca
        );
        $total = $obj_cliente->_total;
        $obj_banco = new clsBanco();
        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $sql_unico = "SELECT 1
                                FROM pmieducar.cliente_suspensao
                               WHERE ref_cod_cliente = {$registro['cod_cliente']}
                                 AND data_liberacao IS NULL
                                 AND EXTRACT ( DAY FROM ( NOW() - data_suspensao ) ) < dias";
                $suspenso  = $obj_banco->CampoUnico($sql_unico);
                if (is_numeric($suspenso)) {
                    $registro['status'] = 'Suspenso';
                } else {
                    $registro['status'] = 'Regular';
                }

                $this->addLinhas([
                    "<a href=\"educar_cliente_det.php?cod_cliente={$registro['cod_cliente']}&ref_cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['cod_cliente']}</a>",
                    "<a href=\"educar_cliente_det.php?cod_cliente={$registro['cod_cliente']}&ref_cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['nome']}</a>",
                    "<a href=\"educar_cliente_det.php?cod_cliente={$registro['cod_cliente']}&ref_cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_cliente_det.php?cod_cliente={$registro['cod_cliente']}&ref_cod_biblioteca={$registro['cod_biblioteca']}\">{$registro['status']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_cliente_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_cliente_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de clientes', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Cliente';
        $this->processoAp = '603';
    }
};
