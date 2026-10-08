<?php

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyEnrollment;
use App\Models\LegacyExemptionType;
use App\Models\LegacyRegistration;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

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

            // Inalcançável: o `simpleRedirect()` acima interrompe a execução.
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
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

        $obj_matricula = new clsPmieducarMatricula;
        $lst_matricula = $obj_matricula->lista(int_cod_matricula: $this->ref_cod_matricula);

        if (is_array(value: $lst_matricula)) {
            $det_matricula = array_shift(array: $lst_matricula);
            $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
            $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

            $enturmacao = LegacyEnrollment::query()
                ->active()
                ->when(is_numeric($this->ref_cod_matricula), fn ($q) => $q->whereRegistration($this->ref_cod_matricula))
                ->whereHas('registration', function ($q) {
                    $q->whereHas('student', fn ($q) => $q->active());
                    $q->when(is_numeric($this->ref_cod_serie), fn ($q) => $q->whereGrade($this->ref_cod_serie));
                    $q->when(is_numeric($this->ref_cod_escola), fn ($q) => $q->whereSchool($this->ref_cod_escola));
                })
                ->first(['ref_cod_turma', 'sequencial']);

            if ($enturmacao) {
                $this->ref_cod_turma = $enturmacao->ref_cod_turma;
                $this->ref_sequencial = $enturmacao->sequencial;
            } else {
                $this->mensagem .= 'Matrícula sem enturmações ativas.<br>';
                $this->simpleRedirect(url: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
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

        $resultado = LegacyDisciplineExemption::query()
            ->with(['type:cod_tipo_dispensa,nm_tipo', 'discipline:id,nome'])
            ->when(is_numeric($this->ref_cod_matricula), function ($query) {
                $query->whereRegistration($this->ref_cod_matricula);
            })
            ->when(is_numeric($this->ref_cod_disciplina), function ($query) {
                $query->whereDiscipline($this->ref_cod_disciplina);
            })
            ->when(is_numeric($this->ref_cod_tipo_dispensa), function ($query) {
                $query->whereExemptionType($this->ref_cod_tipo_dispensa);
            })
            ->orderBy('data_cadastro')
            ->orderBy('cod_dispensa')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $total = $resultado->total();
        $dispensas = $resultado->getCollection();

        foreach ($dispensas as $registro) {
            $url = 'educar_dispensa_disciplina_det.php';
            $options = ['query' => [
                'ref_cod_matricula' => $registro['ref_cod_matricula'],
                'ref_cod_serie' => $registro['ref_cod_serie'],
                'ref_cod_escola' => $registro['ref_cod_escola'],
                'ref_cod_disciplina' => $registro['ref_cod_disciplina'],
            ]];

            $this->addLinhas(linha: [
                $urlHelper->l(text: $registro->discipline->nome, path: $url, options: $options),
                $urlHelper->l(text: $registro->type->nm_tipo, path: $url, options: $options),
                $urlHelper->l(text: $registro['data_cadastro']->format('d/m/Y'), path: $url, options: $options),
            ]);
        }

        $this->addPaginador2(
            strUrl: 'educar_dispensa_disciplina_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissoes = new clsPermissoes;

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

        // Início > Escola > Alunos > Nome > Matrícula > Dispensa de componentes curriculares
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->breadcrumb(currentPage: 'Dispensa de componentes curriculares', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
            url(path: 'intranet/educar_aluno_lst.php') => 'Alunos',
            url(path: "intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url(path: "intranet/educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}") => 'Matrícula',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Dispensa Componente Curricular';
        $this->processoAp = 628;
    }
};
