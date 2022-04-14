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

    public $cod_motivo_afastamento;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Motivo Afastamento - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Motivo de Afastamento'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            //$lista_busca[] = "Escola";
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys

        // outros Filtros
        $get_escola = false;
        include('include/pmieducar/educar_campo_lista.php');

        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_motivo', 'Motivo de Afastamento', $this->nm_motivo, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_motivo_afastamento = new clsPmieducarMotivoAfastamento();
        $obj_motivo_afastamento->setOrderby('nm_motivo ASC');
        $obj_motivo_afastamento->setLimite($this->limite, $this->offset);

        $lista = $obj_motivo_afastamento->lista(
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
            $this->ref_cod_instituicao
        );

        $total = $obj_motivo_afastamento->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                //$obj_ins = new clsPmieducarEscola( $registro["ref_cod_escola"] );
                //$det_ins = $obj_ins->detalhe();

                //  $ref_cod_instituicao = $det_ins["ref_cod_instituicao"];
                $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_instituicao = $obj_instituicao->detalhe();

                $lista_busca = [
                    "<a href=\"educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}\">{$registro['nm_motivo']}</a>"
                ];

                if ($nivel_usuario == 1) {
                    //$lista_busca[] = "<a href=\"educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro["cod_motivo_afastamento"]}\">{$det_ins["nome"]}</a>";
                    $lista_busca[] = "<a href=\"educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}\">{$det_instituicao['nm_instituicao']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_motivo_afastamento_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(633, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_motivo_afastamento_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Motivos de afastamento do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivos de afastamento do servidor';
        $this->processoAp = '633';
    }
};
