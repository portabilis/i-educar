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

    public $cod_pessoa_transporte;
    public $ref_cod_rota_transporte_escolar;
    public $nome_pessoa;
    public $nome_destino;
    public $ano_rota;

    public function Gerar()
    {
        $this->titulo = 'Usuário de transporte - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->campoNumero('cod_pessoa_transporte', 'Código', $this->cod_pessoa_transporte, 20, 255, false);
        $this->campoTexto('nome_pessoa', 'Nome da pessoa', $this->nome_pessoa, 50, 255, false);
        $this->campoTexto('nome_destino', 'Nome do destino', $this->nome_destino, 70, 255, false);

        $this->campoTexto('ano_rota', 'Ano', $this->ano_rota, 20, 4, false);

        $this->inputsHelper()->dynamic('rotas', ['required' =>  false]);

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->addCabecalhos([
            'Código',
            'Nome da pessoa',
            'Rota',
            'Destino',
            'Ponto de embarque'
        ]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsModulesPessoaTransporte();
        $obj->setLimite($this->limite, $this->offset);

        $lista = $obj->lista($this->cod_pessoa_transporte, null, $this->ref_cod_rota_transporte_escolar, null, null, $this->nome_pessoa, $this->nome_destino, $this->ano_rota);

        $total = $obj->_total;

        foreach ($lista as $registro) {
            $this->addLinhas([
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro['cod_pessoa_transporte']}\">{$registro['cod_pessoa_transporte']}</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro['cod_pessoa_transporte']}\">{$registro['nome_pessoa']}</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro['cod_pessoa_transporte']}\">{$registro['nome_rota']}</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro['cod_pessoa_transporte']}\">".(trim($registro['nome_destino'])=='' ? $registro['nome_destino2'] : $registro['nome_destino']).'</a>',
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro['cod_pessoa_transporte']}\">{$registro['nome_ponto']}</a>"
            ]);
        }

        $this->addPaginador2('transporte_pessoa_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21240, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("../module/TransporteEscolar/Pessoatransporte")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de usuários de tranposrte', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Usuários de transporte';
        $this->processoAp = '21240';
    }
};
