<?php

return new class extends clsListagem {
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

    public $cod_modulo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $num_etapas;
    public $num_meses;
    public $num_semanas;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Etapa - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Etapa',
            'Número de etapas',
            'Número de meses',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->campoTexto('nm_tipo', 'Etapa', $this->nm_tipo, 30, 255, false);
        $this->campoNumero('num_meses', 'Número de meses', $this->num_meses, 2, 2, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_modulo = new clsPmieducarModulo();
        $obj_modulo->setOrderby('nm_tipo ASC');
        $obj_modulo->setLimite($this->limite, $this->offset);

        $lista = $obj_modulo->lista(
            null,
            null,
            null,
            $this->nm_tipo,
            null,
            $this->num_meses,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_modulo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['num_etapas']}</a>",
                    "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['num_meses']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_modulo_det.php?cod_modulo={$registro['cod_modulo']}\">{$registro['ref_cod_instituicao']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_modulo_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(584, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_modulo_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de etapas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Etapa';
        $this->processoAp = '584';
    }
};
