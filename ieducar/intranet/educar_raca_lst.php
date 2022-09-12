<?php

return new class extends clsListagem {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $__pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $__offset;

    public $cod_raca;
    public $idpes_exc;
    public $idpes_cad;
    public $nm_raca;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;

        $this->__titulo = 'Raça - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Raça'
        ]);

        // outros Filtros
        $this->campoTexto('nm_raca', 'Raça', $this->nm_raca, 30, 255, false);

        // Paginador
        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

        $obj_raca = new clsCadastroRaca();
        $obj_raca->setOrderby('nm_raca ASC');
        $obj_raca->setLimite($this->__limite, $this->__offset);

        $lista = $obj_raca->lista(
            null,
            null,
            $this->nm_raca,
            null,
            null,
            null,
            null,
            't'
        );

        $total = $obj_raca->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
                $registro['data_cadastro_br'] = date('d/m/Y H:i', $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(substr($registro['data_exclusao'], 0, 16));
                $registro['data_exclusao_br'] = date('d/m/Y H:i', $registro['data_exclusao_time']);

                $this->addLinhas([
                    "<a href=\"educar_raca_det.php?cod_raca={$registro['cod_raca']}\">{$registro['nm_raca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_raca_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(678, $this->__pessoa_logada, 7)) {
            $this->acao = 'go("educar_raca_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Listagem de raças', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Raça';
        $this->processoAp = '678';
    }
};
