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

    public $ref_cod_instituicao;
    public $nome;
    public $abreviatura;
    public $tipo_base;
    public $area_conhecimento_id;

    public function Gerar()
    {
        $this->titulo = 'Componentes curriculares - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Nome',
            'Abreviatura',
            'Base',
            '&Aacute;rea de conhecimento'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nome', 'Nome', $this->nome, 41, 255, false);
        $this->campoTexto('abreviatura', 'Abreviatura', $this->abreviatura, 41, 255, false);

        $tipos = ComponenteCurricular_Model_TipoBase::getInstance();
        $tipos = $tipos->getEnums();
        $tipos = Portabilis_Array_Utils::insertIn(null, 'Selecionar', $tipos);

        $options = [
            'label'       => 'Base Curricular',
            'placeholder' => 'Base curricular',
            'value'       => $this->tipo_base,
            'resources'   => $tipos,
            'required'    => false
        ];

        $this->inputsHelper()->select('tipo_base', $options);

        $objAreas = new AreaConhecimento_Model_AreaDataMapper();
        $objAreas = $objAreas->findAll(['id', 'nome', 'agrupar_descritores']);
        $areas = [];

        foreach ($objAreas as $area) {
            if ($area->agrupar_descritores) {
                $area->nome .= ' (agrupador)';
            }
            $areas[$area->id] = $area->nome;
        }

        $areas = Portabilis_Array_Utils::insertIn(null, 'Selecionar', $areas);

        $options = [
            'label'       => 'Área de conhecimento',
            'placeholder' => 'Área de conhecimento',
            'value'       => $this->area_conhecimento_id,
            'resources'   => $areas,
            'required'    => false
        ];

        $this->inputsHelper()->select('area_conhecimento_id', $options);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $objCC = new clsModulesComponenteCurricular();
        $objCC->setOrderby('cc.nome ASC');
        $objCC->setLimite($this->limite, $this->offset);

        $lista = $objCC->lista(
            $this->ref_cod_instituicao,
            $this->nome,
            $this->abreviatura,
            $this->tipo_base,
            $this->area_conhecimento_id
        );

        $total = $objCC->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['instituicao_id']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['instituicao_id'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro['id']}\">{$registro['nome']}</a>",
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro['id']}\">{$registro['abreviatura']}</a>",
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro['id']}\">".$tipos[$registro['tipo_base']].'</a>',
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro['id']}\">{$registro['area_conhecimento']}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"module/ComponenteCurricular/view?id={$registro['id']}\">{$registro['instituicao_id']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_componente_curricular_lst.php', $total, $_GET, $this->nome, $this->limite);

        if ($obj_permissoes->permissao_cadastra(580, $this->pessoa_logada, 3)) {
            $this->acao = 'go("/module/ComponenteCurricular/edit")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Listagem de componentes curriculares', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Componentes curriculares';
        $this->processoAp = '946';
    }
};
