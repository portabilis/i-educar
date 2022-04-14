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

    public $cod_motivo_suspensao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_biblioteca;

    public function Gerar()
    {
        $this->titulo = 'Motivo Suspens&atilde;o - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Motivo Suspens&atilde;o',
            'Biblioteca'
        ]);

        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_motivo', 'Motivo Suspens&atilde;o', $this->nm_motivo, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if (!is_numeric($this->ref_cod_biblioteca)) {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_motivo_suspensao = new clsPmieducarMotivoSuspensao();
        $obj_motivo_suspensao->setOrderby('nm_motivo ASC');
        $obj_motivo_suspensao->setLimite($this->limite, $this->offset);

        $lista = $obj_motivo_suspensao->lista(
            null,
            null,
            null,
            $this->nm_motivo,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_biblioteca
        );

        $total = $obj_motivo_suspensao->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_biblioteca = $obj_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_biblioteca['nm_biblioteca'];
                $this->addLinhas([
                    "<a href=\"educar_motivo_suspensao_det.php?cod_motivo_suspensao={$registro['cod_motivo_suspensao']}\">{$registro['nm_motivo']}</a>",
                    "<a href=\"educar_motivo_suspensao_det.php?cod_motivo_suspensao={$registro['cod_motivo_suspensao']}\">{$registro['ref_cod_biblioteca']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_motivo_suspensao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(607, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_motivo_suspensao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de motivos de de suspensão', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivo Suspens&atilde;o';
        $this->processoAp = '607';
    }
};
