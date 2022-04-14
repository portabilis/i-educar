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

    public $cod_empresa;
    public $nome_empresa;
    public $nome_responsavel;

    public function Gerar()
    {
        $this->titulo = 'Empresas de transporte escolar - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->campoNumero('cod_empresa', 'C&oacute;digo da empresa', $this->cod_empresa, 20, 255, false);
        $this->campoTexto('nome_empresa', 'Nome fantasia', $this->nome_empresa, 50, 255, false);
        $this->campoTexto('nome_responsavel', 'Nome do responsável', $this->nome_responsavel, 50, 255, false);

        $obj_permissoes = new clsPermissoes();

        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->addCabecalhos([
            'C&oacute;digo da empresa',
            'Nome fantasia',
            'Nome do respons&aacute;vel',
            'Telefone'
        ]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_empresa = new clsModulesEmpresaTransporteEscolar();
        $obj_empresa->setLimite($this->limite, $this->offset);

        $empresas = $obj_empresa->lista($this->cod_empresa, null, null, $this->nome_empresa, $this->nome_responsavel);
        $total = $empresas->_total;

        foreach ($empresas as $registro) {
            $this->addLinhas([
                "<a href=\"transporte_empresa_det.php?cod_empresa={$registro['cod_empresa_transporte_escolar']}\">{$registro['cod_empresa_transporte_escolar']}</a>",
                "<a href=\"transporte_empresa_det.php?cod_empresa={$registro['cod_empresa_transporte_escolar']}\">{$registro['nome_empresa']}</a>",
                "<a href=\"transporte_empresa_det.php?cod_empresa={$registro['cod_empresa_transporte_escolar']}\">{$registro['nome_responsavel']}</a>",
                "<a href=\"transporte_empresa_det.php?cod_empresa={$registro['cod_empresa_transporte_escolar']}\">{$registro['telefone']}</a>"
            ]);
        }

        $this->addPaginador2('transporte_empresa_lst.php', $total, $_GET, $this->nome, $this->limite);

        //**
        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21235, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("../module/TransporteEscolar/Empresa")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de empresas de transporte', [
        url('intranet/educar_transporte_escolar_index.php') => 'Transporte escolar',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Empresas';
        $this->processoAp = '21235';
    }
};
