<?php

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

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
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Nome',
            'Abreviatura',
            'Base',
            'área de conhecimento',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos($lista_busca);

        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto(nome: 'nome', campo: 'Nome', valor: $this->nome, tamanhovisivel: 41, tamanhomaximo: 255);
        $this->campoTexto(nome: 'abreviatura', campo: 'Abreviatura', valor: $this->abreviatura, tamanhovisivel: 41, tamanhomaximo: 255);

        $tipos = ComponenteCurricular_Model_TipoBase::getInstance();
        $tipos = $tipos->getEnums();
        $tipos = Portabilis_Array_Utils::insertIn(key: null, value: 'Selecionar', array: $tipos);

        $options = [
            'label' => 'Base Curricular',
            'placeholder' => 'Base curricular',
            'value' => $this->tipo_base,
            'resources' => $tipos,
            'required' => false,
        ];

        $this->inputsHelper()->select(attrName: 'tipo_base', inputOptions: $options);

        $objAreas = new AreaConhecimento_Model_AreaDataMapper();
        $objAreas = $objAreas->findAll(['id', 'nome', 'agrupar_descritores']);
        $areas = [];

        foreach ($objAreas as $area) {
            if ($area->agrupar_descritores) {
                $area->nome .= ' (agrupador)';
            }
            $areas[$area->id] = $area->nome;
        }

        $areas = Portabilis_Array_Utils::insertIn(key: null, value: 'Selecionar', array: $areas);

        $options = [
            'label' => 'Área de conhecimento',
            'placeholder' => 'Área de conhecimento',
            'value' => $this->area_conhecimento_id,
            'resources' => $areas,
            'required' => false,
        ];

        $this->inputsHelper()->select(attrName: 'area_conhecimento_id', inputOptions: $options);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $objCC = new clsModulesComponenteCurricular();
        $objCC->setOrderby('cc.nome ASC');
        $objCC->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $objCC->lista(
            instituicao_id: $this->ref_cod_instituicao,
            nome: $this->nome,
            abreviatura: $this->abreviatura,
            tipo_base: $this->tipo_base,
            area_conhecimento_id: $this->area_conhecimento_id
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
                    "<a href=\"/module/ComponenteCurricular/view?id={$registro['id']}\">{$registro['area_conhecimento']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"module/ComponenteCurricular/view?id={$registro['id']}\">{$registro['instituicao_id']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_componente_curricular_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 580, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("/module/ComponenteCurricular/edit")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de componentes curriculares', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Componentes curriculares';
        $this->processoAp = '946';
    }
};
