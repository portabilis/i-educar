<?php

use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacySchoolCourse;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;
use iEducar\Support\View\SelectOptions;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_serie;
    public $ref_cod_serie_;
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
        7 => 'Sábado'
    ];
    public $nao_informar_educacenso;
    public $ano_letivo;
    public $nome_url_cancelar = 'Cancelar';
    public $url_cancelar = 'educar_turma_lst.php';
    public $ano;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_turma = $_GET['cod_turma'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(586, $this->pessoa_logada, 7, 'educar_turma_lst.php');

        //Define que esta tela executa suas ações atraves de requisições ajax
        $this->acao_executa_submit_ajax = true;

        if (is_numeric($this->cod_turma)) {
            $obj_turma = new clsPmieducarTurma($this->cod_turma);
            $registro = $obj_turma->detalhe();
            $obj_esc = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
            $det_esc = $obj_esc->detalhe();
            $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
            $det_ser = $obj_ser->detalhe();

            $this->visivel = (int) $registro['visivel'];

            $regra_avaliacao_id = $det_ser['regra_avaliacao_id'];
            if ($regra_avaliacao_id) {
                $regra_avaliacao_mapper = new RegraAvaliacao_Model_RegraDataMapper();
                $regra_avaliacao = $regra_avaliacao_mapper->find($regra_avaliacao_id);

                $this->definirComponentePorEtapa = ($regra_avaliacao->definirComponentePorEtapa == 1);
            }

            $this->dependencia_administrativa = $det_esc['dependencia_administrativa'];
            $this->ref_cod_escola = $det_esc['cod_escola'];
            $this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
            $this->ref_cod_curso = $det_ser['ref_cod_curso'];
            $this->ref_cod_serie = $det_ser['cod_serie'];

            $obj_curso = new clsPmieducarCurso(($this->ref_cod_curso));
            $det_curso = $obj_curso->detalhe();
            $this->padrao_ano_escolar = $det_curso['padrao_ano_escolar'];
            $this->modalidade_curso = $det_curso['modalidade_curso'];

            $inep = $obj_turma->getInep();

            if ($inep) {
                $this->codigo_inep_educacenso = $inep;
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $objTurma = new clsPmieducarTurma($this->cod_turma);
                $possuiAlunosVinculados = $objTurma->possuiAlunosVinculados();

                if ($possuiAlunosVinculados) {
                    $this->script_excluir = 'excluir_turma_com_matriculas();';
                } elseif ($this->acao_executa_submit_ajax) {
                    $this->script_excluir = 'excluirAjax();';
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(
                    586,
                    $this->pessoa_logada,
                    7,
                    'educar_turma_lst.php'
                );

                $retorno = 'Editar';
            }
        }

        $this->dias_semana = transformStringFromDBInArray($this->dias_semana);
        $this->atividades_complementares = transformStringFromDBInArray($this->atividades_complementares);
        $this->estrutura_curricular = transformStringFromDBInArray($this->estrutura_curricular);
        $this->cod_curso_profissional = transformStringFromDBInArray($this->cod_curso_profissional);
        $this->unidade_curricular = transformStringFromDBInArray($this->unidade_curricular);

        $this->url_cancelar = $retorno == 'Editar' ?
            'educar_turma_det.php?cod_turma=' . $registro['cod_turma'] : 'educar_turma_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' turma', [
            url('intranet/educar_index.php') => 'Escola',
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

        if (is_numeric($this->cod_turma)) {
            $obj_turma = new clsPmieducarTurma($this->cod_turma);
            $registro = $obj_turma->detalhe();
            $obj_esc = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
            $det_esc = $obj_esc->detalhe();
            $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
            $det_ser = $obj_ser->detalhe();

            $this->ref_cod_escola = $det_esc['cod_escola'];
            $this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
            $this->ref_cod_curso = $det_ser['ref_cod_curso'];
            $this->ref_cod_serie = $det_ser['cod_serie'];
            $this->ano = $registro['ano'];
        }

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();

        $this->campoOculto('obrigar_campos_censo', (int)$obrigarCamposCenso);
        $this->campoOculto('cod_turma', $this->cod_turma);
        $this->campoOculto('ref_cod_escola_', $this->ref_cod_escola);
        $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
        $this->campoOculto('ref_cod_serie_', $this->ref_cod_serie);
        $this->campoOculto('ano_letivo', (is_null($this->ano) ? date('Y') : $this->ano));
        $this->campoOculto('dependencia_administrativa', $this->dependencia_administrativa);
        $this->campoOculto('modalidade_curso', $this->modalidade_curso);
        $this->campoOculto('retorno', $this->retorno);

        $bloqueia = false;
        if (!isset($this->cod_turma)) {
            $bloqueia = false;
        } elseif (is_numeric($this->cod_turma)) {
            $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
            $obj_matriculas_turma->setOrderby('nome_aluno');
            $lst_matriculas_turma = $obj_matriculas_turma->lista(
                null,
                $this->cod_turma,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                null,
                null,
                null,
                null,
                null,
                null,
                [1, 2, 3],
                null,
                null,
                null,
                null,
                true,
                null,
                1,
                true
            );

            if (is_array($lst_matriculas_turma) && count($lst_matriculas_turma) > 0) {
                $bloqueia = true;
            }
        }

        $desabilitado = $bloqueia;

        $this->inputsHelper()->dynamic('ano', ['value' => (is_null($this->ano) ? date('Y') : $this->ano), 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('instituicao', ['value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('escola', ['value' => $this->ref_cod_escola, 'disabled' => $desabilitado]);

        $multiseriada = $this->multiseriada ?? 0;
        $this->campoCheck('multiseriada', 'Multisseriada', $multiseriada);

        $opcoesCursos = [
            null => 'Selecione um curso',
        ];

        if ($this->ref_ref_cod_escola) {
            $cursosDaEscola = LegacySchoolCourse::query()
                ->with('course')
                ->where('ref_cod_escola', $this->ref_ref_cod_escola)
                ->get()
                ->pluck('course.nm_curso', 'ref_cod_curso')
                ->toArray();
            $opcoesCursos = array_replace($opcoesCursos, $cursosDaEscola);
        }

        $this->inputsHelper()->dynamic(['curso', 'serie'], ['disabled' => $desabilitado]);

        $tiposBoletim = Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
        asort($tiposBoletim);
        $tiposBoletim = Portabilis_Array_Utils::insertIn(null, 'Selecione um modelo', $tiposBoletim);

        $this->campoTabelaInicio('turma_serie', 'Séries da turma', ['Curso', 'Série', 'Boletim', 'Boletim diferenciado'], $this->turma_serie);
        $this->campoLista('mult_curso_id', 'Curso', $opcoesCursos, $this->mult_curso_id, 'atualizaInformacoesComBaseNoCurso(this)');
        $this->campoLista('mult_serie_id', 'Série', ['Selecione uma série'], $this->mult_serie_id, 'atualizaInformacoesComBaseNaSerie()');
        $this->campoLista('mult_boletim_id', 'Boletim', $tiposBoletim, $this->mult_boletim_id);
        $this->campoLista('mult_boletim_diferenciado_id', 'Boletim diferenciado', $tiposBoletim, $this->mult_boletim_diferenciado_id, null, null, null, null, null, false);
        $this->campoOculto('mult_padrao_ano_escolar', $this->mult_padrao_ano_escolar);
        $this->campoTabelaFim();

        // Infra prédio cômodo
        $opcoes = ['' => 'Selecione'];

        // Editar
        if ($this->ref_ref_cod_escola) {
            $obj_infra_predio = new clsPmieducarInfraPredio();
            $obj_infra_predio->setOrderby('nm_predio ASC');
            $lst_infra_predio = $obj_infra_predio->lista(
                null,
                null,
                null,
                $this->ref_ref_cod_escola,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1
            );

            if (is_array($lst_infra_predio) && count($lst_infra_predio)) {
                foreach ($lst_infra_predio as $predio) {
                    $obj_infra_predio_comodo = new clsPmieducarInfraPredioComodo();
                    $lst_infra_predio_comodo = $obj_infra_predio_comodo->lista(
                        null,
                        null,
                        null,
                        null,
                        $predio['cod_infra_predio'],
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        1
                    );

                    if (is_array($lst_infra_predio_comodo) && count($lst_infra_predio_comodo)) {
                        foreach ($lst_infra_predio_comodo as $comodo) {
                            $opcoes[$comodo['cod_infra_predio_comodo']] = $comodo['nm_comodo'];
                        }
                    }
                }
            }
        }

        $this->campoLista(
            'ref_cod_infra_predio_comodo',
            'Sala',
            $opcoes,
            $this->ref_cod_infra_predio_comodo,
            null,
            null,
            null,
            null,
            null,
            false
        );

        $array_servidor = ['' => 'Selecione um servidor'];
        if ($this->ref_cod_regente) {
            $obj_pessoa = new clsPessoa_($this->ref_cod_regente);
            $det = $obj_pessoa->detalhe();
            $array_servidor[$this->ref_cod_regente] = $det['nome'];
        }

        $this->campoListaPesq('ref_cod_regente', 'Professor/Regente', $array_servidor, $this->ref_cod_regente, '', '', false, '', '', null, null, '', true, false, false);

        // Turma tipo
        $opcoes = ['' => 'Selecione'];

        // Editar
        $objTemp = new clsPmieducarTurmaTipo();
        $objTemp->setOrderby('nm_tipo ASC');
        $lista = $objTemp->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['cod_turma_tipo']] = $registro['nm_tipo'];
            }
        }

        $script = 'javascript:showExpansivelIframe(520, 170, \'educar_turma_tipo_cad_pop.php\');';

        $script = sprintf(
            '<div id=\'img_turma\' border=\'0\' onclick=\'%s\'>',
            $script
        );

        $this->campoLista(
            'ref_cod_turma_tipo',
            'Tipo de turma',
            $opcoes,
            $this->ref_cod_turma_tipo,
            '',
            false,
            '',
            $script
        );

        $this->campoTexto('nm_turma', 'Nome da turma', $this->nm_turma, 30, 255, true);

        $this->campoTexto('sgl_turma', _cl('turma.detalhe.sigla'), $this->sgl_turma, 15, 15, false);

        $this->campoNumero('max_aluno', 'Máximo de Alunos', $this->max_aluno, 3, 3, true);

        unset($opcoes);
        if (!is_null($this->ref_cod_serie)) {
            $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
            $opcaoPadrao = [null => 'Selecione'];
            $listaComponentes = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);
            if (!empty($listaComponentes)) {
                foreach ($listaComponentes as $componente) {
                    $componente->nome = ucwords(strtolower($componente->nome));
                    $opcoes["{$componente->id}"] = "{$componente->nome}";
                }
                $opcoes = $opcaoPadrao + $opcoes;
                $this->campoLista('ref_cod_disciplina_dispensada', 'Disciplina dispensada', $opcoes, $this->ref_cod_disciplina_dispensada, '', false, '', '', false, false);
            }
        }

        $ativo = is_numeric($this->cod_turma) ? (bool) ($this->visivel) : true;
        $this->campoCheck('visivel', 'Ativo', $ativo);

        $resources = SelectOptions::tiposMediacaoDidaticoPedagogico();
        $options = ['label' => 'Tipo de mediação didático pedagógico', 'resources' => $resources, 'value' => $this->tipo_mediacao_didatico_pedagogico, 'required' => $obrigarCamposCenso, 'size' => 70,];
        $this->inputsHelper()->select('tipo_mediacao_didatico_pedagogico', $options);

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
            'horario_funcionamento_turma',
            '<b>Horário de funcionamento da turma</b>'
        );

        $this->campoHora('hora_inicial', 'Hora inicial', $this->hora_inicial, false, null, null, null);

        $this->campoHora(
            'hora_inicio_intervalo',
            'Hora inicial do intervalo',
            $this->hora_inicio_intervalo,
            false,
            null,
            null,
            null
        );

        $this->campoHora('hora_fim_intervalo', 'Hora final do intervalo', $this->hora_fim_intervalo, false, null, null, null);

        $this->campoHora('hora_final', 'Hora final', $this->hora_final, false, null, null, null);

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
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $this->inputsHelper()->turmaTurno();

        $this->campoLista('tipo_boletim', 'Modelo relatório boletim', $tiposBoletim, $this->tipo_boletim, '', false, '', '', false, false);
        $this->campoLista('tipo_boletim_diferenciado', 'Modelo relatório boletim diferenciado', $tiposBoletim, $this->tipo_boletim_diferenciado, '', false, '', '', false, false);

        $this->montaListaComponentesSerieEscola();

        $objTemp = new clsPmieducarModulo();
        $objTemp->setOrderby('nm_tipo ASC');

        $lista = $objTemp->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null
        );

        $opcoesCampoModulo = [];

        if (is_array($lista) && count($lista)) {
            $this->modulos = $lista;
            foreach ($lista as $registro) {
                $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
            }
        }

        $registros = [];

        if (is_numeric($this->cod_turma)) {
            $objTurma = new clsPmieducarTurmaModulo();
            $objTurma->setOrderBy('sequencial ASC');

            $registros = $objTurma->lista($this->cod_turma);
        }

        if (
            empty($registros)
            && is_numeric($this->ano)
            && is_numeric($this->ref_cod_escola)
        ) {
            $objAno = new clsPmieducarAnoLetivoModulo();
            $objAno->setOrderBy('sequencial ASC');

            $registros = $objAno->lista($this->ano, $this->ref_cod_escola);
        }

        if ($this->padrao_ano_escolar != 1) {
            $qtd_registros = 0;
            $moduloSelecionado = 0;

            if ($registros) {
                $moduloSelecionado = $registros[0]['ref_cod_modulo'];

                foreach ($registros as $campo) {
                    $this->turma_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_inicio']);
                    $this->turma_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_fim']);
                    $this->turma_modulo[$qtd_registros][] = $campo['dias_letivos'];
                    $qtd_registros++;
                }
            }
        }

        $this->campoQuebra2();

        $this->campoRotulo('etapas_cabecalho', '<b>Etapas da turma</b>');

        $this->campoLista(
            'ref_cod_modulo',
            'Etapa',
            $opcoesCampoModulo,
            $moduloSelecionado,
            null,
            null,
            null,
            null,
            null,
            true
        );

        $this->campoTabelaInicio('turma_modulo', 'Etapas', ['Data inicial', 'Data final', 'Dias Letivos'], $this->turma_modulo);

        $this->campoData('data_inicio', 'Data Início', $this->data_inicio, false);
        $this->campoData('data_fim', 'Data Fim', $this->data_fim, false);
        $this->campoTexto('dias_letivos', 'Dias Letivos', $this->dias_letivos_, 9);

        $this->campoTabelaFim();

        $this->campoOculto('padrao_ano_escolar', $this->padrao_ano_escolar);

        $this->acao_enviar = 'valida()';
        $this->acao_executa_submit = false;

        $this->inputsHelper()->integer('codigo_inep_educacenso', ['label' => 'Código INEP',
            'label_hint' => 'Somente números',
            'placeholder' => 'INEP',
            'required' => false,
            'max_length' => 14,
            'value' => $this->codigo_inep_educacenso]);

        $resources = [null => 'Selecione',
            0 => 'Escolarização',
            4 => 'Atividade complementar',
            5 => 'Atendimento educacional especializado (AEE)'];

        $options = ['label' => 'Tipo de atendimento', 'resources' => $resources, 'value' => $this->tipo_atendimento, 'required' => $obrigarCamposCenso, 'size' => 70,];
        $this->inputsHelper()->select('tipo_atendimento', $options);

        $helperOptions = ['objectName' => 'estrutura_curricular'];
        $options = [
            'label' => 'Estrutura curricular',
            'required' => false,
            'size' => 70,
            'options' => [
                'values' => $this->estrutura_curricular,
                'all_values'=> [
                    1 => 'Formação geral básica',
                    2 => 'Itinerário formativo',
                    3 => 'Não se aplica'
                ]
            ]
        ];

        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $atividadesComplementares = loadJson('educacenso_json/atividades_complementares.json');
        $helperOptions = ['objectName' => 'atividades_complementares'];
        $options = ['label' => 'Tipos de atividades complementares',
            'size' => 50,
            'required' => false,
            'options' => ['values' => $this->atividades_complementares,
                'all_values' => $atividadesComplementares]];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $resources = Portabilis_Utils_Database::fetchPreparedQuery('SELECT id,nome FROM modules.etapas_educacenso');
        $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
        $resources = Portabilis_Array_Utils::merge($resources, ['null' => 'Selecione']);

        $etapas_educacenso = loadJson('educacenso_json/etapas_ensino.json');
        $etapas_educacenso = array_replace([null => 'Selecione'], $etapas_educacenso);

        $options = ['label' => 'Etapa de ensino', 'resources' => $etapas_educacenso, 'value' => $this->etapa_educacenso, 'required' => false, 'size' => 70];
        $this->inputsHelper()->select('etapa_educacenso', $options);

        $resources = [
            null => 'Selecione',
            1 => 'Série/ano (séries anuais)',
            2 => 'Períodos semestrais',
            3 => 'Ciclo(s)',
            4 => 'Grupos não seriados com base na idade ou competência',
            5 => 'Módulos',
            6 => 'Alternância regular de períodos de estudos'
        ];

        $options = ['label' => 'Formas de organização da turma', 'resources' => $resources, 'value' => $this->formas_organizacao_turma, 'required' => false, 'size' => 70,];
        $this->inputsHelper()->select('formas_organizacao_turma', $options);

        $helperOptions = ['objectName' => 'unidade_curricular'];
        $options = [
            'label' => 'Unidade curricular',
            'required' => false,
            'size' => 70,
            'options' => [
                'values' => $this->unidade_curricular,
                'all_values'=> UnidadesCurriculares::getDescriptiveValues()
            ]
        ];

        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $cursos = loadJson('educacenso_json/cursos_da_educacao_profissional.json');
        $helperOptions = ['objectName' => 'cod_curso_profissional',
            'type' => 'single'];
        $options = ['label' => 'Curso de educação profissional',
            'size' => 50,
            'required' => false,
            'options' => ['values' => $this->cod_curso_profissional,
                'all_values' => $cursos]];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $resources = App_Model_LocalFuncionamentoDiferenciado::getInstance()->getEnums();
        $resources = array_replace([null => 'Selecione'], $resources);

        $options = ['label' => 'Local de funcionamento diferenciado da turma', 'resources' => $resources, 'value' => $this->local_funcionamento_diferenciado, 'required' => false, 'size' => 70,];
        $this->inputsHelper()->select('local_funcionamento_diferenciado', $options);

        $options = ['label' => 'Não informar esta turma no Censo escolar',
            'value' => $this->nao_informar_educacenso,
            'label_hint' => 'Caso este campo seja selecionado, esta turma e todas as matrículas vinculadas a mesma, não serão informadas no arquivo de exportação do Censo escolar'];
        $this->inputsHelper()->checkbox('nao_informar_educacenso', $options);

        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/Turma.js',
            '/intranet/scripts/etapas.js',
            '/intranet/scripts/tabelaSerieMult.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $styles = ['/modules/Cadastro/Assets/Stylesheets/Turma.css'];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    protected function obrigaCamposHorario()
    {
        return $this->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
    }

    protected function existeComponentesNaTurma()
    {
        if ($this->cod_turma) {
            return LegacyDisciplineSchoolClass::query()
                ->where('turma_id', $this->cod_turma)
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
                    $this->ref_cod_serie,
                    $this->ref_cod_escola,
                    null,
                    null,
                    null,
                    true,
                    $this->ano
                );
            } catch (Throwable) {
                return;
            }

            // Instancia o mapper de turma
            $componenteTurmaMapper = new ComponenteCurricular_Model_TurmaDataMapper();
            $componentesTurma = [];

            if (isset($this->cod_turma) && is_numeric($this->cod_turma)) {
                $componentesTurma = $componenteTurmaMapper->findAll(
                    [],
                    ['turma' => $this->cod_turma]
                );
            }

            $componentes = [];
            foreach ($componentesTurma as $componenteTurma) {
                $componentes[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
            }
            unset($componentesTurma);

            $instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
            $instituicao = $instituicao->detalhe();

            $podeCadastrarComponenteDiferenciado = dbBool($instituicao['componente_curricular_turma']);

            if ($podeCadastrarComponenteDiferenciado) {
                $checkDefinirComponente = ($componentes == true);
                $disableDefinirComponente = false;
            } else {
                $disableDefinirComponente = true;
            }

            $this->campoCheck(
                'definir_componentes_diferenciados',
                'Definir componentes curriculares diferenciados',
                $checkDefinirComponente,
                null,
                false,
                false,
                $disableDefinirComponente,
                'Está opção poderá ser utilizada, somente se no cadastro da instituição o parâmetro de permissão estiver habilitado'
            );

            $this->escola_serie_disciplina = [];

            if (is_array($lista) && count($lista)) {
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

                    if (is_null($componentes[$registro->id]->cargaHoraria) ||
                        0 == $componentes[$registro->id]->cargaHoraria) {
                        $usarComponente = true;
                    } else {
                        $cargaHoraria = $componentes[$registro->id]->cargaHoraria;
                    }
                    $cargaComponente = $registro->cargaHoraria;

                    if (1 == $componentes[$registro->id]->docenteVinculado) {
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
        }

        $help = [];

        $label = 'Componentes curriculares definidos em séries da escola';

        if ($this->multiseriada && !$existeComponentesNaTurma) {
            $label = 'Os componentes curriculares de turmas multisseriadas devem ser definidos em suas respectivas Séries (Escola > Cadastros > Séries da escola)';
        }

        $label = sprintf($label, $help);

        $this->campoRotulo(
            'disciplinas_',
            $label,
            "<div id='disciplinas'>$disciplinas</div>"
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
                'etapas' => (int)$modulo['num_etapas']
            ];
        }

        return json_encode($retorno);
    }

    public function makeExtra()
    {
        return str_replace(
            '#modulos',
            $this->gerarJsonDosModulos(),
            file_get_contents(__DIR__ . '/scripts/extra/educar-turma-cad.js')
        );
    }

    public function Formular()
    {
        $this->title = 'Turma';
        $this->processoAp = 586;
    }
};
