<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassType;
use App\Models\LegacySchoolCourse;
use App\Models\LegacyStageType;
use App\Services\SchoolClass\SchoolClassService;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;
use iEducar\Modules\SchoolClass\Period;
use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $cod_turma;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_serie;

    public $ref_cod_serie_;

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

    public $ref_cod_escola_;

    public $padrao_ano_escolar = 1;

    public $ref_cod_regente;

    public $ref_cod_instituicao_regente;

    public $turma_modulo = [];

    public $incluir_modulo;

    public $excluir_modulo;

    public $visivel;

    public $tipo_atendimento;

    public $atividades_complementares;

    public $cod_curso_profissional;

    public $etapa_educacenso;

    public $formas_organizacao_turma;

    public $ref_cod_disciplina_dispensada;

    public $codigo_inep_educacenso;

    public $estrutura_curricular;

    public $tipo_mediacao_didatico_pedagogico;

    public $unidade_curricular;

    public $dias_semana;

    public $tipo_boletim;

    public $tipo_boletim_diferenciado;

    public $sequencial;

    public $ref_cod_modulo;

    public $data_inicio;

    public $data_fim;

    public $dias_letivos;

    public $etapas_especificas;

    public $etapas_utilizadas;

    public $local_funcionamento_diferenciado;

    public $definirComponentePorEtapa;

    public $modulos = [];

    public $retorno;

    public $dias_da_semana = [
        '' => 'Selecione',
        1 => 'Domingo',
        2 => 'Segunda',
        3 => 'Terça',
        4 => 'Quarta',
        5 => 'Quinta',
        6 => 'Sexta',
        7 => 'Sábado',
    ];

    public $nao_informar_educacenso;

    public $ano_letivo;

    public $nome_url_cancelar = 'Cancelar';

    public $url_cancelar = 'educar_turma_lst.php';

    public $ano;

    public $outras_unidades_curriculares_obrigatorias;

    public $classe_com_lingua_brasileira_sinais;

    public $horario_funcionamento_turno_matutino;

    public $codigo_inep_matutino;

    public $hora_inicial_matutino;

    public $hora_inicio_intervalo_matutino;

    public $hora_fim_intervalo_matutino;

    public $hora_final_matutino;

    public $horario_funcionamento_turma_vespertino;

    public $codigo_inep_vespertino;

    public $hora_inicial_vespertino;

    public $hora_inicio_intervalo_vespertino;

    public $hora_fim_intervalo_vespertino;

    public $hora_final_vespertino;

    private $hasStudentsPartials;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma = $_GET['cod_turma'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 586, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_turma_lst.php');

        //Define que esta tela executa suas ações atraves de requisições ajax
        $this->acao_executa_submit_ajax = true;

        if (is_numeric(value: $this->cod_turma)) {
            $obj_turma = new clsPmieducarTurma(cod_turma: $this->cod_turma);
            $registro = $obj_turma->detalhe();
            $obj_esc = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
            $det_esc = $obj_esc->detalhe();
            $obj_ser = new clsPmieducarSerie(cod_serie: $registro['ref_ref_cod_serie']);
            $det_ser = $obj_ser->detalhe();

            $this->visivel = (int) $registro['visivel'];

            $regra_avaliacao_id = $det_ser['regra_avaliacao_id'];
            if ($regra_avaliacao_id) {
                $regra_avaliacao_mapper = new RegraAvaliacao_Model_RegraDataMapper();
                $regra_avaliacao = $regra_avaliacao_mapper->find(pkey: $regra_avaliacao_id);

                $this->definirComponentePorEtapa = ($regra_avaliacao->definirComponentePorEtapa == 1);
            }

            $this->dependencia_administrativa = $det_esc['dependencia_administrativa'];
            $this->ref_cod_escola = $det_esc['cod_escola'];
            $this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
            $this->ref_cod_curso = $det_ser['ref_cod_curso'];
            $this->ref_cod_serie = $det_ser['cod_serie'];

            $obj_curso = new clsPmieducarCurso(cod_curso: ($this->ref_cod_curso));
            $det_curso = $obj_curso->detalhe();
            $this->padrao_ano_escolar = $det_curso['padrao_ano_escolar'];
            $this->modalidade_curso = $det_curso['modalidade_curso'];

            $inep = $obj_turma->getInep();

            if ($inep) {
                $this->codigo_inep_educacenso = $inep;
            }

            $service = new SchoolClassService();
            $this->hasStudentsPartials = $service->hasStudentsPartials($this->cod_turma);

            if ($this->hasStudentsPartials) {
                $this->codigo_inep_matutino = $obj_turma->getInepTurno(Period::MORNING);
                $this->codigo_inep_vespertino = $obj_turma->getInepTurno(Period::AFTERNOON);
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $objTurma = new clsPmieducarTurma(cod_turma: $this->cod_turma);
                $possuiAlunosVinculados = $objTurma->possuiAlunosVinculados();

                if ($possuiAlunosVinculados) {
                    $this->script_excluir = 'excluir_turma_com_matriculas();';
                } elseif ($this->acao_executa_submit_ajax) {
                    $this->script_excluir = 'excluirAjax();';
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    int_processo_ap: 586,
                    int_idpes_usuario: $this->pessoa_logada,
                    int_soma_nivel_acesso: 7,
                    str_pagina_redirecionar: 'educar_turma_lst.php'
                );

                $retorno = 'Editar';
            }
        }

        $this->dias_semana = transformStringFromDBInArray(string: $this->dias_semana);
        $this->atividades_complementares = transformStringFromDBInArray(string: $this->atividades_complementares);
        $this->estrutura_curricular = transformStringFromDBInArray(string: $this->estrutura_curricular);
        $this->cod_curso_profissional = transformStringFromDBInArray(string: $this->cod_curso_profissional);
        $this->unidade_curricular = transformStringFromDBInArray(string: $this->unidade_curricular);

        $this->url_cancelar = $retorno == 'Editar' ?
            'educar_turma_det.php?cod_turma=' . $registro['cod_turma'] : 'educar_turma_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->retorno = $retorno;

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = $this->$campo ? $this->$campo : $val;
            }
        }

        if (is_numeric(value: $this->cod_turma)) {
            if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar(codUsuario: $this->pessoa_logada)) {
                $not_access = LegacySchoolClass::filter(['school_user' => $this->pessoa_logada])->where(column: 'cod_turma', operator: $this->cod_turma)->doesntExist();
                if ($not_access) {
                    $this->simpleRedirect(url: 'educar_turma_lst.php');
                }
            }

            $obj_turma = new clsPmieducarTurma(cod_turma: $this->cod_turma);
            $registro = $obj_turma->detalhe();
            $obj_esc = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
            $det_esc = $obj_esc->detalhe();
            $obj_ser = new clsPmieducarSerie(cod_serie: $registro['ref_ref_cod_serie']);
            $det_ser = $obj_ser->detalhe();

            $this->ref_cod_escola = $det_esc['cod_escola'];
            $this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
            $this->ref_cod_curso = $det_ser['ref_cod_curso'];
            $this->ref_cod_serie = $det_ser['cod_serie'];
            $this->ano = $registro['ano'];
        }

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();

        $this->campoOculto(nome: 'obrigar_campos_censo', valor: (int) $obrigarCamposCenso);
        $this->campoOculto(nome: 'cod_turma', valor: $this->cod_turma);
        $this->campoOculto(nome: 'ref_cod_escola_', valor: $this->ref_cod_escola);
        $this->campoOculto(nome: 'ref_cod_curso_', valor: $this->ref_cod_curso);
        $this->campoOculto(nome: 'ref_cod_serie_', valor: $this->ref_cod_serie);
        $this->campoOculto(nome: 'ano_letivo', valor: (is_null(value: $this->ano) ? date(format: 'Y') : $this->ano));
        $this->campoOculto(nome: 'dependencia_administrativa', valor: $this->dependencia_administrativa);
        $this->campoOculto(nome: 'modalidade_curso', valor: $this->modalidade_curso);
        $this->campoOculto(nome: 'retorno', valor: $this->retorno);

        $bloqueia = false;
        if (!isset($this->cod_turma)) {
            $bloqueia = false;
        } elseif (is_numeric(value: $this->cod_turma)) {
            $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
            $obj_matriculas_turma->setOrderby(strNomeCampo: 'nome_aluno');
            $lst_matriculas_turma = $obj_matriculas_turma->lista(
                int_ref_cod_turma: $this->cod_turma,
                int_ativo: 1,
                aprovado: [1, 2, 3],
                bool_get_nome_aluno: true,
                int_ultima_matricula: 1,
                bool_matricula_ativo: true
            );

            if (is_array(value: $lst_matriculas_turma) && count(value: $lst_matriculas_turma) > 0) {
                $bloqueia = true;
            }
        }

        $desabilitado = $bloqueia;

        $this->inputsHelper()->dynamic(helperNames: 'ano', inputOptions: ['value' => (is_null(value: $this->ano) ? date(format: 'Y') : $this->ano), 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: ['value' => $this->ref_cod_escola, 'disabled' => $desabilitado]);

        $multiseriada = $this->multiseriada ?? 0;
        $this->campoCheck(nome: 'multiseriada', campo: 'Multisseriada', valor: $multiseriada);

        $opcoesCursos = [
            null => 'Selecione um curso',
        ];

        if ($this->ref_ref_cod_escola) {
            $cursosDaEscola = LegacySchoolCourse::query()
                ->with(relations: 'course')
                ->where(column: 'ref_cod_escola', operator: $this->ref_ref_cod_escola)
                ->get()
                ->pluck(value: 'course.nm_curso', key: 'ref_cod_curso')
                ->toArray();
            $opcoesCursos = array_replace($opcoesCursos, $cursosDaEscola);
        }

        $this->inputsHelper()->dynamic(helperNames: ['curso', 'serie'], inputOptions: ['disabled' => $desabilitado, 'ano' => $this->ano]);

        $tiposBoletim = Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
        asort(array: $tiposBoletim);
        $tiposBoletim = Portabilis_Array_Utils::insertIn(key: null, value: 'Selecione um modelo', array: $tiposBoletim);

        $this->campoTabelaInicio(nome: 'turma_serie', titulo: 'Séries da turma', arr_campos: ['Curso', 'Série', 'Boletim', 'Boletim diferenciado'], arr_valores: $this->turma_serie);
        $this->campoLista(nome: 'mult_curso_id', campo: 'Curso', valor: $opcoesCursos, default: $this->mult_curso_id, acao: 'atualizaInformacoesComBaseNoCurso(this)');
        $this->campoLista(nome: 'mult_serie_id', campo: 'Série', valor: ['Selecione uma série'], default: $this->mult_serie_id, acao: 'atualizaInformacoesComBaseNaSerie()');
        $this->campoLista(nome: 'mult_boletim_id', campo: 'Boletim', valor: $tiposBoletim, default: $this->mult_boletim_id);
        $this->campoLista(nome: 'mult_boletim_diferenciado_id', campo: 'Boletim diferenciado', valor: $tiposBoletim, default: $this->mult_boletim_diferenciado_id, acao: null, duplo: null, descricao: null, complemento: null, desabilitado: null, obrigatorio: false);
        $this->campoOculto(nome: 'mult_padrao_ano_escolar', valor: $this->mult_padrao_ano_escolar);
        $this->campoTabelaFim();

        $array_servidor = ['' => 'Selecione um servidor'];
        if ($this->ref_cod_regente) {
            $obj_pessoa = new clsPessoa_(int_idpes: $this->ref_cod_regente);
            $det = $obj_pessoa->detalhe();
            $array_servidor[$this->ref_cod_regente] = $det['nome'];
        }

        $this->campoListaPesq(nome: 'ref_cod_regente', campo: 'Professor/Regente', valor: $array_servidor, default: $this->ref_cod_regente, div: true);

        // Turma tipo
        $query = LegacySchoolClassType::query()->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC');
        if (is_numeric(value: $this->ref_cod_instituicao)) {
            $query->where(column: 'ref_cod_instituicao', operator: $this->ref_cod_instituicao);
        }
        $opcoes = $query->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_turma_tipo')
            ->prepend(value: 'Selecione', key: '');

        $script = 'javascript:showExpansivelIframe(520, 170, \'educar_turma_tipo_cad_pop.php\');';

        $script = sprintf(
            '<div id=\'img_turma\' border=\'0\' onclick=\'%s\'>',
            $script
        );

        $this->campoLista(
            nome: 'ref_cod_turma_tipo',
            campo: 'Tipo de turma',
            valor: $opcoes,
            default: $this->ref_cod_turma_tipo,
            complemento: $script
        );

        $this->campoTexto(nome: 'nm_turma', campo: 'Nome da turma', valor: e(value: $this->nm_turma), tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);

        $this->campoTexto(nome: 'sgl_turma', campo: _cl(key: 'turma.detalhe.sigla'), valor: $this->sgl_turma, tamanhovisivel: 15, tamanhomaximo: 15);

        $this->campoNumero(nome: 'max_aluno', campo: 'Máximo de Alunos', valor: $this->max_aluno, tamanhovisivel: 3, tamanhomaximo: 3, obrigatorio: true);

        unset($opcoes);
        if (!is_null(value: $this->ref_cod_serie)) {
            $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
            $opcaoPadrao = [null => 'Selecione'];
            $listaComponentes = $anoEscolar->findComponentePorSerie(serieId: $this->ref_cod_serie);
            if (!empty($listaComponentes)) {
                foreach ($listaComponentes as $componente) {
                    $componente->nome = ucwords(string: strtolower(string: $componente->nome));
                    $opcoes["{$componente->id}"] = "{$componente->nome}";
                }
                $opcoes = $opcaoPadrao + $opcoes;
                $this->campoLista(nome: 'ref_cod_disciplina_dispensada', campo: 'Disciplina dispensada', valor: $opcoes, default: $this->ref_cod_disciplina_dispensada, obrigatorio: false);
            }
        }

        $ativo = is_numeric(value: $this->cod_turma) ? (bool) ($this->visivel) : true;
        $this->campoCheck(nome: 'visivel', campo: 'Ativo', valor: $ativo);

        $resources = SelectOptions::tiposMediacaoDidaticoPedagogico();
        $options = ['label' => 'Tipo de mediação didático pedagógico', 'resources' => $resources, 'value' => $this->tipo_mediacao_didatico_pedagogico, 'required' => $obrigarCamposCenso, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'tipo_mediacao_didatico_pedagogico', inputOptions: $options);

        $this->campoQuebra2();

        // hora
        if ($obrigarCamposCenso && !$this->obrigaCamposHorario()) {
            $this->hora_inicial = '';
            $this->hora_final = '';
            $this->hora_inicio_intervalo = '';
            $this->hora_fim_intervalo = '';
            $this->dias_semana = [];
        }

        $this->campoRotulo(
            nome: 'horario_funcionamento_turma',
            campo: '<b>Horário de funcionamento da turma</b>'
        );

        $this->campoHora(nome: 'hora_inicial', campo: 'Hora inicial', valor: $this->hora_inicial, descricao: null, acao: null, limitaHora: null);

        $this->campoHora(
            nome: 'hora_inicio_intervalo',
            campo: 'Hora inicial do intervalo',
            valor: $this->hora_inicio_intervalo,
            descricao: null,
            acao: null,
            limitaHora: null
        );

        $this->campoHora(nome: 'hora_fim_intervalo', campo: 'Hora final do intervalo', valor: $this->hora_fim_intervalo, descricao: null, acao: null, limitaHora: null);

        $this->campoHora(nome: 'hora_final', campo: 'Hora final', valor: $this->hora_final, descricao: null, acao: null, limitaHora: null);

        $helperOptions = ['objectName' => 'dias_semana'];
        $options = ['label' => 'Dias da semana',
            'size' => 50,
            'required' => false,
            'disabled' => $obrigarCamposCenso && !$this->obrigaCamposHorario(),
            'options' => ['values' => $this->dias_semana,
                'all_values' => [1 => 'Domingo',
                    2 => 'Segunda',
                    3 => 'Terça',
                    4 => 'Quarta',
                    5 => 'Quinta',
                    6 => 'Sexta',
                    7 => 'Sábado']]];
        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $this->inputsHelper()->turmaTurno();

        $this->campoLista(nome: 'tipo_boletim', campo: 'Modelo relatório boletim', valor: $tiposBoletim, default: $this->tipo_boletim, obrigatorio: false);
        $this->campoLista(nome: 'tipo_boletim_diferenciado', campo: 'Modelo relatório boletim diferenciado', valor: $tiposBoletim, default: $this->tipo_boletim_diferenciado, obrigatorio: false);

        $this->montaListaComponentesSerieEscola();

        $lista = LegacyStageType::query()->where('ativo', 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->get()->toArray();

        $opcoesCampoModulo = [];

        if (is_array(value: $lista) && count(value: $lista)) {
            $this->modulos = $lista;
            foreach ($lista as $registro) {
                $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
            }
        }

        $registros = [];

        if (is_numeric(value: $this->cod_turma)) {
            $objTurma = new clsPmieducarTurmaModulo();
            $objTurma->setOrderBy(strNomeCampo: 'sequencial ASC');

            $registros = $objTurma->lista(int_ref_cod_turma: $this->cod_turma);
        }

        if (
            empty($registros)
            && is_numeric(value: $this->ano)
            && is_numeric(value: $this->ref_cod_escola)
        ) {
            $registros = LegacyAcademicYearStage::query()->whereSchool($this->ref_cod_escola)->whereYearEq($this->ano)->orderBySequencial()->get();
        }

        if ($this->padrao_ano_escolar != 1) {
            $qtd_registros = 0;
            $moduloSelecionado = 0;

            if ($registros) {
                $moduloSelecionado = $registros[0]['ref_cod_modulo'];

                foreach ($registros as $campo) {
                    $this->turma_modulo[$qtd_registros][] = dataFromPgToBr(data_original: $campo['data_inicio']);
                    $this->turma_modulo[$qtd_registros][] = dataFromPgToBr(data_original: $campo['data_fim']);
                    $this->turma_modulo[$qtd_registros][] = $campo['dias_letivos'];
                    $qtd_registros++;
                }
            }
        }

        $this->campoQuebra2();

        $this->campoRotulo(nome: 'etapas_cabecalho', campo: '<b>Etapas da turma</b>');

        $this->campoLista(
            nome: 'ref_cod_modulo',
            campo: 'Etapa',
            valor: $opcoesCampoModulo,
            default: $moduloSelecionado,
            acao: null,
            duplo: null,
            descricao: null,
            complemento: null,
            desabilitado: null
        );

        $this->campoTabelaInicio(nome: 'turma_modulo', titulo: 'Etapas', arr_campos: ['Data inicial', 'Data final', 'Dias Letivos'], arr_valores: $this->turma_modulo);

        $this->campoData(nome: 'data_inicio', campo: 'Data Início', valor: $this->data_inicio);
        $this->campoData(nome: 'data_fim', campo: 'Data Fim', valor: $this->data_fim);
        $this->campoTexto(nome: 'dias_letivos', campo: 'Dias Letivos', valor: $this->dias_letivos_, tamanhovisivel: 9);

        $this->campoTabelaFim();

        $this->campoOculto(nome: 'padrao_ano_escolar', valor: $this->padrao_ano_escolar);

        $this->acao_enviar = 'valida()';
        $this->acao_executa_submit = false;

        $this->inputsHelper()->integer(attrName: 'codigo_inep_educacenso', inputOptions: ['label' => 'Código INEP',
            'label_hint' => 'Somente números',
            'placeholder' => 'INEP',
            'required' => false,
            'max_length' => 14,
            'value' => $this->codigo_inep_educacenso]);

        $resources = [null => 'Selecione',
            0 => 'Escolarização',
            4 => 'Atividade complementar',
            5 => 'Atendimento educacional especializado (AEE)'];

        $options = ['label' => 'Tipo de atendimento', 'resources' => $resources, 'value' => $this->tipo_atendimento, 'required' => $obrigarCamposCenso, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'tipo_atendimento', inputOptions: $options);

        $helperOptions = ['objectName' => 'estrutura_curricular'];
        $options = [
            'label' => 'Estrutura curricular',
            'required' => false,
            'size' => 70,
            'options' => [
                'values' => $this->estrutura_curricular,
                'all_values' => [
                    1 => 'Formação geral básica',
                    2 => 'Itinerário formativo',
                    3 => 'Não se aplica',
                ],
            ],
        ];

        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $atividadesComplementares = loadJson(file: 'educacenso_json/atividades_complementares.json');
        $helperOptions = ['objectName' => 'atividades_complementares'];
        $options = ['label' => 'Tipos de atividades complementares',
            'size' => 50,
            'required' => false,
            'options' => ['values' => $this->atividades_complementares,
                'all_values' => $atividadesComplementares]];
        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $resources = Portabilis_Utils_Database::fetchPreparedQuery(sql: 'SELECT id,nome FROM modules.etapas_educacenso');
        $resources = Portabilis_Array_Utils::setAsIdValue(arrays: $resources, keyAttr: 'id', valueAtt: 'nome');
        $resources = Portabilis_Array_Utils::merge(array: $resources, defaultArray: ['null' => 'Selecione']);

        $etapas_educacenso = loadJson(file: 'educacenso_json/etapas_ensino.json');
        $etapas_educacenso = array_replace([null => 'Selecione'], $etapas_educacenso);

        $options = ['label' => 'Etapa de ensino', 'resources' => $etapas_educacenso, 'value' => $this->etapa_educacenso, 'required' => false, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'etapa_educacenso', inputOptions: $options);

        $resources = [
            null => 'Selecione',
            1 => 'Série/ano (séries anuais)',
            2 => 'Períodos semestrais',
            3 => 'Ciclo(s)',
            4 => 'Grupos não seriados com base na idade ou competência',
            5 => 'Módulos',
            6 => 'Alternância regular de períodos de estudos',
        ];

        $options = ['label' => 'Formas de organização da turma', 'resources' => $resources, 'value' => $this->formas_organizacao_turma, 'required' => false, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'formas_organizacao_turma', inputOptions: $options);

        $helperOptions = ['objectName' => 'unidade_curricular'];
        $options = [
            'label' => 'Unidade curricular',
            'required' => false,
            'size' => 70,
            'options' => [
                'values' => $this->unidade_curricular,
                'all_values' => UnidadesCurriculares::getDescriptiveValues(),
            ],
        ];

        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $options = [
            'required' => false,
            'label' => 'Outra(s) unidade(s) curricular(es) obrigatória(s)',
            'label_hint' => 'Informe outras unidades curriculares que a turma trabalha separadas por ponto e vírgula (;)',
            'value' => $this->outras_unidades_curriculares_obrigatorias,
            'cols' => 45,
            'max_length' => 500,
            'disabled' => true,
        ];

        $this->inputsHelper()->textArea('outras_unidades_curriculares_obrigatorias', $options);

        $cursos = loadJson(file: 'educacenso_json/cursos_da_educacao_profissional.json');
        $helperOptions = ['objectName' => 'cod_curso_profissional',
            'type' => 'single'];
        $options = ['label' => 'Curso de educação profissional',
            'size' => 50,
            'required' => false,
            'options' => ['values' => $this->cod_curso_profissional,
                'all_values' => $cursos]];
        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $resources = App_Model_LocalFuncionamentoDiferenciado::getInstance()->getEnums();
        $resources = array_replace([null => 'Selecione'], $resources);

        $options = ['label' => 'Local de funcionamento diferenciado da turma', 'resources' => $resources, 'value' => $this->local_funcionamento_diferenciado, 'required' => false, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'local_funcionamento_diferenciado', inputOptions: $options);

        $resources = [
            null => 'Selecione',
            1 => 'Sim',
            2 => 'Não',
        ];

        $options = ['label' => 'Classe bilíngue de surdos tendo a Libras (Língua Brasileira de Sinais) como língua de instrução, ensino, comunicação e interação e a língua portuguesa escrita como segunda língua', 'resources' => $resources, 'value' => $this->classe_com_lingua_brasileira_sinais, 'required' => $obrigarCamposCenso, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'classe_com_lingua_brasileira_sinais', inputOptions: $options);

        $options = ['label' => 'Não informar esta turma no Censo escolar',
            'value' => $this->nao_informar_educacenso,
            'label_hint' => 'Caso marcado, esta turma e suas matrículas, não serão informadas no arquivo da 1° e 2° etapa do Censo escolar'];
        $this->inputsHelper()->checkbox(attrName: 'nao_informar_educacenso', inputOptions: $options);

        $this->campoOculto(
            nome: 'turno_parcial',
            valor: $this->hasStudentsPartials ? 'S' : 'N'
        );

        $this->campoRotulo(
            nome: 'horario_funcionamento_turno_matutino',
            campo: '<b>Horário de funcionamento da turma - PERÍODO MATUTINO</b>'
        );

        $this->inputsHelper()->integer(
            attrName: 'codigo_inep_matutino',
            inputOptions: [
                'label' => 'Código INEP - Turno Matutino',
                'label_hint' => 'Somente números',
                'placeholder' => 'INEP',
                'required' => false,
                'max_length' => 14,
                'value' => $this->codigo_inep_matutino,
            ]
        );

        $this->campoHora(
            nome: 'hora_inicial_matutino',
            campo: 'Hora inicial do turno MATUTINO',
            valor: $this->hora_inicial_matutino,
        );

        $this->campoHora(
            nome: 'hora_inicio_intervalo_matutino',
            campo: 'Hora inicial do intervalo do turno MATUTINO',
            valor: $this->hora_inicio_intervalo_matutino,
        );

        $this->campoHora(
            nome: 'hora_fim_intervalo_matutino',
            campo: 'Hora final do intervalo do turno MATUTINO',
            valor: $this->hora_fim_intervalo_matutino,
        );

        $this->campoHora(
            nome: 'hora_final_matutino',
            campo: 'Hora Final do turno MATUTINO',
            valor: $this->hora_final_matutino,
        );

        $this->campoQuebra2();
        $this->campoRotulo(
            nome: 'horario_funcionamento_turma_vespertino',
            campo: '<b>Horário de funcionamento da turma - PERÍODO VESPERTINO</b>'
        );

        $this->inputsHelper()->integer(
            attrName: 'codigo_inep_vespertino',
            inputOptions: [
                'label' => 'Código INEP - Turno Vespertino',
                'label_hint' => 'Somente números',
                'placeholder' => 'INEP',
                'required' => false,
                'max_length' => 14,
                'value' => $this->codigo_inep_vespertino,
            ]
        );

        $this->campoHora(
            nome: 'hora_inicial_vespertino',
            campo: 'Hora inicial do turno VESPERTINO',
            valor: $this->hora_inicial_vespertino,
        );

        $this->campoHora(
            nome: 'hora_inicio_intervalo_vespertino',
            campo: 'Hora inicial do intervalo do turno VESPERTINO',
            valor: $this->hora_inicio_intervalo_vespertino,
        );

        $this->campoHora(
            nome: 'hora_fim_intervalo_vespertino',
            campo: 'Hora final do intervalo do turno VESPERTINO',
            valor: $this->hora_fim_intervalo_vespertino,
        );

        $this->campoHora(
            nome: 'hora_final_vespertino',
            campo: 'Hora Final do turno VESPERTINO',
            valor: $this->hora_final_vespertino,
        );

        $scripts = [
            '/vendor/legacy/Cadastro/Assets/Javascripts/Turma.js',
            '/intranet/scripts/etapas.js',
            '/intranet/scripts/tabelaSerieMult.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/Turma.css'];

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);
    }

    protected function obrigaCamposHorario()
    {
        return $this->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
    }

    protected function existeComponentesNaTurma()
    {
        if ($this->cod_turma) {
            return LegacyDisciplineSchoolClass::query()
                ->where(column: 'turma_id', operator: $this->cod_turma)
                ->exists();
        }

        return false;
    }

    public function montaListaComponentesSerieEscola()
    {
        $this->campoQuebra2();
        $existeComponentesNaTurma = $this->existeComponentesNaTurma();

        if ($this->ref_cod_serie && (!$this->multiseriada || $existeComponentesNaTurma)) {
            $conteudo = '';

            try {
                $lista = App_Model_IedFinder::getEscolaSerieDisciplina(
                    serieId: $this->ref_cod_serie,
                    escolaId: $this->ref_cod_escola,
                    ano: $this->ano
                );
            } catch (Throwable) {
                return;
            }

            // Instancia o mapper de turma
            $componenteTurmaMapper = new ComponenteCurricular_Model_TurmaDataMapper();
            $componentesTurma = [];

            if (isset($this->cod_turma) && is_numeric(value: $this->cod_turma)) {
                $componentesTurma = $componenteTurmaMapper->findAll(
                    where: ['turma' => $this->cod_turma]
                );
            }

            $componentes = [];
            foreach ($componentesTurma as $componenteTurma) {
                $componentes[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
            }
            unset($componentesTurma);

            $instituicao = new clsPmieducarInstituicao(cod_instituicao: $this->ref_cod_instituicao);
            $instituicao = $instituicao->detalhe();

            $podeCadastrarComponenteDiferenciado = dbBool(val: $instituicao['componente_curricular_turma']);

            if ($podeCadastrarComponenteDiferenciado) {
                $checkDefinirComponente = ($componentes == true);
                $disableDefinirComponente = false;
            } else {
                $disableDefinirComponente = true;
            }

            $this->campoCheck(
                nome: 'definir_componentes_diferenciados',
                campo: 'Definir componentes curriculares diferenciados',
                valor: $checkDefinirComponente,
                desc: null,
                disable: $disableDefinirComponente,
                dica: 'Está opção poderá ser utilizada, somente se no cadastro da instituição o parâmetro de permissão estiver habilitado'
            );

            $this->escola_serie_disciplina = [];

            if (is_array(value: $lista) && count(value: $lista)) {
                $conteudo .= '<tr>';
                $conteudo .= '<td  width="250"><span style="display: block; float: left; width: 250px;">Nome</span></td>';
                $conteudo .= '<td><span>Nome abreviado</span></td>';
                $conteudo .= '<td><span>Carga horária</span></td>';
                $conteudo .= '<td><span>Usar padrão do componente?</span></td>';
                if ($this->definirComponentePorEtapa) {
                    $conteudo .= '<td><span>Usar etapas específicas?</span></td>';
                    $conteudo .= '<td><span">Etapas utilizadas</span></td>';
                }
                $conteudo .= '<td><span>Possui docente vinculado?</span></td>';
                $conteudo .= '</tr>';

                foreach ($lista as $registro) {
                    $checked = '';
                    $usarComponente = false;
                    $docenteVinculado = false;
                    $checkedEtapaEspecifica = '';
                    $etapaUtilizada = '';

                    if ($componentes[$registro->id]->etapasEspecificas == '1') {
                        $checkedEtapaEspecifica = 'checked="checked"';
                        $etapaUtilizada = $componentes[$registro->id]->etapasUtilizadas;
                    }

                    if (isset($componentes[$registro->id])) {
                        $checked = 'checked="checked"';
                    }

                    if (is_null(value: $componentes[$registro->id]->cargaHoraria)) {
                        $usarComponente = true;
                    } else {
                        $cargaHoraria = $componentes[$registro->id]->cargaHoraria;
                    }
                    $cargaComponente = $registro->cargaHoraria;

                    if ($componentes[$registro->id]->docenteVinculado == 1) {
                        $docenteVinculado = true;
                    }

                    $conteudo .= '<tr class="linha-disciplina" >';
                    $conteudo .= "<td><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" class='check-disciplina' id=\"disciplinas[$registro->id]\" value=\"{$registro->id}\">{$registro}</td>";
                    $conteudo .= "<td>{$registro->abreviatura}</td>";
                    $conteudo .= "<td><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></td>";
                    $conteudo .= "<td><input type='checkbox' name='usar_componente[$registro->id]' value='1' " . ($usarComponente == true ? $checked : '') . ">($cargaComponente h)</td>";
                    if ($this->definirComponentePorEtapa) {
                        $conteudo .= "<td><input style='float:left;' type='checkbox' id='etapas_especificas[]' name='etapas_especificas[$registro->id]' value='1' " . $checkedEtapaEspecifica . '></td>';
                        $conteudo .= "<td><input type='text' class='etapas_utilizadas' name='etapas_utilizadas[$registro->id]' value='{$etapaUtilizada}' size='5' maxlength='7'></td>";
                    }
                    $conteudo .= "<td><input type='checkbox' name='docente_vinculado[$registro->id]' value='1' " . ($docenteVinculado == true ? $checked : '') . '></td>';
                    $conteudo .= '</tr>';

                    $cargaHoraria = '';
                }

                $disciplinas = '<table id="componentes_turma_cad" cellspacing="0" cellpadding="0" border="0">';
                $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
                $disciplinas .= '</table>';
            } else {
                $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
            }
        } else {
            $this->campoCheck(
                nome: 'definir_componentes_diferenciados',
                campo: 'Definir componentes curriculares diferenciados',
                valor: false,
                desc: null,
                disable: true,
                dica: 'Está opção poderá ser utilizada, somente se no cadastro da instituição o parâmetro de permissão estiver habilitado'
            );
        }

        $help = [];

        $label = 'Componentes curriculares definidos em séries da escola';

        if ($this->multiseriada && !$existeComponentesNaTurma) {
            $label = 'Os componentes curriculares de turmas multisseriadas devem ser definidos em suas respectivas Séries (Escola > Cadastros > Séries da escola)';
        }

        $label = sprintf($label, $help);

        $this->campoRotulo(
            nome: 'disciplinas_',
            campo: $label,
            valor: "<div id='disciplinas'>$disciplinas</div>"
        );
    }

    protected function getEscolaSerie($escolaId, $serieId)
    {
        $escolaSerie = new clsPmieducarEscolaSerie();
        $escolaSerie->ref_cod_escola = $escolaId;
        $escolaSerie->ref_cod_serie = $serieId;

        return $escolaSerie->detalhe();
    }

    public function gerarJsonDosModulos()
    {
        $retorno = [];

        foreach ($this->modulos as $modulo) {
            $retorno[$modulo['cod_modulo']] = [
                'label' => $modulo['nm_tipo'],
                'etapas' => (int) $modulo['num_etapas'],
            ];
        }

        return json_encode(value: $retorno);
    }

    public function makeExtra()
    {
        return str_replace(
            search: '#modulos',
            replace: $this->gerarJsonDosModulos(),
            subject: file_get_contents(filename: __DIR__ . '/scripts/extra/educar-turma-cad.js')
        );
    }

    public function Formular()
    {
        $this->title = 'Turma';
        $this->processoAp = 586;
    }
};
