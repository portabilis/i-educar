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

    public $cod_motivo_baixa;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo_baixa;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        $this->titulo = 'Motivo Baixa - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Motivo Baixa',
            'Biblioteca'
        ]);

        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_motivo_baixa', 'Motivo Baixa', $this->nm_motivo_baixa, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if (!is_numeric($this->ref_cod_biblioteca)) {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_motivo_baixa = new clsPmieducarMotivoBaixa();
        $obj_motivo_baixa->setOrderby('nm_motivo_baixa ASC');
        $obj_motivo_baixa->setLimite($this->limite, $this->offset);

        $lista = $obj_motivo_baixa->lista(
            null,
            null,
            null,
            $this->nm_motivo_baixa,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_biblioteca
        );

        $total = $obj_motivo_baixa->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_biblioteca = $obj_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_biblioteca['nm_biblioteca'];
                $this->addLinhas([
                    "<a href=\"educar_motivo_baixa_det.php?cod_motivo_baixa={$registro['cod_motivo_baixa']}\">{$registro['nm_motivo_baixa']}</a>",
                    "<a href=\"educar_motivo_baixa_det.php?cod_motivo_baixa={$registro['cod_motivo_baixa']}\">{$registro['ref_cod_biblioteca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_motivo_baixa_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(600, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_motivo_baixa_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de motivos de baixa', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Baixa';
        $this->processoAp = '600';
    }
};
