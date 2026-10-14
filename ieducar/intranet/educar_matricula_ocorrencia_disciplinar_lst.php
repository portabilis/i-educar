<?php

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use App\Models\LegacyStudent;

return new class extends clsListagem
{
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

    public $ref_cod_matricula;

    public $ref_cod_tipo_ocorrencia_disciplinar;

    public $sequencial;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $observacao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_ref_cod_serie;

    public $ref_cod_turma;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', 681);

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
        $this->titulo = 'Ocorrências disciplinares - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        if (!$this->ref_cod_matricula) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

        $this->addCabecalhos([
            'Tipo de Ocorrência Disciplinar',
            'Série ',
            'Turma',
        ]);

        $matricula = (new clsPmieducarMatricula)->lista($this->ref_cod_matricula);
        $detalhe_aluno = array_shift($matricula);

        (new clsPmieducarEscola($detalhe_aluno['ref_ref_cod_escola']))->detalhe();
        $nm_aluno = LegacyStudent::query()
            ->active()
            ->whereStudent($detalhe_aluno['ref_cod_aluno'])
            ->joinPerson()
            ->value('pessoa.nome');

        $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $nm_aluno);

        $opcoes = LegacyDisciplinaryOccurrenceType::query()
            ->where('ativo', 1)
            ->orderBy('nm_tipo', 'ASC')
            ->pluck('nm_tipo', 'cod_tipo_ocorrencia_disciplinar')
            ->prepend('Selecione', '');

        $this->campoLista(
            'ref_cod_tipo_ocorrencia_disciplinar',
            'Tipo Ocorrência Disciplinar',
            $opcoes,
            $this->ref_cod_tipo_ocorrencia_disciplinar,
            '',
            false,
            '',
            '',
            false,
            false
        );

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $query = LegacyRegistrationDisciplinaryOccurrenceType::query()
            ->where('ativo', 1)
            ->orderBy('cod_ocorrencia_disciplinar', 'DESC');

        if (is_numeric($this->ref_cod_matricula)) {
            $query->where('ref_cod_matricula', $this->ref_cod_matricula);
        }

        if (is_numeric($this->ref_cod_tipo_ocorrencia_disciplinar)) {
            $query->where('ref_cod_tipo_ocorrencia_disciplinar', $this->ref_cod_tipo_ocorrencia_disciplinar);
        }

        $result = $query->paginate($this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        $nomesSeriesPorMatricula = LegacyRegistration::query()
            ->join('pmieducar.serie', 'serie.cod_serie', 'matricula.ref_ref_cod_serie')
            ->whereIn('cod_matricula', array_filter(array_column(is_array($lista) ? $lista : [], 'ref_cod_matricula'), 'is_numeric'))
            ->pluck('serie.nm_serie', 'matricula.cod_matricula');

        $turmasPorMatricula = LegacyEnrollment::query()
            ->active()
            ->whereIn('ref_cod_matricula', array_filter(array_column($lista, 'ref_cod_matricula'), 'is_numeric'))
            ->whereHas('registration', function ($q) {
                $q->whereHas('student', fn ($q) => $q->active());
            })
            ->with('schoolClass:cod_turma,nm_turma')
            ->get([
                'ref_cod_matricula',
                'ref_cod_turma',
            ])
            ->groupBy('ref_cod_matricula');

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $registro['ref_ref_cod_serie'] = $nomesSeriesPorMatricula[$registro['ref_cod_matricula']] ?? null;

                $det_ref_cod_tipo_ocorrencia_disciplinar = LegacyDisciplinaryOccurrenceType::find($registro['ref_cod_tipo_ocorrencia_disciplinar'])?->getAttributes();
                $registro['nm_tipo'] = $det_ref_cod_tipo_ocorrencia_disciplinar['nm_tipo'];

                $nomeTurma = $turmasPorMatricula->get($registro['ref_cod_matricula'])?->first()?->schoolClass->nm_turma;

                $this->addLinhas([
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}\">{$registro['ref_ref_cod_serie']}</a>",
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}\">{$nomeTurma}</a>",
                ]);
            }
        }
        $this->addPaginador2('educar_matricula_ocorrencia_disciplinar_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes;

        $this->array_botao = [];
        $this->array_botao_url = [];
        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            $this->array_botao_url[] = "educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula={$this->ref_cod_matricula}";
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
        }

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        $this->largura = '100%';

        // Início > Escola > Alunos > Nome > Matrícula > Ocorrências disciplinares
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->breadcrumb('Ocorrências disciplinares', [
            url('intranet/educar_index.php') => 'Escola',
            url('intranet/educar_aluno_lst.php') => 'Alunos',
            url("intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url("intranet/educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}") => 'Matrícula',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Ocorrências disciplinares';
        $this->processoAp = '578';
    }
};
