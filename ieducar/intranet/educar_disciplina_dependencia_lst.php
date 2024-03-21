<?php

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

    public $observacao;

    public $ref_sequencial;

    public $ref_cod_instituicao;

    public $ref_cod_turma;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 682);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Gerar()
    {
        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

        $this->titulo = 'Disciplina de dependência - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        if (!$_GET['ref_cod_matricula']) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);

        if (is_array($lst_matricula)) {
            $det_matricula = array_shift($lst_matricula);
            $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

            $obj_matricula_turma = new clsPmieducarMatriculaTurma();
            $lst_matricula_turma = $obj_matricula_turma->lista(
                $this->ref_cod_matricula,
                int_ativo: 1,
                int_ref_cod_serie: $this->ref_cod_serie,
                int_ref_cod_escola: $this->ref_cod_escola
            );

            if (is_array($lst_matricula_turma)) {
                $det = array_shift($lst_matricula_turma);
                $this->ref_cod_turma = $det['ref_cod_turma'];
                $this->ref_sequencial = $det['sequencial'];
            }
        }

        $this->campoOculto(nome: 'ref_cod_turma', valor: $this->ref_cod_turma);

        $this->addCabecalhos([
            'Disciplina',
        ]);

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

        $obj_disciplina_dependencia = new clsPmieducarDisciplinaDependencia();
        $obj_disciplina_dependencia->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_disciplina_dependencia->lista(
            int_ref_cod_matricula: $this->ref_cod_matricula,
            int_ref_cod_disciplina: $this->ref_cod_disciplina
        );

        $total = $obj_disciplina_dependencia->_total;

        // Mapper de componente curricular
        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {

                $componente = $componenteMapper->find($registro['ref_cod_disciplina']);
                // Dados para a url
                $url = 'educar_disciplina_dependencia_det.php';
                $options = ['query' => [
                    'ref_cod_matricula' => $registro['ref_cod_matricula'],
                    'ref_cod_serie' => $registro['ref_cod_serie'],
                    'ref_cod_escola' => $registro['ref_cod_escola'],
                    'ref_cod_disciplina' => $registro['ref_cod_disciplina'],
                ]];

                $this->addLinhas([
                    $urlHelper->l(text: $componente->nome, path: $url, options: $options),
                ]);
            }
        }

        $this->addPaginador2(
            strUrl: 'educar_disciplina_dependencia_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao_url[] = 'educar_disciplina_dependencia_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
        }

        $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Disciplinas de dependência', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Disciplina de dependência';
        $this->processoAp = 578;
    }
};
