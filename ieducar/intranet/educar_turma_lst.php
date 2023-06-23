<?php

use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_turma;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_ref_cod_serie;

    public $ref_ref_cod_escola;

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
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Ano',
            'Turma',
            'Turno',
            'Série',
            'Curso',
            'Escola',
            'Situação',
        ];

        $this->addCabecalhos(coluna: $lista_busca);

        if ($this->ref_cod_escola) {
            $this->ref_ref_cod_escola = $this->ref_cod_escola;
        }

        if (!isset($_GET['busca'])) {
            $this->ano = date(format: 'Y');
        }

        $this->inputsHelper()->dynamic(helperNames: 'ano', inputOptions: ['ano' => $this->ano]);
        $obrigatorio = false;
        $get_escola = true;
        $get_curso = true;
        $get_serie = false;
        $get_escola_serie = true;
        $get_select_name_full = true;
        $get_ano = $this->ano;

        include 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola_) {
            $this->ref_cod_escola = $this->ref_cod_escola_;
        }

        if ($this->ref_cod_serie_) {
            $this->ref_cod_serie = $this->ref_cod_serie_;
        }

        $opcoes_serie = ['' => 'Selecione uma série'];

        // Editar
        if ($this->ref_cod_curso) {
            $series = LegacyGrade::where(column: 'ativo', operator: 1)->where(column: 'ref_cod_curso', operator: $this->ref_cod_curso)->orderBy('nm_serie')->get(columns: ['nm_serie', 'cod_serie']);

            foreach ($series as $serie) {
                $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
            }
        }

        $this->campoLista(
            nome: 'ref_cod_serie',
            campo: 'Série',
            valor: $opcoes_serie,
            default: $this->ref_cod_serie,
            obrigatorio: false
        );

        $this->campoTexto(nome: 'nm_turma', campo: 'Turma', valor: $this->nm_turma, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoLista(nome: 'visivel', campo: 'Situação', valor: ['' => 'Selecione', '1' => 'Ativo', '2' => 'Inativo'], default: $this->visivel, acao: null, duplo: null, descricao: null, complemento: null, desabilitado: null, obrigatorio: false);
        $this->inputsHelper()->turmaTurno(inputOptions: ['required' => false, 'label' => 'Turno']);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_turma = new clsPmieducarTurma();
        $obj_turma->setOrderby(strNomeCampo: 'nm_turma ASC');
        $obj_turma->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        if ($this->visivel == 1) {
            $visivel = true;
        } elseif ($this->visivel == 2) {
            $visivel = false;
        } else {
            $visivel = null;
        }

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
            $obj_turma->codUsuario = $this->pessoa_logada;
        }

        $lista = LegacySchoolClass::query()
            ->filter(data: [
                'grade' => $this->ref_cod_serie,
                'school' => $this->ref_cod_escola,
                'school_user' => $obj_turma->codUsuario,
                'name' => $this->nm_turma,
                'course' => $this->ref_cod_curso,
                'institution' => $this->ref_cod_instituicao,
                'shift' => $this->turma_turno_id,
                'visible' => $visivel,
                'year_eq' => $this->ano,
            ])
            ->with(relations: [
                'school' => fn ($q) => $q->select('cod_escola', 'ref_idpes')->with('organization:idpes,fantasia'),
                'course:cod_curso,nm_curso',
                'grades' => fn ($q) => $q->select('cod_serie', 'nm_serie', 'ref_cod_curso')->with('course:cod_curso,nm_curso')->orderBy('nm_serie'),
                'grade:cod_serie,nm_serie',
                'period:id,nome',
            ])
            ->active()
            ->orderBy(column: 'nm_turma')
            ->paginate(perPage: $this->limite, columns: ['cod_turma', 'ano', 'nm_turma', 'ref_ref_cod_escola', 'turma_turno_id', 'ref_ref_cod_serie', 'ref_cod_curso', 'visivel', 'multiseriada'], pageName: 'pagina_' . $this->nome);

        // monta a lista
        if ($lista->isNotEmpty()) {
            foreach ($lista as $registro) {
                $nm_escola = $registro->school->name;
                $nm_serie = $registro->multiseriada ? $registro->grades->unique()->implode('name', '<br>') : $registro->grade->name;
                $nm_curso = $registro->multiseriada ? $registro->grades->unique('course.name')->implode('course.name', '<br>') : $registro->course->name;

                $lista_busca = [
                    "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$registro->year}</a>",
                    "<a href=\"educar_turma_det.php?cod_turma={$registro->id}\">{$registro->nm_turma}</a>",
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
                $this->addLinhas(linha: $lista_busca);
            }
        }

        $this->addPaginador2(strUrl: 'educar_turma_lst.php', intTotalRegistros: $lista->total(), mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 586, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_turma_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de turmas', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(filename: public_path(path: '/vendor/legacy/Cadastro/Assets/Javascripts/EscolaSerie.js'));
    }

    public function Formular()
    {
        $this->title = 'Turma';
        $this->processoAp = '586';
    }
};
