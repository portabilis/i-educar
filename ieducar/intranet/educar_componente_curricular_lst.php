<?php

use App\Models\LegacyDiscipline;

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
            'Área de Conhecimento',
        ];

        $obj_permissoes = new clsPermissoes;
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

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

        $objAreas = new AreaConhecimento_Model_AreaDataMapper;
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
        $result = LegacyDiscipline::query()
            ->from('modules.componente_curricular', 'cc')
            ->select([
                'cc.id',
                'cc.nome',
                'area_conhecimento_id',
                'abreviatura',
                'tipo_base',
                'area_conhecimento.nome as area_conhecimento',
            ])
            ->join('modules.area_conhecimento', 'cc.area_conhecimento_id', 'area_conhecimento.id')
            ->when(request('nome'), function ($query) {
                $query->where('cc.nome', 'ilike', '%' . request('nome') . '%');
            })
            ->when(request('abreviatura'), function ($query) {
                $query->where('cc.abreviatura', 'ilike', '%' . request('abreviatura') . '%');
            })
            ->when(request('area_conhecimento_id'), function ($query) {
                $query->where('area_conhecimento_id', request('area_conhecimento_id'));
            })
            ->when(request('tipo_base'), function ($query) {
                $query->where('cc.tipo_base', request('tipo_base'));
            })
            ->orderBy('cc.nome')
            ->paginate(
                perPage: $this->limite,
                pageName: 'pagina_' . $this->nome
            );

        $disciplinas = $result->items();
        $total = $result->total();

        foreach ($disciplinas as $disciplina) {
            $lista_busca = [
                '<a href="/module/ComponenteCurricular/view?id=' . $disciplina->getKey() . '">' .  $disciplina->nome . '</a>',
                '<a href="/module/ComponenteCurricular/view?id=' . $disciplina->getKey() . '">' .  $disciplina->abreviatura . '</a>',
                '<a href="/module/ComponenteCurricular/view?id=' . $disciplina->getKey() . '">' .  $tipos[$disciplina->tipo_base] . '</a>',
                '<a href="/module/ComponenteCurricular/view?id=' . $disciplina->getKey() . '">' .  $disciplina->area_conhecimento . '</a>',
            ];

            $this->addLinhas($lista_busca);
        }

        $this->addPaginador2(
            strUrl: 'educar_componente_curricular_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

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
