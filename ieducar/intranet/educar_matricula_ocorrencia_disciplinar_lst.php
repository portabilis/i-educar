<?php

use App\Models\LegacyDisciplinaryOccurrenceType;
use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;

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

            return false;
        }
    }

    public function Gerar()
    {
        $this->titulo = 'Matricula Ocorrência Disciplinar - Listagem';

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

        $matricula = (new clsPmieducarMatricula())->lista($this->ref_cod_matricula);
        $detalhe_aluno = array_shift($matricula);

        (new clsPmieducarEscola($detalhe_aluno['ref_ref_cod_escola']))->detalhe();
        $det_aluno = (new clsPmieducarAluno())->lista($detalhe_aluno['ref_cod_aluno'], null, null, null, null, null, null, null, null, null, 1);

        $det_aluno = array_shift($det_aluno);

        $this->campoRotulo('nm_pessoa', 'Nome do Aluno', $det_aluno['nome_aluno']);

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

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_matricula = new clsPmieducarMatricula($registro['ref_cod_matricula']);
                $det_ref_cod_matricula = $obj_ref_cod_matricula->detalhe();

                $obj_serie = new clsPmieducarSerie($det_ref_cod_matricula['ref_ref_cod_serie']);
                $det_serie = $obj_serie->detalhe();
                $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

                $det_ref_cod_tipo_ocorrencia_disciplinar = LegacyDisciplinaryOccurrenceType::find($registro['ref_cod_tipo_ocorrencia_disciplinar'])?->getAttributes();
                $registro['nm_tipo'] = $det_ref_cod_tipo_ocorrencia_disciplinar['nm_tipo'];

                $obj_mat_turma = new clsPmieducarMatriculaTurma();

                $det_mat_turma = $obj_mat_turma->lista($registro['ref_cod_matricula'], null, null, null, null, null, null, null, 1);

                if ($det_mat_turma) {
                    $det_mat_turma = array_shift($det_mat_turma);
                    $obj_turma = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
                    $det_turma = $obj_turma->detalhe();
                }

                $this->addLinhas([
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}\">{$registro['ref_ref_cod_serie']}</a>",
                    "<a href=\"educar_matricula_ocorrencia_disciplinar_det.php?ref_cod_matricula={$registro['ref_cod_matricula']}&ref_cod_tipo_ocorrencia_disciplinar={$registro['ref_cod_tipo_ocorrencia_disciplinar']}&sequencial={$registro['sequencial']}\">{$det_turma['nm_turma']}</a>",
                ]);
            }
        }
        $this->addPaginador2('educar_matricula_ocorrencia_disciplinar_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

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

        $this->breadcrumb('Ocorrências disciplinares da matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Ocorrência Disciplinar';
        $this->processoAp = '578';
    }
};
