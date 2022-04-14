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

    public $cod_operador;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nome;
    public $valor;
    public $fim_sentenca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Operador - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Valor',
            'Fim Sentenca'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, false);
        $this->campoTexto('valor', 'Valor', $this->valor, 30, 255, false);
        $opcoes = [ 'Não', 'Sim' ];
        $this->campoLista('fim_sentenca', 'Fim Sentenca', $opcoes, $this->fim_sentenca, '', false, '', '', false, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_operador = new clsPmieducarOperador();
        $obj_operador->setOrderby('nome ASC');
        $obj_operador->setLimite($this->limite, $this->offset);

        $lista = $obj_operador->lista(
            $this->cod_operador,
            null,
            null,
            $this->nome,
            $this->valor,
            $this->fim_sentenca,
            null,
            null,
            1
        );

        $total = $obj_operador->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
                $registro['data_cadastro_br'] = date('d/m/Y H:i', $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(substr($registro['data_exclusao'], 0, 16));
                $registro['data_exclusao_br'] = date('d/m/Y H:i', $registro['data_exclusao_time']);

                $obj_ref_usuario_exc = new clsPmieducarUsuario($registro['ref_usuario_exc']);
                $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
                $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

                $obj_ref_usuario_cad = new clsPmieducarUsuario($registro['ref_usuario_cad']);
                $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
                $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

                $registro['fim_sentenca'] = ($registro['fim_sentenca']) ? 'Sim': 'Não';

                $this->addLinhas([
                    "<a href=\"educar_operador_det.php?cod_operador={$registro['cod_operador']}\">{$registro['nome']}</a>",
                    "<a href=\"educar_operador_det.php?cod_operador={$registro['cod_operador']}\">{$registro['valor']}</a>",
                    "<a href=\"educar_operador_det.php?cod_operador={$registro['cod_operador']}\">{$registro['fim_sentenca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_operador_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(589, $this->pessoa_logada, 0, null, true)) {
            $this->acao = 'go("educar_operador_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Operador';
        $this->processoAp = '589';
    }
};
