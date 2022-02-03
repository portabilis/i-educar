<?php

use App\Exceptions\SchoolClass\DisciplinesValidationException;
use App\Models\LegacyCourse;
use App\Models\LegacySchoolClass;
use App\Models\School;
use App\Services\iDiarioService;
use App\Services\SchoolClass\ExemptedDisciplineLinksRemover;
use App\Services\SchoolClassService;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Support\View\SelectOptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

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
    public $padrao_ano_escolar;
    public $ref_cod_regente;
    public $ref_cod_instituicao_regente;
    public $ref_cod_serie_mult;
    public $turma_modulo = [];
    public $incluir_modulo;
    public $excluir_modulo;
    public $visivel;
    public $tipo_atendimento;
    public $atividades_complementares;
    public $cod_curso_profissional;
    public $etapa_educacenso;
    public $ref_cod_disciplina_dispensada;
    public $codigo_inep_educacenso;
    public $tipo_mediacao_didatico_pedagogico;
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
        3 => 'Ter&ccedil;a',
        4 => 'Quarta',
        5 => 'Quinta',
        6 => 'Sexta',
        7 => 'S&aacute;bado'
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

        if (is_numeric($this->cod_turma)) {
            $obj_turma = new clsPmieducarTurma($this->cod_turma);
            $registro = $obj_turma->detalhe();
            $obj_esc = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
            $det_esc = $obj_esc->detalhe();
            $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
            $det_ser = $obj_ser->detalhe();

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

        if (is_string($this->dias_semana)) {
            $this->dias_semana = explode(',', str_replace(['{', '}'], '', $this->dias_semana));
        }

        if (is_string($this->atividades_complementares)) {
            $this->atividades_complementares = explode(',', str_replace(['{', '}'], '', $this->atividades_complementares));
        }

        if (is_string($this->cod_curso_profissional)) {
            $this->cod_curso_profissional = explode(',', str_replace(['{', '}'], '', $this->cod_curso_profissional));
        }

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

        if (is_numeric($this->ano_letivo)) {
            $this->ano = $this->ano_letivo;
        }

        $this->campoOculto('obrigar_campos_censo', (int)$obrigarCamposCenso);
        $this->campoOculto('cod_turma', $this->cod_turma);
        $this->campoOculto('ano_letivo', (is_null($this->ano) ? date('Y') : $this->ano));
        $this->campoOculto('dependencia_administrativa', $this->dependencia_administrativa);
        $this->campoOculto('modalidade_curso', $this->modalidade_curso);
        $this->campoOculto('retorno', $this->retorno);

        $bloqueia = false;
        if (!isset($this->cod_turma)) {
            $bloqueia = false;
        } else {
            if (is_numeric($this->cod_turma)) {
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
        }

        $desabilitado = $bloqueia;

        $this->inputsHelper()->dynamic('ano', ['value' => (is_null($this->ano) ? date('Y') : $this->ano), 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie'], ['disabled' => $desabilitado]);

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

        if ($this->ref_cod_instituicao && $this->ref_cod_escola && $this->ref_cod_curso) {
            $script = sprintf(
                '<div id=\'img_turma\' border=\'0\' onclick=\'%s\'>',
                $script
            );
        } else {
            $script = sprintf(
                '<div id=\'img_turma\' border=\'0\' onclick=\'%s\'>',
                $script
            );
        }

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

        $this->campoNumero('max_aluno', 'M&aacute;ximo de Alunos', $this->max_aluno, 3, 3, true);

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

        $ativo = isset($this->cod_turma) ? dbBool($this->visivel) : true;
        $this->campoCheck('visivel', 'Ativo', $ativo);

        $this->campoCheck(
            'multiseriada',
            'Multi-Seriada',
            $this->multiseriada,
            '',
            false,
            false
        );

        $this->campoLista(
            'ref_cod_serie_mult',
            'S&eacute;rie',
            ['' => 'Selecione'],
            '',
            '',
            false,
            '',
            '',
            '',
            false
        );

        $this->campoOculto('ref_cod_serie_mult_', $this->ref_ref_cod_serie_mult);

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

        $tiposBoletim = Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
        asort($tiposBoletim);
        $tiposBoletim = Portabilis_Array_Utils::insertIn(null, 'Selecione um modelo', $tiposBoletim);

        $this->campoLista('tipo_boletim', 'Modelo de boletim', $tiposBoletim, $this->tipo_boletim);
        $this->campoLista('tipo_boletim_diferenciado', 'Modelo do boletim inclusivo', $tiposBoletim, $this->tipo_boletim_diferenciado, '', false, '', '', false, false);

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

        $this->campoData('data_inicio', 'Data In&iacute;cio', $this->data_inicio, false);
        $this->campoData('data_fim', 'Data Fim', $this->data_fim, false);
        $this->campoTexto('dias_letivos', 'Dias Letivos', $this->dias_letivos_, 9);

        $this->campoTabelaFim();

        $this->campoOculto('padrao_ano_escolar', $this->padrao_ano_escolar);

        $this->acao_enviar = 'valida()';

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

        $options = ['label' => 'Etapa de ensino', 'resources' => $etapas_educacenso, 'value' => $this->etapa_educacenso, 'required' => true, 'size' => 70];
        $this->inputsHelper()->select('etapa_educacenso', $options);

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
            '/intranet/scripts/etapas.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $styles = ['/modules/Cadastro/Assets/Stylesheets/Turma.css'];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    protected function obrigaCamposHorario()
    {
        return $this->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
    }

    public function montaListaComponentesSerieEscola()
    {
        $this->campoQuebra2();

        if ($this->ref_cod_serie) {
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
            } catch (Throwable $e) {
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
                    $conteudo .= "<td><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" class='check-disciplina' id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</td>";
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

        $label = 'Componentes curriculares definidos em s&eacute;ries da escola';

        $label = sprintf($label, $help);

        $this->campoRotulo(
            'disciplinas_',
            $label,
            "<div id='disciplinas'>$disciplinas</div>"
        );
    }

    /**
     * @see SchoolClassService::isAvailableName()
     *
     * @param int      $ano
     * @param int      $curso
     * @param int      $serie
     * @param int      $escola
     * @param string   $nome
     * @param int|null $id
     *
     * @return bool
     */
    public function nomeEstaDisponivel($ano, $curso, $serie, $escola, $nome, $id = null)
    {
        $service = new SchoolClassService();

        return $service->isAvailableName($nome, $curso, $serie, $escola, $ano, $id);
    }

    /**
     * Valida o campo Boletim Diferenciado
     *
     * @param $levelId
     * @param $academicYear
     * @param $alternativeReportCard
     *
     * @return bool
     */
    public function temBoletimDiferenciado($levelId, $academicYear, $alternativeReportCard)
    {
        if ($alternativeReportCard) {
            return true;
        }

        $service = new SchoolClassService();

        if ($service->isRequiredAlternativeReportCard($levelId, $academicYear)) {
            return false;
        }

        return true;
    }

    public function Novo()
    {
        if (!$this->canCreateTurma($this->ref_cod_escola, $this->ref_cod_serie, $this->turma_turno_id)) {
            return false;
        }

        if (!$this->verificaModulos()) {
            return false;
        }

        if (!$this->verificaCamposCenso()) {
            return false;
        }

        if (!$this->nomeEstaDisponivel($this->ano, $this->ref_cod_curso, $this->ref_cod_serie, $this->ref_cod_escola, $this->nm_turma)) {
            $this->mensagem = 'O nome da turma já está sendo utilizado nesta escola, para o curso, série e anos informados.';

            return false;
        }

        if (!$this->temBoletimDiferenciado($this->ref_cod_serie, $this->ano, $this->tipo_boletim_diferenciado)) {
            $this->mensagem = 'O campo \'<b>Boletim diferenciado</b>\' é obrigatório quando a regra de avaliação da série possui regra diferenciada definida.';

            return false;
        }

        $this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;

        $this->multiseriada = isset($this->multiseriada) ? 1 : 0;
        $this->visivel = isset($this->visivel);

        $objTurma = $this->montaObjetoTurma(null, $this->pessoa_logada);

        $this->cod_turma = $cadastrou = $objTurma->cadastra();

        if (!$cadastrou) {
            $this->mensagem = 'Cadastro não realizado.';

            return false;
        }

        $turma = new clsPmieducarTurma($this->cod_turma);
        $turma = $turma->detalhe();

        $this->atualizaComponentesCurriculares(
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->cod_turma,
            $this->disciplinas,
            $this->carga_horaria,
            $this->usar_componente,
            $this->docente_vinculado
        );

        $this->cadastraInepTurma($this->cod_turma, $this->codigo_inep_educacenso);

        if (!$this->atualizaModulos()) {
            return false;
        }

        $this->mensagem = 'Cadastro efetuado com sucesso.';
        $this->simpleRedirect('educar_turma_lst.php');
    }

    public function Editar()
    {
        $turmaDetalhe = new clsPmieducarTurma($this->cod_turma);
        $possuiAlunosVinculados = $turmaDetalhe->possuiAlunosVinculados();
        $turmaDetalhe = $turmaDetalhe->detalhe();
        $this->ref_cod_curso = $this->ref_cod_curso ?? $turmaDetalhe['ref_cod_curso'];
        $this->ref_ref_cod_escola = $this->ref_ref_cod_escola ?? $turmaDetalhe['ref_ref_cod_escola'];

        if (!$this->verificaModulos()) {
            return false;
        }

        if (!$this->verificaCamposCenso()) {
            return false;
        }

        $this->visivel = isset($this->visivel);

        if (!$this->visivel && $possuiAlunosVinculados) {
            $this->mensagem = 'Não foi possível inativar a turma, pois a mesma possui matrículas vinculadas.';

            return false;
        }

        $this->multiseriada = isset($this->multiseriada) ? 1 : 0;

        $objTurma = $this->montaObjetoTurma($this->cod_turma, null, $this->pessoa_logada);
        $dadosTurma = $objTurma->detalhe();

        if (!$this->nomeEstaDisponivel($dadosTurma['ano'], $this->ref_cod_curso, $dadosTurma['ref_ref_cod_serie'], $dadosTurma['ref_ref_cod_escola'], $this->nm_turma, $this->cod_turma)) {
            $this->mensagem = 'O nome da turma já está sendo utilizado nesta escola, para o curso, série e anos informados.';

            return false;
        }

        if (!$this->temBoletimDiferenciado($dadosTurma['ref_ref_cod_serie'], $dadosTurma['ano'], $this->tipo_boletim_diferenciado)) {
            $this->mensagem = 'O campo \'<b>Boletim diferenciado</b>\' é obrigatório quando a regra de avaliação da série possui regra diferenciada definida.';

            return false;
        }

        if (!$this->verificaTurno()) {
            return false;
        }

        if (is_null($this->ref_cod_instituicao)) {
            $this->ref_cod_instituicao = $turmaDetalhe['ref_cod_instituicao'];
            $this->ref_cod_instituicao_regente = $turmaDetalhe['ref_cod_instituicao'];
        } else {
            $this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;
        }

        DB::beginTransaction();
        $editou = $objTurma->edita();

        if (!$editou) {
            $this->mensagem = 'Edição não realizada.';

            DB::rollBack();

            return false;
        }

        if ($this->ref_cod_disciplina_dispensada) {
            (new ExemptedDisciplineLinksRemover())->remove(LegacySchoolClass::find($this->cod_turma));
        }

        try {
            $this->atualizaComponentesCurriculares(
                $turmaDetalhe['ref_ref_cod_serie'],
                $turmaDetalhe['ref_ref_cod_escola'],
                $this->cod_turma,
                $this->disciplinas,
                $this->carga_horaria,
                $this->usar_componente,
                $this->docente_vinculado
            );
        } catch (DisciplinesValidationException $e) {
            $this->mensagem = $e->getMessage();

            DB::rollBack();

            return false;
        }

        $this->cadastraInepTurma($this->cod_turma, $this->codigo_inep_educacenso);

        if (!$this->atualizaModulos()) {
            DB::rollBack();

            return false;
        }

        DB::commit();

        $this->mensagem = 'Edição efetuada com sucesso.';

        throw new HttpResponseException(
            new RedirectResponse('educar_turma_lst.php')
        );
    }

    protected function validaCamposHorario()
    {
        if (!$this->obrigaCamposHorario()) {
            return true;
        }
        if (empty($this->hora_inicial)) {
            $this->mensagem = 'O campo hora inicial é obrigatório';

            return false;
        }
        if (empty($this->hora_final)) {
            $this->mensagem = 'O campo hora final é obrigatório';

            return false;
        }
        if (empty($this->hora_inicio_intervalo)) {
            $this->mensagem = 'O campo hora início intervalo é obrigatório';

            return false;
        }
        if (empty($this->hora_fim_intervalo)) {
            $this->mensagem = 'O campo hora fim intervalo é obrigatório';

            return false;
        }
        if (empty($this->dias_semana)) {
            $this->mensagem = 'O campo dias da semana é obrigatório';

            return false;
        }

        return true;
    }

    protected function validaCampoAtividadesComplementares()
    {
        if ($this->tipo_atendimento == 4 && empty($this->atividades_complementares)) {
            $this->mensagem = 'Campo atividades complementares é obrigatório';

            return false;
        }

        return true;
    }

    protected function validaCampoTipoAtendimento()
    {
        if ($this->tipo_atendimento != 0 && in_array($this->tipo_mediacao_didatico_pedagogico, [
            App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL,
            App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA
        ])) {
            $this->mensagem = 'O campo: Tipo de atendimento deve ser: Escolarização quando o campo: Tipo de mediação didático-pedagógica for: Semipresencial ou Educação a Distância.';

            return false;
        }

        return true;
    }

    protected function validaCampoLocalFuncionamentoDiferenciado()
    {
        $school = School::find($this->ref_ref_cod_escola);
        $localFuncionamentoEscola = $school->local_funcionamento;
        if (is_string($localFuncionamentoEscola)) {
            $localFuncionamentoEscola = explode(',', str_replace(['{', '}'], '', $localFuncionamentoEscola));
        }

        $localFuncionamentoEscola = (array) $localFuncionamentoEscola;

        if (!in_array(9, $localFuncionamentoEscola) && $this->local_funcionamento_diferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO) {
            $this->mensagem = 'Não é possível selecionar a opção: Unidade de atendimento socioeducativo quando o local de funcionamento da escola não for: Unidade de atendimento socioeducativo.';

            return false;
        }

        if (!in_array(10, $localFuncionamentoEscola) && $this->local_funcionamento_diferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL) {
            $this->mensagem = 'Não é possível selecionar a opção: Unidade prisional quando o local de funcionamento da escola não for: Unidade prisional.';

            return false;
        }

        return true;
    }

    protected function validaTipoAtendimento()
    {
        if ($this->tipo_atendimento == 4 && empty($this->atividades_complementares)) {
            $this->mensagem = 'Campo atividades complementares é obrigatório';

            return false;
        }

        return true;
    }

    protected function validaCampoEtapaEnsino()
    {
        if (!empty($this->tipo_atendimento) &&
            $this->tipo_atendimento != -1 &&
            $this->tipo_atendimento != 4 &&
            $this->tipo_atendimento != 5) {
            $this->mensagem = 'Campo etapa de ensino é obrigatório';

            return false;
        }

        return true;
    }

    private function validaEtapaEducacenso()
    {
        $course = LegacyCourse::find($this->ref_cod_curso);

        if ($this->tipo_atendimento != TipoAtendimentoTurma::ESCOLARIZACAO) {
            return true;
        }

        if ($course->modalidade_curso == 1 && !in_array($this->etapa_educacenso, [1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 35, 36, 37, 38, 41, 56])) {
            $this->mensagem = 'Quando a modalidade do curso é: Ensino regular, o campo: Etapa de ensino deve ser uma das seguintes opções: 1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 35, 36, 37, 38, 41 ou 56.';

            return false;
        }

        if ($course->modalidade_curso == 2 && !in_array($this->etapa_educacenso, [1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 56, 39, 40, 69, 70, 71, 72, 73, 74, 64, 67, 68])) {
            $this->mensagem = 'Quando a modalidade do curso é: Educação especial, o campo: Etapa de ensino deve ser uma das seguintes opções: 1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 56, 39, 40, 69, 70, 71, 72, 73, 74, 64, 67 ou 68.';

            return false;
        }

        if ($course->modalidade_curso == 3 && !in_array($this->etapa_educacenso, [69, 70, 71, 72])) {
            $this->mensagem = 'Quando a modalidade do curso é: Educação de Jovens e Adultos (EJA), o campo: Etapa de ensino deve ser uma das seguintes opções: 69, 70, 71 ou 72.';

            return false;
        }

        if ($course->modalidade_curso == 4 && !in_array($this->etapa_educacenso, [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68])) {
            $this->mensagem = 'Quando a modalidade do curso é: Educação Profissional, o campo: Etapa de ensino deve ser uma das seguintes opções: 30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67 ou 68.';

            return false;
        }

        if ($this->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL && !in_array($this->etapa_educacenso, [69, 70, 71, 72])) {
            $this->mensagem = 'Quando o campo: Tipo de mediação didático-pedagógica é: Semipresencial, o campo: Etapa de ensino deve ser uma das seguintes opções: 69, 70, 71 ou 72';

            return false;
        }

        if ($this->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA && !in_array($this->etapa_educacenso, [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 70, 71, 73, 74, 64, 67, 68])) {
            $this->mensagem = 'Quando o campo: Tipo de mediação didático-pedagógica é: Educação a Distância, o campo: Etapa de ensino deve ser uma das seguintes opções: 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 70, 71, 73, 74, 64, 67 ou 68';

            return false;
        }

        if (in_array($this->local_funcionamento_diferenciado, [App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO, App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL]) &&
            in_array($this->etapa_educacenso, [1, 2, 3, 56])
        ) {
            $nomeOpcao = (App_Model_LocalFuncionamentoDiferenciado::getInstance()->getEnums())[$this->local_funcionamento_diferenciado];
            $this->mensagem = "Quando o campo: Local de funcionamento diferenciado é: {$nomeOpcao}, o campo: Etapa de ensino não pode ser nenhuma das seguintes opções: 1, 2, 3 ou 56";

            return false;
        }

        return true;
    }

    protected function verificaCamposCenso()
    {
        if (!$this->validarCamposObrigatoriosCenso()) {
            return true;
        }
        if (!$this->validaCamposHorario()) {
            return false;
        }
        if (!$this->validaEtapaEducacenso()) {
            return false;
        }
        if (!$this->validaCampoAtividadesComplementares()) {
            return false;
        }
        if (!$this->validaCampoEtapaEnsino()) {
            return false;
        }
        if (!$this->validaCampoTipoAtendimento()) {
            return false;
        }
        if (!$this->validaCampoLocalFuncionamentoDiferenciado()) {
            return false;
        }

        return true;
    }

    protected function verificaTurno()
    {
        $turmaId = (int) $this->cod_turma;
        $turnoId = (int) $this->turma_turno_id;
        $count = 0;

        if ($turnoId === clsPmieducarTurma::TURNO_INTEGRAL) { // Se integral não pode ter vínculos noturnos
            $count += DB::table('pmieducar.matricula_turma as mt')
                ->join('pmieducar.turma as t', 't.cod_turma', '=', 'mt.ref_cod_turma')
                ->where('mt.turno_id', clsPmieducarTurma::TURNO_NOTURNO)
                ->where('t.cod_turma', $turmaId)
                ->count();

            $count += DB::table('modules.professor_turma as pt')
                ->join('pmieducar.turma as t', 't.cod_turma', '=', 'pt.turma_id')
                ->where('pt.turno_id', clsPmieducarTurma::TURNO_NOTURNO)
                ->where('t.cod_turma', $turmaId)
                ->count();
        } else { // Se ñ é integral não pode ter vínculos diferentes do novo turno
            $count += DB::table('pmieducar.matricula_turma as mt')
                ->join('pmieducar.turma as t', 't.cod_turma', '=', 'mt.ref_cod_turma')
                ->where('mt.turno_id', '<>', $turnoId)
                ->where('t.cod_turma', $turmaId)
                ->count();

            $count += DB::table('modules.professor_turma as pt')
                ->join('pmieducar.turma as t', 't.cod_turma', '=', 'pt.turma_id')
                ->where('pt.turno_id', '<>', $turnoId)
                ->where('t.cod_turma', $turmaId)
                ->count();
        }

        if ($count > 0) {
            $this->mensagem = 'Existem enturmações ou professores atrelados a esta turma em turnos diferentes do especificado.';

            return false;
        }

        return true;
    }

    public function montaObjetoTurma($codTurma = null, $usuarioCad = null, $usuarioExc = null)
    {
        $this->dias_semana = is_array($this->dias_semana) ? $this->dias_semana : [];
        $this->dias_semana = '{' . implode(',', $this->dias_semana) . '}';
        $this->atividades_complementares = '{' . implode(',', $this->atividades_complementares) . '}';
        $this->cod_curso_profissional = $this->cod_curso_profissional[0];

        if ($this->tipo_atendimento != 4) {
            $this->atividades_complementares = '{}';
        }

        $etapasCursoTecnico = [30, 31, 32, 33, 34, 39, 40, 64, 74];

        if (!in_array($this->etapa_educacenso, $etapasCursoTecnico)) {
            $this->cod_curso_profissional = null;
        }

        $objTurma = new clsPmieducarTurma($codTurma);
        $objTurma->ref_usuario_cad = $usuarioCad;
        $objTurma->ref_usuario_exc = $usuarioExc;
        $objTurma->ref_ref_cod_serie = $this->ref_cod_serie;
        $objTurma->ref_ref_cod_escola = $this->ref_cod_escola;
        $objTurma->ref_cod_infra_predio_comodo = $this->ref_cod_infra_predio_comodo;
        $objTurma->nm_turma = $this->nm_turma;
        $objTurma->sgl_turma = $this->sgl_turma;
        $objTurma->max_aluno = $this->max_aluno;
        $objTurma->multiseriada = $this->multiseriada;
        $objTurma->ativo = 1;
        $objTurma->ref_cod_turma_tipo = $this->ref_cod_turma_tipo;
        $objTurma->hora_inicial = $this->hora_inicial;
        $objTurma->hora_final = $this->hora_final;
        $objTurma->hora_inicio_intervalo = $this->hora_inicio_intervalo;
        $objTurma->hora_fim_intervalo = $this->hora_fim_intervalo;
        $objTurma->ref_cod_regente = $this->ref_cod_regente;
        $objTurma->ref_cod_instituicao_regente = $this->ref_cod_instituicao_regente;
        $objTurma->ref_cod_instituicao = $this->ref_cod_instituicao;
        $objTurma->ref_cod_curso = $this->ref_cod_curso;
        $objTurma->ref_ref_cod_serie_mult = $objTurma->multiseriada ? $this->ref_cod_serie_mult : null;
        $objTurma->ref_ref_cod_escola_mult = $objTurma->multiseriada ? $this->ref_cod_escola : null;
        $objTurma->visivel = $this->visivel;
        $objTurma->turma_turno_id = $this->turma_turno_id;
        $objTurma->tipo_boletim = $this->tipo_boletim;
        $objTurma->tipo_boletim_diferenciado = $this->tipo_boletim_diferenciado;
        $objTurma->ano = $this->ano;
        $objTurma->tipo_atendimento = $this->tipo_atendimento;
        $objTurma->cod_curso_profissional = $this->cod_curso_profissional;
        $objTurma->etapa_educacenso = $this->etapa_educacenso == '' ? null : $this->etapa_educacenso;
        $objTurma->ref_cod_disciplina_dispensada = $this->ref_cod_disciplina_dispensada == '' ? null : $this->ref_cod_disciplina_dispensada;
        $objTurma->nao_informar_educacenso = $this->nao_informar_educacenso == 'on' ? 1 : 0;
        $objTurma->tipo_mediacao_didatico_pedagogico = $this->tipo_mediacao_didatico_pedagogico;
        $objTurma->dias_semana = $this->dias_semana;
        $objTurma->atividades_complementares = $this->atividades_complementares;
        $objTurma->local_funcionamento_diferenciado = $this->local_funcionamento_diferenciado;

        return $objTurma;
    }

    protected function validaModulos()
    {
        $turmaId = $this->cod_turma;
        $anoTurma = $this->ano_letivo;
        $etapasCount = count($this->data_inicio);
        $etapasCountAntigo = (int) Portabilis_Utils_Database::selectField(
            'SELECT COUNT(*) AS count FROM pmieducar.turma_modulo WHERE ref_cod_turma = $1',
            [$turmaId]
        );

        if ($etapasCount >= $etapasCountAntigo) {
            return true;
        }

        $course = LegacyCourse::query()->find($this->ref_cod_curso);
        if ($course != null && $course->padrao_ano_escolar = 1) {
            return true;
        }

        $etapasTmp = $etapasCount;
        $etapas = [];

        while ($etapasTmp < $etapasCountAntigo) {
            $etapasTmp += 1;
            $etapas[] = $etapasTmp;
        }

        $counts = [];

        $counts[] = DB::table('modules.falta_componente_curricular as fcc')
            ->join('modules.falta_aluno as fa', 'fa.id', '=', 'fcc.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'fa.matricula_id')
            ->join('pmieducar.matricula_turma as mt', 'mt.ref_cod_matricula', '=', 'm.cod_matricula')
            ->whereIn('fcc.etapa', $etapas)
            ->where('mt.ref_cod_turma', $turmaId)
            ->where('m.ativo', 1)
            ->count();

        $counts[] = DB::table('modules.falta_geral as fg')
            ->join('modules.falta_aluno as fa', 'fa.id', '=', 'fg.falta_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'fa.matricula_id')
            ->join('pmieducar.matricula_turma as mt', 'mt.ref_cod_matricula', '=', 'm.cod_matricula')
            ->whereIn('fg.etapa', $etapas)
            ->where('mt.ref_cod_turma', $turmaId)
            ->where('m.ativo', 1)
            ->count();

        $counts[] = DB::table('modules.nota_componente_curricular as ncc')
            ->join('modules.nota_aluno as na', 'na.id', '=', 'ncc.nota_aluno_id')
            ->join('pmieducar.matricula as m', 'm.cod_matricula', '=', 'na.matricula_id')
            ->join('pmieducar.matricula_turma as mt', 'mt.ref_cod_matricula', '=', 'm.cod_matricula')
            ->whereIn('ncc.etapa', $etapas)
            ->where('mt.ref_cod_turma', $turmaId)
            ->where('m.ativo', 1)
            ->count();

        $sum = array_sum($counts);

        if ($sum > 0) {
            throw new RuntimeException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.');
        }

        // Caso não exista token e URL de integração com o i-Diário, não irá
        // validar se há lançamentos nas etapas removidas

        $checkReleases = config('legacy.config.url_novo_educacao')
            && config('legacy.config.token_novo_educacao');

        if (!$checkReleases) {
            return true;
        }

        $iDiarioService = app(iDiarioService::class);

        foreach ($etapas as $etapa) {
            if ($iDiarioService->getStepActivityByClassroom($turmaId, $anoTurma, $etapa)) {
                throw new RuntimeException('Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.');
            }
        }

        return true;
    }

    public function atualizaModulos()
    {
        try {
            $this->validaModulos();
        } catch (Exception $e) {
            $this->Inicializar();

            $this->mensagem = $e->getMessage();

            return false;
        }

        $objModulo = new clsPmieducarTurmaModulo();
        $excluiu = $objModulo->excluirTodos($this->cod_turma);
        $modulos = $this->montaModulos();

        if (!$excluiu) {
            $this->mensagem = 'Edição não realizada.';

            return false;
        }

        foreach ($modulos as $modulo) {
            $this->cadastraModulo($modulo);
        }

        return true;
    }

    public function montaModulos()
    {
        // itera pelo campo `data_inicio`, um dos campos referentes às etapas,
        // para definir sequencialmente os dados de cada etapa
        foreach ($this->data_inicio as $key => $modulo) {
            $turmaModulo[$key]['sequencial'] = $key + 1;
            $turmaModulo[$key]['ref_cod_modulo'] = $this->ref_cod_modulo;
            $turmaModulo[$key]['data_inicio'] = $this->data_inicio[$key];
            $turmaModulo[$key]['data_fim'] = $this->data_fim[$key];
            $turmaModulo[$key]['dias_letivos'] = $this->dias_letivos[$key];
        }

        return $turmaModulo;
    }

    public function cadastraModulo($modulo)
    {
        $modulo['data_inicio'] = dataToBanco($modulo['data_inicio']);
        $modulo['data_fim'] = dataToBanco($modulo['data_fim']);

        $objModulo = new clsPmieducarTurmaModulo($this->cod_turma);
        $objModulo->ref_cod_modulo = $modulo['ref_cod_modulo'];
        $objModulo->sequencial = $modulo['sequencial'];
        $objModulo->data_inicio = $modulo['data_inicio'];
        $objModulo->data_fim = $modulo['data_fim'];
        $objModulo->dias_letivos = $modulo['dias_letivos'];

        $cadastrou = $objModulo->cadastra();

        if (!$cadastrou) {
        }

        return true;
    }

    public function verificaModulos()
    {
        $cursoPadraoAnoEscolar = $this->padrao_ano_escolar == 1;
        $possuiModulosInformados = (count($this->data_inicio) > 1 || $this->data_inicio[0] != '');

        if ($cursoPadraoAnoEscolar) {
            return true;
        }

        if (!$possuiModulosInformados) {
            $this->mensagem = 'Edição não realizada.';

            return false;
        }

        return true;
    }

    public function atualizaComponentesCurriculares($codSerie, $codEscola, $codTurma, $componentes, $cargaHoraria, $usarComponente, $docente)
    {
        $mapper = new ComponenteCurricular_Model_TurmaDataMapper();

        $componentesTurma = [];

        foreach ($componentes as $key => $value) {
            $carga = isset($usarComponente[$key]) ?
                null : $cargaHoraria[$key];

            $docente_ = isset($docente[$key]) ?
                1 : 0;

            $etapasEspecificas = isset($this->etapas_especificas[$key]) ?
                1 : 0;

            $etapasUtilizadas = ($etapasEspecificas == 1) ? $this->etapas_utilizadas[$key] : null;

            $componentesTurma[] = [
                'id' => $value,
                'cargaHoraria' => $carga,
                'docenteVinculado' => $docente_,
                'etapasEspecificas' => $etapasEspecificas,
                'etapasUtilizadas' => $etapasUtilizadas
            ];
        }

        $idiarioService = $this->getIdiarioService();

        $mapper->bulkUpdate($codSerie, $codEscola, $codTurma, $componentesTurma, $idiarioService);
    }

    public function cadastraInepTurma($cod_turma, $codigo_inep_educacenso)
    {
        $turma = new clsPmieducarTurma($cod_turma);
        $turma->updateInep($codigo_inep_educacenso);
    }

    public function Excluir()
    {
        $obj = new clsPmieducarTurma(
            $this->cod_turma,
            $this->pessoa_logada,
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
            0
        );

        if ($obj->possuiAlunosVinculados()) {
            $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';

            return false;
        }

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $obj = new clsPmieducarTurmaModulo();
            $excluiu1 = $obj->excluirTodos($this->cod_turma);

            if ($excluiu1) {
                $this->mensagem = 'Exclusão efetuada com sucesso.';

                throw new HttpResponseException(
                    new RedirectResponse('educar_turma_lst.php')
                );
            } else {
                $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';

                return false;
            }
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';

        return false;
    }

    protected function getDb()
    {
        if (!isset($this->db)) {
            $this->db = new clsBanco();
        }

        return $this->db;
    }

    protected function getEscolaSerie($escolaId, $serieId)
    {
        $escolaSerie = new clsPmieducarEscolaSerie();
        $escolaSerie->ref_cod_escola = $escolaId;
        $escolaSerie->ref_cod_serie = $serieId;

        return $escolaSerie->detalhe();
    }

    protected function getCountMatriculas($turmaId)
    {
        if (!is_numeric($this->ano)) {
            $this->mensagem = 'É necessário informar um ano letivo.';

            return false;
        }

        $sql = "select count(cod_matricula) as matriculas from pmieducar.matricula, pmieducar.matricula_turma where ano = {$this->ano} and matricula.ativo = 1 and matricula_turma.ativo = matricula.ativo and cod_matricula = ref_cod_matricula and ref_cod_turma = {$turmaId}";

        return $this->getDb()->CampoUnico($sql);
    }

    protected function canCreateTurma($escolaId, $serieId, $turnoId)
    {
        $escolaSerie = $this->getEscolaSerie($escolaId, $serieId);

        if ($escolaSerie['bloquear_cadastro_turma_para_serie_com_vagas'] == 1) {
            $turmas = new clsPmieducarTurma();

            $turmas = $turmas->lista(null, null, null, $serieId, $escolaId, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, true, $turnoId, null, $this->ano, true);

            foreach ($turmas as $turma) {
                $countMatriculas = $this->getCountMatriculas($turma['cod_turma']);

                // countMatriculas retorna false e adiciona mensagem, se não obter ano em andamento
                if ($countMatriculas === false) {
                    return false;
                } elseif ($turma['max_aluno'] - $countMatriculas > 0) {
                    $vagas = $turma['max_aluno'] - $countMatriculas;
                    $this->mensagem = "N&atilde;o &eacute; possivel cadastrar turmas, pois ainda existem $vagas vagas em aberto na turma '{$turma['nm_turma']}' desta serie e turno.\n\nTal limita&ccedil;&atilde;o ocorre devido defini&ccedil;&atilde;o feita para esta escola e s&eacute;rie.";

                    return false;
                }
            }
        }

        return true;
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

    /**
     * Retorna instância do iDiarioService
     *
     * @return iDiarioService|null
     */
    private function getIdiarioService()
    {
        if (iDiarioService::hasIdiarioConfigurations()) {
            return app(iDiarioService::class);
        }

        return null;
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
