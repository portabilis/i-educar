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

    public $cod_aluno_beneficio;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_beneficio;
    public $desc_beneficio;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Benef&iacute;cio Aluno - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Beneficio'
        ]);

        // Filtros de Foreign Keys

        //$obrigatorio = true;
        //include("include/pmieducar/educar_pesquisa_instituicao_escola.php");

        // outros Filtros
        $this->campoTexto('nm_beneficio', 'Benef&iacute;cio', $this->nm_beneficio, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_aluno_beneficio = new clsPmieducarAlunoBeneficio();
        $obj_aluno_beneficio->setOrderby('nm_beneficio ASC');
        $obj_aluno_beneficio->setLimite($this->limite, $this->offset);

        $lista = $obj_aluno_beneficio->lista(
            null,
            null,
            null,
            $this->nm_beneficio,
            null,
            null,
            null,
            1
        );

        $total = $obj_aluno_beneficio->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $this->addLinhas([
                    "<a href=\"educar_aluno_beneficio_det.php?cod_aluno_beneficio={$registro['cod_aluno_beneficio']}\">{$registro['nm_beneficio']}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_aluno_beneficio_lst.php', $total, $_GET, $this->nome, $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(581, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_aluno_beneficio_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->largura = '100%';

        $this->breadcrumb('Tipos de benefício do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Benefício do aluno';
        $this->processoAp = '581';
    }
};
