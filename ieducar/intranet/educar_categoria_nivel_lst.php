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

    public $cod_categoria_nivel;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_categoria_nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;

        $this->__titulo = 'Categoria Nivel - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
      'Nome Categoria Nivel'
    ]);

        // Filtros
        $this->campoTexto(
            'nm_categoria_nivel',
            'Nome Categoria Nivel',
            $this->nm_categoria_nivel,
            30,
            255,
            false
        );

        // Paginador
        $this->__limite = 20;
        $this->__offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->__limite-$this->__limite : 0;

        $obj_categoria_nivel = new clsPmieducarCategoriaNivel();
        $obj_categoria_nivel->setOrderby('nm_categoria_nivel ASC');
        $obj_categoria_nivel->setLimite($this->__limite, $this->__offset);

        $lista = $obj_categoria_nivel->lista(
            null,
            null,
            $this->nm_categoria_nivel,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_categoria_nivel->_total;

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
                $registro['data_cadastro_br']   = date('d/m/Y H:i', $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(substr($registro['data_exclusao'], 0, 16));
                $registro['data_exclusao_br']   = date('d/m/Y H:i', $registro['data_exclusao_time']);

                $obj_ref_usuario_cad = new clsPmieducarUsuario($registro['ref_usuario_cad']);
                $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
                $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

                $obj_ref_usuario_exc = new clsPmieducarUsuario($registro['ref_usuario_exc']);
                $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
                $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

                $this->addLinhas([
          sprintf(
              '<a href="educar_categoria_nivel_det.php?cod_categoria_nivel=%s">%s</a>',
              $registro['cod_categoria_nivel'],
              $registro['nm_categoria_nivel']
          )
        ]);
            }
        }

        $this->addPaginador2(
            'educar_categoria_nivel_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->__limite
        );

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(
            829,
            $this->__pessoa_logada,
            3,
            null,
            true
        )) {
            $this->acao = 'go("educar_categoria_nivel_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Categorias ou níveis do servidor', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Categorias ou níveis do servidor';
        $this->processoAp = '829';
    }
};
