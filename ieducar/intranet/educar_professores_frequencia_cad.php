<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Models\Frequencia;
use App\Models\ComponenteCurricularTurma;
use App\Models\ComponenteCurricularAno;
use App\Models\SerieTurma;
use App\Models\Serie;
use App\Models\Turma;
use App\Models\FrequenciaInformacoes;

return new class extends clsCadastro {
    public $id;
    public $data;
    public $ref_cod_turma;
    public $ref_cod_componente_curricular;
    public $fase_etapa;
    public $ref_cod_serie;
    public $alunos;
    public $justificativa;
    public $ordens_aulas;
    public $ordens_aulas1;
    public $ordens_aulas2;
    public $ordens_aulas3;
    public $ordens_aulas4;
    public $ordens_aulas5;
    public $atividades;
    public $conteudos;
    public $planejamento_aula_ids;
    public $componente_curricular_registro_individual;
    public $registra_diario_individual;

    public function Inicializar () {
        $this->titulo = 'Frequência - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_frequencia_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesFrequencia($this->id);
            $registro = $tmp_obj->detalhe();


            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->matriculas = $registro['matriculas'];
                $this->alunos = $registro['alunos'];
                $this->ref_cod_serie = $registro['detalhes']['ref_cod_serie'];
                $this->ordens_aulas = explode(',', $registro['detalhes']['ordens_aulas']);
                $this->atividades = $registro['planejamento_aula']['atividades'];
                $this->conteudos = array_column($registro['planejamento_aula']['conteudos'], 'planejamento_aula_conteudo_id');


                $podeExcluir = (!empty($registro['detalhes']['cod_professor_registro']) && $registro['detalhes']['cod_professor_registro'] == $this->pessoa_logada) || empty($registro['detalhes']['cod_professor_registro']);

                if ($podeExcluir) {
                    $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                }


                $retorno = 'Editar';

                $this->titulo = 'Frequência - Edição';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_frequencia_det.php?id=%d', $this->id)
            : 'educar_professores_frequencia_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' frequência', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar () {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }
        $this->data = dataToBrasil($this->data);
        $this->ano = explode('/', $this->data)[2];

        if ($tipoacao == 'Edita' || !$_POST
            && $this->data
            && is_numeric($this->ref_cod_turma)
            && is_numeric($this->fase_etapa)
        ) {
            $desabilitado = true;
        }

        if (is_numeric($this->ref_cod_turma)) {
            $this->campoOculto('data_', dataToBanco($this->data));
            $this->campoOculto('ref_cod_turma_', $this->ref_cod_turma);
            $this->campoOculto('ref_cod_componente_curricular_', $this->ref_cod_componente_curricular);
            $this->campoOculto('fase_etapa_', $this->fase_etapa);
        }

        $tipo_presenca = '';
        if ($this->ref_cod_serie) {
            $obj = new clsPmieducarSerie();
            $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($this->ref_cod_serie);
        }

        $obrigatorio = true;

        $obj_servidor = new clsPmieducarServidor(
            $this->pessoa_logada,
            null,
            null,
            null,
            null,
            null,
            1,      //  Ativo
            1,      //  Fixado na instituição de ID 1
        );
        $isProfessor = $obj_servidor->isProfessor();

        $obj = new clsModulesPlanejamentoAula($this->id);
        $resultado = $obj->getMensagem($this->pessoa_logada);

        $this->campoOculto('id', $this->id);
        $this->campoOculto('servidor_id', $resultado['emissor_user_id']);
        $this->campoOculto('auth_id', $this->pessoa_logada);
        $this->campoOculto('is_professor', $isProfessor);
        $this->campoOculto('componente_curricular_registro_individual', '');

        $this->inputsHelper()->dynamic('data', ['required' => $obrigatorio, 'disabled' => $desabilitado]);  // Disabled não funciona; ação colocada no javascript.
        $this->inputsHelper()->dynamic('todasTurmas', ['required' => $obrigatorio, 'ano' => $this->ano, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('componenteCurricular', ['required' => !$obrigatorio, 'disabled' => !$desabilitado]);
        $this->inputsHelper()->dynamic('faseEtapa', ['required' => $obrigatorio, 'label' => 'Etapa', 'disabled' => $desabilitado]);



        if (empty($tipo_presenca) || $tipo_presenca == 2) {
            for ($i = 1; $i <= 5; $i++) {
                $this->inputsHelper()->checkbox('ordens_aulas'.$i, ['label' => "Quantidade de aulas <span class='campo_obrigatorio'>*</span> ", 'value' => (in_array($i, $this->ordens_aulas) ? $i : ''), 'disabled' => $desabilitado, 'required' => false, 'label_hint' => 'Aula '.$i]);
            }
        }

        if (is_numeric($this->id)) {
            $obj_servidor = new clsPmieducarServidor(
                $this->pessoa_logada,
                null,
                null,
                null,
                null,
                null,
                1,      //  Ativo
                1,      //  Fixado na instituição de ID 1
            );
            $is_professor = $obj_servidor->isProfessor();

            $obj = new clsModulesPlanejamentoAula();
            $planejamentos = $obj->lista(
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_turma,
                $this->ref_cod_componente_curricular,
                null,
                null,
                null,
                $this->fase_etapa,
                $is_professor ? $this->pessoa_logada : null,
                Portabilis_Date_Utils::brToPgSQL($this->data)
            );

            $this->planejamento_aula_ids = [];

            foreach ($planejamentos as $planejamento) {
                if (!in_array($planejamento['id'], $this->planejamento_aula_ids)) {
                    array_push($this->planejamento_aula_ids, $planejamento['id']);
                }
            }
        }


        // Editar
        $maxCaracteresObservacao = 256;
        $alunos = 'Faltam informações obrigatórias.';

        if ($this->data && is_numeric($this->ref_cod_turma) && $this->ref_cod_serie && $this->fase_etapa && $this->alunos) {
            $alunos = '';
            $conteudo = '';

            $matriculas = $this->matriculas['refs_cod_matricula'];
            $justificativas = $this->matriculas['justificativas'];
            $aulasArray = explode(';', $this->matriculas['aulas_faltou']);


            if (is_string($matriculas) && !empty($matriculas)) {
                $matriculas = explode(',', $matriculas);
                $justificativas = explode(',', $justificativas);

                for ($i = 0; $i < count($matriculas); $i++) {
                    $this->alunos[$matriculas[$i]]['presenca'] = true;
                    $this->alunos[$matriculas[$i]]['justificativa'] = $justificativas[$i];
                    $this->alunos[$matriculas[$i]]['aulas'] = explode(',', $aulasArray[$i]);
                }
            }


            $conteudo .= '  </tr><td class="tableDetalheLinhaSeparador" colspan="3"></td><tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%"><tr class="tableHeader">';
            $conteudo .= '  <th><span style="display: block; float: left; width: auto; font-weight: bold">'."Nome".'</span></th>';
            $conteudo .= '  <th><span style="display: block; float: left; width: auto; font-weight: bold">'."FA".'</span></th>';

            if ($tipo_presenca == 1) {
                $conteudo .= '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' . "Presença" . '</span></th>';
            }

            if ($tipo_presenca == 2) {
                for ($i = 1; $i <= count($this->ordens_aulas); $i++) {
                    $conteudo .= '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' . "Aula " .$i. '</span></th>';
                }
            }

            $conteudo .= '  <th><span style="display: block; float: left; width: auto; font-weight: bold">'."Justificativa".'</span></th>';
            $conteudo .= '  </tr>';
            $conteudo .= '  <tr><td class="tableDetalheLinhaSeparador" colspan="3"></td></tr>';


            $objFrequencia = new clsModulesFrequencia();

            foreach ($this->alunos as $key => $aluno) {
                $id = $aluno['matricula'];

                $qtdFaltasGravadas = $objFrequencia->getTotalFaltas($aluno['matricula'], $this->ref_cod_componente_curricular);

                $name = "alunos[" . $id . "]";
                $checked = !$aluno['presenca'] ? "checked='true'" : '';
                $disabled = !$aluno['presenca'] ? "disabled='true'" : '';

                $conteudo .= '  <tr>';
                $conteudo .= '  <td class="sizeFont colorFont"><p>' . $aluno['nome'] . '</p></td>';
                $conteudo .= '  <td class="sizeFont colorFont"><p>' . $qtdFaltasGravadas . '</p></td>';

                if($tipo_presenca == 1 && !$this->registra_diario_individual) {
                    $conteudo .= "  <td class='sizeFont colorFont'>
                                    <input
                                        type='checkbox'
                                        onchange='presencaMudou(this)'
                                        id='alunos[]'
                                        name={$name}
                                        value={$id}
                                        {$checked}
                                        autocomplete='off'
                                    >
                                    </td>";
                }

                if ($tipo_presenca == 1 && $this->registra_diario_individual) {
                    $aulasFaltou = '';
                    $qtdFaltas = 0;

                    if ($aluno['presenca']) {
                        $aulasFaltou = 1 . ',';
                        $qtdFaltas = 1;
                    }

                    $conteudo .= "  <td class='sizeFont colorFont'>
                                    <input
                                        type='checkbox'
                                        onchange='presencaMudou(this)'
                                        id='alunos[]'
                                        name={$name}
                                        value={$id}
                                        data-aulaid='1'
                                        {$checked}
                                        autocomplete='off'
                                    >
                                    </td>";
                    $conteudo .= "  <input
                                    type='hidden'
                                    name='justificativa[${id}][qtd]'
                                    style='display: flex;'
                                    value='{$qtdFaltas}'
                                    readonly
                                    autocomplete='off'
                                />";
                    $conteudo .= "  <input
                                    type='hidden'
                                    name='justificativa[${id}][qtdFaltasFreqAntiga]'
                                    style='display: flex;'
                                    value='{$qtdFaltas}'
                                    readonly
                                    autocomplete='off'
                                />";
                    $conteudo .= "  <input
                                    type='hidden'
                                    name='justificativa[${id}][aulas]'
                                    style='display: flex;'
                                    value='{$aulasFaltou}'
                                    readonly
                                    autocomplete='off'
                                />";
                }
                if ($tipo_presenca == 2) {
                 $qtdFaltas = 0;
                 $aulasFaltou = '';

                    for ($i = 1; $i <= count($this->ordens_aulas); $i++) {
                        $checkFalta = in_array($i, $aluno['aulas']);

                        $checked = (!$checkFalta ? "checked='true'" : '');

                        if ($checkFalta) {
                            $aulasFaltou .= $i . ',';
                            $qtdFaltas++;
                        }

                        $conteudo .= "  <td class='sizeFont colorFont'>
                                    <input
                                        type='checkbox'
                                        onchange='presencaMudou(this)'
                                        id='alunos[]'
                                        name={$name}
                                        value={$id}
                                        data-aulaid={$i}
                                        {$checked}
                                        autocomplete='off'
                                    >
                                </td>";
                    }

                    $conteudo .= "  <input
                                    type='hidden'
                                    name='justificativa[${id}][qtd]'
                                    style='display: flex;'
                                    value='{$qtdFaltas}'
                                    readonly
                                    autocomplete='off'
                                />";
                    $conteudo .= "  <input
                                    type='hidden'
                                    name='justificativa[${id}][qtdFaltasFreqAntiga]'
                                    style='display: flex;'
                                    value='{$qtdFaltas}'
                                    readonly
                                    autocomplete='off'
                                />";
                    $conteudo .= "  <input
                                    type='hidden'
                                    name='justificativa[${id}][aulas]'
                                    style='display: flex;'
                                    value='{$aulasFaltou}'
                                    readonly
                                    autocomplete='off'
                                />";
                }
                $conteudo .= "  <td><input
                                    type='text'
                                    name='justificativa[${id}][]'
                                    style='display: flex;'
                                    maxlength=${maxCaracteresObservacao}
                                    value='{$aluno['justificativa']}'
                                    {$disabled}
                                    autocomplete='off'
                                />";

                $conteudo .= '  </td>';
                $conteudo .= '  </tr>';
            }

            if ($conteudo) {
                $alunos .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
                $alunos .= '<tr align="left"><td></td></tr>';
                $alunos .= '<tr align="left"><td>' . $conteudo . '</td></tr>';
                $alunos .= '</table>';
            }
        }

        $this->campoRotulo('alunos_lista_', 'Alunos', "<div id='alunos'>$alunos</div>");
        $this->campoQuebra();

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $obrigatorioRegistroDiarioAtividade = $instituicao['obrigatorio_registro_diario_atividade'];
        $obrigatorioConteudo = $instituicao['permitir_planeja_conteudos'];
        $utilizar_planejamento_aula = $instituicao['utilizar_planejamento_aula'];

        $this->campoMemo('atividades',
            'Registro diário de aula',
            $this->atividades,
            100,
            5,
            $obrigatorioRegistroDiarioAtividade,
            '',
            '',
            false,
            false,
            'onclick',
            false
        );

        if ($obrigatorioConteudo && $utilizar_planejamento_aula) {
            $this->adicionarConteudosMultiplaEscolha();
        }

           // Componente Curricular.


           $this->inputsHelper()->dynamic('frequenciaComponente', ['required' => !$obrigatorio, 'disabled' => !$desabilitado]);




           //end componente



        $this->campoOculto('ano', explode('/', dataToBrasil(NOW()))[2]);
    }

    public function Novo() {
        $this->ordens_aulas = [];

        if (isset($this->ordens_aulas1) && !empty($this->ordens_aulas1)) array_push($this->ordens_aulas, '1');
        if (isset($this->ordens_aulas2) && !empty($this->ordens_aulas2)) array_push($this->ordens_aulas, '2');
        if (isset($this->ordens_aulas3) && !empty($this->ordens_aulas3)) array_push($this->ordens_aulas, '3');
        if (isset($this->ordens_aulas4) && !empty($this->ordens_aulas4)) array_push($this->ordens_aulas, '4');
        if (isset($this->ordens_aulas5) && !empty($this->ordens_aulas5)) array_push($this->ordens_aulas, '5');

        $obj = new clsPmieducarTurma();
        $turmaDetalhes = $obj->lista($this->ref_cod_turma)[0];
        $serie = $turmaDetalhes['ref_ref_cod_serie'];

        $obj = new clsPmieducarSerie();
        $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($serie);

        if ($tipo_presenca == null) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não possui uma regra de avaliação configurada.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        if ($tipo_presenca == 1 && $this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois esta série não admite frequência por componente curricular.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        if ($tipo_presenca == 2 && !$this->ref_cod_componente_curricular) {
            $this->mensagem = 'Cadastro não realizado, pois o componente curricular é obrigatório para esta série.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }


        if (($tipo_presenca == 2 || !empty($this->componente_curricular_registro_individual)) && empty($this->ordens_aulas)) {
            $this->mensagem = 'Cadastro não realizado, pois a quantidade de aulas não foi selecionada.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $data_agora = new DateTime('now');
        $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $data_cadastro =  dataToBanco($this->data);

        $turma = $this->ref_cod_turma;
        $sequencia = $this->fase_etapa;
        $obj = new clsPmieducarTurmaModulo();

        $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia, $turmaDetalhes['ref_ref_cod_escola']);
        if ($data['inicio'] != null && $data['fim'] != null) {
            $data['inicio_periodo_lancamentos'] = explode(',', $data['inicio']);
            $data['fim_periodo_lancamentos'] = explode(',', $data['fim']);

            array_walk($data['inicio_periodo_lancamentos'], function(&$data_inicio, $key) {
                $data_inicio = new \DateTime($data_inicio);
            });

            array_walk($data['fim_periodo_lancamentos'], function(&$data_fim, $key) {
                $data_fim = new \DateTime($data_fim);
            });
        }

        $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
        $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));

        $podeRegistrar = false;
        if (is_array($data['inicio_periodo_lancamentos']) && is_array($data['fim_periodo_lancamentos'])) {
            for ($i=0; $i < count($data['inicio_periodo_lancamentos']); $i++) {
                $data_inicio = $data['inicio_periodo_lancamentos'][$i];
                $data_fim = $data['fim_periodo_lancamentos'][$i];

                $podeRegistrar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

                if ($podeRegistrar) break;
            }
            $podeRegistrar = $podeRegistrar && new DateTime($data_cadastro) >= $data['inicio'] && new DateTime($data_cadastro) <= $data['fim'];
        } else {
            $podeRegistrar = new DateTime($data_cadastro) >= $data['inicio'] && new DateTime($data_cadastro) <= $data['fim'];
            $podeRegistrar = $podeRegistrar && $data['inicio'] >= $data_agora && $data['fim'] <= $data_agora;
        }

        if (!$podeRegistrar) {
            $this->mensagem = 'Cadastro não realizado, pois não é mais possível submeter frequência para esta etapa.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $dataHoje = Carbon::now();
        $dataCadastro = Carbon::parse($data_cadastro);
        $resultData = $dataCadastro->lt($dataHoje);

        if (!$resultData) {
            $this->mensagem = 'Cadastro não realizado, pois a data informada é maior que a data atual.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $utilizarPlanejamentoAula = $instituicao['utilizar_planejamento_aula'];

        if ($utilizarPlanejamentoAula) {
            $componenteCurricular = (isset($this->ref_cod_componente_curricular) && !empty($this->ref_cod_componente_curricular)
                                    ? [$this->ref_cod_componente_curricular]
                                    : null);

            $obj = new clsModulesPlanejamentoAula(
                null,
                $this->ref_cod_turma,
                $componenteCurricular,
                $this->fase_etapa
            );

            $existe = $obj->existeComponenteByData($data_cadastro);

            if (!$existe) {
                $this->mensagem = 'Cadastro não realizado, pois não há planejamento de aula para essa data.<br>';
                $this->simpleRedirect('educar_professores_frequencia_cad.php');
            }
        }

        $servidor_id = $this->pessoa_logada;

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();
        $checaQtdAulasQuadroHorario = $instituicao['checa_qtd_aulas_quadro_horario'];
        $utilizaSabadoAlternado = $instituicao['utiliza_sabado_alternado'];

        $obj_servidor = new clsPmieducarServidor(
            $servidor_id,
            null,
            null,
            null,
            null,
            null,
            1,      //  Ativo
            1,      //  Fixado na instituição de ID 1
        );

        $isProfessor = $obj_servidor->isProfessor();
        $diaSemana =  Carbon::parse($data_cadastro)->dayOfWeek;

        if ($utilizaSabadoAlternado && $diaSemana == 6) {
            $checaQtdAulasQuadroHorario = false;
        }

        if ($checaQtdAulasQuadroHorario && $isProfessor) {
            $diaSemanaConvertido = $this->converterDiaSemanaQuadroHorario($diaSemana);
            $verificacaoQuadroHorario = false;
            $quadroHorario = Portabilis_Business_Professor::quadroHorarioAlocado($this->ref_cod_turma, $servidor_id, $diaSemanaConvertido);

            if(($tipo_presenca == 1 && $diaSemanaConvertido != 1) || ($tipo_presenca == 2 && (count($quadroHorario) > 0 || $diaSemanaConvertido == 7))) {
                $verificacaoQuadroHorario = true;
            }

            if (!$verificacaoQuadroHorario) {
                $this->mensagem = 'Cadastro não realizado, pois a data não está alocada no quadro de horário.<br>';
                $this->simpleRedirect('educar_professores_frequencia_cad.php');
            }
        }


        $obj = new clsModulesFrequencia(
            null,
            null,
            null,
            null,
            null,
            $serie,
            $this->ref_cod_turma,
            $this->ref_cod_componente_curricular,
            null,
            $data_cadastro,
            null,
            null,
            $this->fase_etapa,
            $this->justificativa,
            $servidor_id,
            $this->ordens_aulas,
            (!empty($this->componente_curricular_registro_individual) ? true : null)
        );

        $existe = $obj->existe();

        if ($existe){
            $this->mensagem = 'Cadastro não realizado, pois esta frequência já existe.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        }

        $cadastrou = $obj->cadastra($this->componente_curricular_registro_individual);


        if (!$cadastrou) {
            $this->mensagem = 'Cadastro não realizado.<br>';
            $this->simpleRedirect('educar_professores_frequencia_cad.php');
        } else {
            $obj = new clsModulesComponenteMinistrado(
                null,
                $cadastrou,
                $this->atividades,
                null,
                $this->conteudos,
            );

            $obj->cadastra();
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_det.php?id='.$id_frequencia);
        }

        $this->mensagem = 'Cadastro não realizado.<br>';





        return false;
    }

    public function Editar() {
        $this->data = $this->data_;
        $this->ref_cod_turma = $this->ref_cod_turma_;
        $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;
        $this->fase_etapa = $this->fase_etapa_;

        $obj = new clsPmieducarTurma();
        $turmaDetalhes = $obj->lista($this->ref_cod_turma)[0];
        $serie = $turmaDetalhes['ref_ref_cod_serie'];

        $obj = new clsModulesFrequencia(
            $this->id,
            null,
            null,
            null,
            null,
            $serie,
            null,
            $this->ref_cod_componente_curricular,
            null,
            $this->data,
            null,
            null,
            $this->fase_etapa,
            $this->justificativa,
            null,
            null,
            $this->registra_diario_individual
        );

        $editou = $obj->edita();

        $obj = new clsModulesComponenteMinistrado();
        $componenteMinistrado = $obj->lista(
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
            null,
            $this->id
        )[0];

        if ($componenteMinistrado) {
            $obj = new clsModulesComponenteMinistrado(
                $componenteMinistrado['id'],
                $this->id,
                $this->atividades,
                $componenteMinistrado['observacao'],
                $this->conteudos
            );

            $obj->edita();
        } else {
            $obj = new clsModulesComponenteMinistrado(
                null,
                $this->id,
                $this->atividades,
                null,
                $this->conteudos,
            );

            $obj->cadastra();
        }


        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir () {
        $this->ref_cod_turma = $this->ref_cod_turma_;
        $this->ref_cod_componente_curricular = $this->ref_cod_componente_curricular_;
        $this->fase_etapa = $this->fase_etapa_;

        $obj = new clsPmieducarTurma();
        $serie = $obj->lista($this->ref_cod_turma)[0]['ref_ref_cod_serie'];

        $obj = new clsModulesFrequencia(
            $this->id,
            null,
            null,
            null,
            null,
            $serie,
            null,
            $this->ref_cod_componente_curricular,
            null,
            null,
            null,
            null,
            $this->fase_etapa,
            null
        );

        $excluiu = $obj->excluir();

        $obj = new clsModulesComponenteMinistrado();
        $componenteMinistrado = $obj->lista(
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
            null,
            $this->id
        )[0];

        if ($componenteMinistrado) {
            $obj = new clsModulesComponenteMinistrado($componenteMinistrado['id']);
            $obj->excluir();
        }

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_frequencia_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    function montaListaFrequenciaAlunos ($matriculas, $justificativas, $alunos) {
        if (is_string($matriculas) && !empty($matriculas)) {
            $matriculas = explode(',', $matriculas);
            $justificativas = explode(',', $justificativas);

            for ($i = 0; $i < count($matriculas); $i++) {
                $alunos[$matriculas[$i]]['presenca'] = true;
                $alunos[$matriculas[$i]]['justificativa'] = $justificativas[$i];
            }
        }

        $this->tabela .= ' <tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%"><tr class="tableHeader">';
        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">'+'Nome'+'</span></th>';
        $this->tabela .= ' <th><span style="display: block; float: left; width: 100px; font-weight: bold">'+'Presença'+'</span></th>';
        $this->tabela .= ' <th><span style="display: block; float: left; width: auto; font-weight: bold">'+'Justificativa'+'</span></th>';
        $this->tabela .= ' </tr>';
        $this->tabela .= ' <tr><td class="tableDetalheLinhaSeparador" colspan="3"></td></tr>';

        foreach ($alunos as $aluno) {
            $this->tabela .= '  <td  class="colorFont">';
            $this->tabela .= "  <p>{$aluno['nome']}</p></td>";


            if(!$aluno['presenca']) {
                $this->tabela .= '  <td >
                                        <input type="checkbox" disabled Checked={false}>
                                    </td>';
            } else {
                $this->tabela .= '  <td >
                                        <input type="checkbox" disabled !Checked>
                                    </td>';
                $this->tabela .= "  <td><p>{$aluno['justificativa']}</p></td>";
            }

            $this->tabela .= '  </div>';
            $this->tabela .= '  <br style="clear: left" />';
        }

        $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $this->tabela);
        $disciplinas .= '</table>';

        return $disciplinas;
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/Frequencia.js',
            '/modules/DynamicInput/Assets/Javascripts/TodasTurmas.js',
            '/modules/Cadastro/Assets/Javascripts/ValidacaoEnviarMensagemModal.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $styles = ['/modules/Cadastro/Assets/Stylesheets/frequencia_input.css'];
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    protected function adicionarConteudosMultiplaEscolha() {
        // ESPECIFICAÇÕES
        /*$helperOptions = [
            'objectName' => 'especificacoes',
        ];

        //$todas_especificacoes = $this->getEspecificacoes($this->planejamento_aula_id);

        $options = [
            'label' => 'Especificações',
            'required' => false,
            'options' => [
                'values' => $this->especificacoes,
                'all_values' => /*$todas_especificacoes*//*[]
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);*/

        // CONTEUDOS
        $helperOptions = [
            'objectName' => 'conteudos',
        ];

        $todos_conteudos = $this->getConteudos($this->planejamento_aula_ids);

        $options = [
            'label' => 'Objetivo(s) do conhecimento/conteúdo',
            'required' => true,
            'options' => [
                'values' => $this->conteudos,
                'all_values' => $todos_conteudos
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
    }

    private function getConteudos($planejamento_aula_ids = null)
    {
        if (is_array($planejamento_aula_ids)) {
            $rows = [];

            $obj = new clsModulesPlanejamentoAulaConteudo();
            $conteudos = $obj->listaByPlanejamentos($planejamento_aula_ids);

            foreach ($conteudos as $key => $conteudo) {
                $rows[$conteudo['id']] = $conteudo['conteudo'];
            }

            return $rows;
        }


        return [];
    }

    protected function serviceBoletim($matricula_id, $componente_curricular_id, $turma_id, $reload = false)
    {
        $matriculaId = $matricula_id;

        if (!isset($this->_boletimServiceInstances)) {
            $this->_boletimServiceInstances = [];
        }

        // set service
        if (!isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
            try {
                $params = [
                    'matricula' => $matriculaId,
                    'usuario' => \Illuminate\Support\Facades\Auth::id(),
                    'turmaId' => $turma_id,
                ];

                if (!empty($componente_curricular_id)) {
                    $params['componenteCurricularId'] = $componente_curricular_id;
                }

                $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
            } catch (Exception $e) {
                throw new CoreExt_Exception($e->getMessage());
            }
        }

        // validates service
        if (is_null($this->_boletimServiceInstances[$matriculaId])) {
            throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");
        }

        return $this->_boletimServiceInstances[$matriculaId];
    }

    private function converterDiaSemanaQuadroHorario(int $diaSemana)
    {
        $arrDiasSemanaIeducar = [
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
            6 => 7,
        ];

        return $arrDiasSemanaIeducar[$diaSemana];
    }

    public function Formular () {
        $this->title = 'Frequência - Cadastro';
        $this->processoAp = '58';
    }
};
