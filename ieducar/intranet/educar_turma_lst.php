<?php

use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_ref_cod_serie;
    public $ref_ref_cod_escola;
    public $ref_cod_infra_predio_comodo;
    public $nm_turma;
    public $sgl_turma;
    public $max_aluno;
    public $multiseriada;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_turma_tipo;
    public $hora_inicial;
    public $hora_final;
    public $hora_inicio_intervalo;
    public $hora_fim_intervalo;
    public $ref_cod_instituicao;
    public $ref_cod_curso;
    public $ref_cod_escola;
    public $visivel;
    public $ano;
    public $ref_cod_serie;

    public function Gerar()
    {
        $this->titulo = 'Turma - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Ano',
            'Turma',
            'Turno',
            'Série',
            'Curso',
            'Escola',
            'Situação'
        ];

        $this->addCabecalhos($lista_busca);

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        if (!isset($_GET['busca'])) {
            $this->ano = date('Y');
        }

        $this->inputsHelper()->dynamic('ano', ['ano' => $this->ano]);
        $obrigatorio = false;
        $get_escola = true;
        $get_curso = true;
        $get_serie = false;
        $get_escola_serie = true;
        $get_select_name_full = true;

        include_once 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola_) {
            $this->ref_cod_escola = $this->ref_cod_escola_;
        }

        if ($this->ref_cod_serie_) {
            $this->ref_cod_serie = $this->ref_cod_serie_;
        }

        $opcoes_serie = ['' => 'Selecione uma série'];

        // Editar
        if ($this->ref_cod_curso) {
            $series = LegacyGrade::where('ativo',1)->where('ref_cod_curso',$this->ref_cod_curso)->orderBy('nm_serie')->get(['nm_serie','cod_serie']);

            foreach ($series as $serie) {
                $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
            }
        }

        $this->campoLista(
            'ref_cod_serie',
            'Série',
            $opcoes_serie,
            $this->ref_cod_serie,
            obrigatorio: false
        );

        $this->campoTexto('nm_turma', 'Turma', $this->nm_turma, 30, 255);
        $this->campoLista('visivel', 'Situação', ['' => 'Selecione', '1' => 'Ativo', '2' => 'Inativo'], $this->visivel, null, null, null, null, null, false);
        $this->inputsHelper()->turmaTurno(['required' => false, 'label' => 'Turno']);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby('nm_turma ASC');
        $obj_turma->setLimite($this->limite, $this->offset);

        if ($this->visivel == 1) {
            $visivel = true;
        } elseif ($this->visivel == 2) {
            $visivel = false;
        } else {
            $visivel = null;
        }

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_turma->codUsuario = $this->pessoa_logada;
        }

        $lista = LegacySchoolClass::query()
            ->filter([
                'grade' => $this->ref_cod_serie,
                'school' => $this->ref_cod_escola,
                'school_user' => $obj_turma->codUsuario,
                'name' => $this->nm_turma,
                'course' => $this->ref_cod_curso,
                'institution' => $this->ref_cod_instituicao,
                'shift' => $this->turma_turno_id,
                'visible' => $visivel,
                'year_eq' => $this->ano
            ])
            ->with([
                'school' => fn($q)=>$q->select('cod_escola','ref_idpes')->with('organization:idpes,fantasia'),
                'course:cod_curso,nm_curso',
                'grades'=> fn($q)=>$q->select('cod_serie','nm_serie','ref_cod_curso')->with('course:cod_curso,nm_curso')->orderBy('nm_serie'),
                'grade:cod_serie,nm_serie',
                'period:id,nome'
            ])
            ->active()
            ->orderBy('nm_turma')
            ->paginate($this->limite, ['cod_turma', 'ano','nm_turma','ref_ref_cod_escola','turma_turno_id','ref_ref_cod_serie','ref_cod_curso','visivel','multiseriada'], 'pagina_' . $this->nome);

        // monta a lista
        if ($lista->isNotEmpty()) {
            foreach ($lista as $registro) {
                $nm_escola = $registro->school->name;
                $nm_serie = $registro->multiseriada ? $registro->grades->unique()->implode('name','<br>') : $registro->grade->name;
                $nm_curso = $registro->multiseriada ? $registro->grades->unique('course.name')->implode('course.name','<br>') : $registro->course->name;

                $lista_busca = [
                    "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$registro->year}</a>",
                    "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$registro->nm_turma}</a>"
                ];

                if ($registro->turma_turno_id) {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$registro->period->name}</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\"></a>";
                }

                if ($nm_serie) {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$nm_serie}</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">-</a>";
                }

                $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$nm_curso}</a>";

                if ($nm_escola) {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$nm_escola}</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">-</a>";
                }

                if ($registro->visible) {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">Ativo</a>";
                } else {
                    $lista_busca[] = "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">Inativo</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_turma_lst.php', $lista->total(), $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(586, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_turma_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Listagem de turmas', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(public_path('/vendor/legacy/Cadastro/Assets/Javascripts/EscolaSerie.js'));
    }

    public function Formular()
    {
        $this->title = 'Turma';
        $this->processoAp = '586';
    }
};
