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

    public $cod_ponto;
    public $descricao;

    public function Gerar()
    {
        $this->titulo = 'Pontos - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->campoNumero('cod_ponto', 'Código do ponto', $this->cod_ponto, 20, 255, false);
        $this->campoTexto('descricao', 'Descrição', $this->descricao, 50, 255, false);

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->addCabecalhos([
            'Código do ponto',
            'Descrição',
            'CEP',
            'Município - UF',
            'Bairro',
            'Logradouro'
        ]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_ponto = new clsModulesPontoTransporteEscolar();
        $obj_ponto->setOrderBy(' descricao asc ');
        $obj_ponto->setLimite($this->limite, $this->offset);

        $pontos = $obj_ponto->lista($this->cod_ponto, $this->descricao);
        $total = $pontos->_total;

        foreach ($pontos as $registro) {
            $cep = is_numeric($registro['cep']) ? int2CEP($registro['cep']) : '-';
            $municipio = is_string($registro['municipio']) ? $registro['municipio'] . ' - '. $registro['sigla_uf'] : '-';
            $bairro = is_string($registro['bairro']) ? $registro['bairro'] : '-';
            $logradouro = is_string($registro['logradouro']) ? $registro['logradouro'] : '-';

            $this->addLinhas([
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro['cod_ponto_transporte_escolar']}\">{$registro['cod_ponto_transporte_escolar']}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro['cod_ponto_transporte_escolar']}\">{$registro['descricao']}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro['cod_ponto_transporte_escolar']}\">{$cep}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro['cod_ponto_transporte_escolar']}\">{$municipio}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro['cod_ponto_transporte_escolar']}\">{$bairro}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro['cod_ponto_transporte_escolar']}\">{$logradouro}</a>",
            ]);
        }

        $this->addPaginador2('transporte_ponto_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21239, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("../module/TransporteEscolar/Ponto")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de pontos', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Pontos';
        $this->processoAp = '21239';
    }
};
