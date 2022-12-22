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

        $this->addCabecalhos(coluna: [
            'Nome',
            'Valor',
            'Fim Sentenca'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto(nome: 'nome', campo: 'Nome', valor: $this->nome, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoTexto(nome: 'valor', campo: 'Valor', valor: $this->valor, tamanhovisivel: 30, tamanhomaximo: 255);
        $opcoes = [ 'Não', 'Sim' ];
        $this->campoLista(nome: 'fim_sentenca', campo: 'Fim Sentenca', valor: $opcoes, default: $this->fim_sentenca, obrigatorio: false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_operador = new clsPmieducarOperador();
        $obj_operador->setOrderby(strNomeCampo: 'nome ASC');
        $obj_operador->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_operador->lista(
            int_cod_operador: $this->cod_operador,
            str_nome: $this->nome,
            str_valor: $this->valor,
            int_fim_sentenca: $this->fim_sentenca,
            date_data_exclusao_ini: 1
        );

        $total = $obj_operador->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(datetime: substr(string: $registro['data_cadastro'], offset: 0, length: 16));
                $registro['data_cadastro_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(datetime: substr(string: $registro['data_exclusao'], offset: 0, length: 16));
                $registro['data_exclusao_br'] = date(format: 'd/m/Y H:i', timestamp: $registro['data_exclusao_time']);

                $obj_ref_usuario_exc = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_exc']);
                $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
                $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

                $obj_ref_usuario_cad = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_cad']);
                $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
                $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

                $registro['fim_sentenca'] = ($registro['fim_sentenca']) ? 'Sim': 'Não';

                $this->addLinhas(linha: [
                    "<a href=\"educar_operador_det.php?cod_operador={$registro['cod_operador']}\">{$registro['nome']}</a>",
                    "<a href=\"educar_operador_det.php?cod_operador={$registro['cod_operador']}\">{$registro['valor']}</a>",
                    "<a href=\"educar_operador_det.php?cod_operador={$registro['cod_operador']}\">{$registro['fim_sentenca']}</a>"
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_operador_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 589, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0, super_usuario: true)) {
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
