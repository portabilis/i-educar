<?php

use App\Models\LegacyExemptionType;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $ref_cod_matricula;

    public $ref_cod_serie;

    public $ref_cod_escola;

    public $ref_cod_disciplina;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_tipo_dispensa;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $observacao;

    public $ref_sequencial;

    public $ref_cod_instituicao;

    public $ref_cod_turma;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 628);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Gerar()
    {
        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

        $this->titulo = 'Dispensa Componente Curricular - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        if (!$_GET['ref_cod_matricula']) {
            $this->simpleRedirect(url: 'educar_matricula_lst.php');
        }

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista(int_cod_matricula: $this->ref_cod_matricula);

        if (is_array(value: $lst_matricula)) {
            $det_matricula = array_shift(array: $lst_matricula);
            $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

            $obj_matricula_turma = new clsPmieducarMatriculaTurma();
            $lst_matricula_turma = $obj_matricula_turma->lista(
                int_ref_cod_matricula: $this->ref_cod_matricula,
                int_ativo: 1,
                int_ref_cod_serie: $this->ref_cod_serie,
                int_ref_cod_escola: $this->ref_cod_escola
            );

            if (is_array(value: $lst_matricula_turma)) {
                $det = array_shift(array: $lst_matricula_turma);
                $this->ref_cod_turma = $det['ref_cod_turma'];
                $this->ref_sequencial = $det['sequencial'];
            }
        }

        $this->campoOculto(nome: 'ref_cod_turma', valor: $this->ref_cod_turma);

        $this->addCabecalhos(coluna: [
            'Disciplina',
            'Tipo Dispensa',
            'Data Dispensa',
        ]);

        $opcoes = LegacyExemptionType::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_tipo_dispensa')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(
            nome: 'ref_cod_tipo_dispensa',
            campo: 'Motivo',
            valor: $opcoes,
            default: $this->ref_cod_tipo_dispensa,
            obrigatorio: false
        );

        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);

        // outros Filtros
        $opcoes = ['' => 'Selecione'];

        // Escola série disciplina
        $componentes = App_Model_IedFinder::getComponentesTurma(
            serieId: $this->ref_cod_serie,
            escola: $this->ref_cod_escola,
            turma: $this->ref_cod_turma
        );

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        $this->campoLista(
            nome: 'ref_cod_disciplina',
            campo: 'Disciplina',
            valor: $opcoes,
            default: $this->ref_cod_disciplina,
            obrigatorio: false
        );

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_dispensa_disciplina = new clsPmieducarDispensaDisciplina();
        $obj_dispensa_disciplina->setOrderby(strNomeCampo: 'data_cadastro ASC');
        $obj_dispensa_disciplina->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_dispensa_disciplina->lista(
            int_ref_cod_matricula: $this->ref_cod_matricula,
            int_ref_cod_disciplina: $this->ref_cod_disciplina,
            int_ref_cod_tipo_dispensa: $this->ref_cod_tipo_dispensa,
            int_ativo: 1
        );

        $total = $obj_dispensa_disciplina->_total;

        // Mapper de componente curricular
        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(datetime: substr(string: $registro['data_cadastro'], offset: 0, length: 16));
                $registro['data_cadastro_br'] = date(format: 'd/m/Y', timestamp: $registro['data_cadastro_time']);

                // Tipo da dispensa
                $det_ref_cod_tipo_dispensa = LegacyExemptionType::find($registro['ref_cod_tipo_dispensa'])?->getAttributes();
                $registro['ref_cod_tipo_dispensa'] = $det_ref_cod_tipo_dispensa['nm_tipo'];

                // Componente curricular
                $componente = $componenteMapper->find(pkey: $registro['ref_cod_disciplina']);

                // Dados para a url
                $url = 'educar_dispensa_disciplina_det.php';
                $options = ['query' => [
                    'ref_cod_matricula' => $registro['ref_cod_matricula'],
                    'ref_cod_serie' => $registro['ref_cod_serie'],
                    'ref_cod_escola' => $registro['ref_cod_escola'],
                    'ref_cod_disciplina' => $registro['ref_cod_disciplina'],
                ]];

                $this->addLinhas(linha: [
                    $urlHelper->l(text: $componente->nome, path: $url, options: $options),
                    $urlHelper->l(text: $registro['ref_cod_tipo_dispensa'], path: $url, options: $options),
                    $urlHelper->l(text: $registro['data_cadastro_br'], path: $url, options: $options),
                ]);
            }
        }

        $this->addPaginador2(
            strUrl: 'educar_dispensa_disciplina_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7) && $det_matricula['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
            $this->array_botao_url[] = 'educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
        }

        $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Dispensa de componentes curriculares', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Dispensa Componente Curricular';
        $this->processoAp = 628;
    }
};
