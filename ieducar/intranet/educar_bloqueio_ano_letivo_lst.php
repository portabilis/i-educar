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

    public $ref_instituicao;
    public $ref_ano;
    public $data_inicio;
    public $data_fim;
    public $ano;

    public function Gerar()
    {
        $this->titulo = 'Bloqueio do ano letivo - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->ref_ano = $this->ano;

        $this->addCabecalhos([
            'Instituição',
            'Ano',
            'Data inicial permitida',
            'Data final permitida',
        ]);

        $this->inputsHelper()->dynamic('instituicao', [],['options' => ['required' => false]]);
        $this->inputsHelper()->dynamic('ano', ['value' => $this->ref_ano, 'required' => false]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsPmieducarBloqueioAnoLetivo();
        $obj->setOrderby('instituicao ASC, ref_ano DESC');
        $obj->setLimite($this->limite, $this->offset);

        $lista = $obj->lista(
            $this->ref_cod_instituicao,
            $this->ref_ano
        );

        $total = $obj->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $data_inicio = dataToBrasil($registro['data_inicio']);
                $data_fim = dataToBrasil($registro['data_fim']);

                $this->addLinhas([
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$registro['instituicao']}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$registro['ref_ano']}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$data_inicio}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$data_fim}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_bloqueio_ano_letivo_lst.php', $total, $_GET, $this->nome, $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(21251, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_bloqueio_ano_letivo_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->largura = '100%';

        $this->breadcrumb('Listagem de bloqueios do ano letivo', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Bloqueio do ano letivo';
        $this->processoAp = '21251';
    }
};
