<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;

return new class() extends clsCadastro
{
    public $ref_cod_escola_;

    public $ref_cod_serie;

    public $ref_cod_serie_;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $hora_inicial;

    public $hora_final;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $hora_inicio_intervalo;

    public $hora_fim_intervalo;

    public $hora_fim_intervalo_;

    public $ref_cod_curso;

    public $escola_serie_disciplina;

    public $ref_cod_disciplina;

    public $incluir_disciplina;

    public $excluir_disciplina;

    public $disciplinas;

    public $carga_horaria;

    public $etapas_especificas;

    public $etapas_utilizadas;

    public $definirComponentePorEtapa;

    public $anos_letivos;

    public $componente_anos_letivos;

    private $escolaSerieService;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 585, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_escola_serie_lst.php');

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $tmp_obj = new clsPmieducarEscolaSerie();
            $lst_obj = $tmp_obj->lista(int_ref_cod_escola: $this->ref_cod_escola, int_ref_cod_serie: $this->ref_cod_serie);

            if (!is_array($lst_obj)) {
                $this->mensagem .= 'Registro não localizado.<br>';
                $this->simpleRedirect('educar_escola_serie_lst.php');
            }

            $registro = array_shift($lst_obj);

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->anos_letivos = json_decode($registro['anos_letivos']);

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 585, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? sprintf('educar_escola_serie_det.php?ref_cod_escola=%d&ref_cod_serie=%d', $registro['ref_cod_escola'], $registro['ref_cod_serie']) : 'educar_escola_serie_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' vínculo entre escola e série', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $this->escolaSerieService = app(SchoolLevelsService::class);

        $regrasAvaliacao = $this->escolaSerieService->getEvaluationRules($this->ref_cod_serie);
        $anosLetivos = [];
        foreach ($regrasAvaliacao as $regraAvaliacao) {
            $anosLetivos[$regraAvaliacao->pivot->ano_letivo] = $regraAvaliacao->pivot->ano_letivo;
        }

        arsort($anosLetivos);

        $anoLetivoSelected = request('ano_letivo') ?? (empty($anosLetivos) ? null : max($anosLetivos));

        $this->definirComponentePorEtapa = $this->escolaSerieService->levelAllowDefineDisciplinePerStage(
            $this->ref_cod_serie,
            $anoLetivoSelected
        );

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $curso_desabilitado = true;
            $serie_desabilitado = true;
            $escola_serie_desabilitado = true;

            $this->campoOculto(nome: 'ref_cod_instituicao_', valor: $this->ref_cod_instituicao);
            $this->campoOculto(nome: 'ref_cod_escola_', valor: $this->ref_cod_escola);
            $this->campoOculto(nome: 'ref_cod_curso_', valor: $this->ref_cod_curso);
            $this->campoOculto(nome: 'ref_cod_serie_', valor: $this->ref_cod_serie);
        }

        $obrigatorio = true;
        $get_escola = true;
        $get_curso = true;
        $get_serie = false;
        $get_escola_serie = true;

        include 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola_) {
            $this->ref_cod_escola = $this->ref_cod_escola_;
        }

        if ($this->ref_cod_serie_) {
            $this->ref_cod_serie = $this->ref_cod_serie_;
        }

        $opcoes_serie = ['' => 'Selecione'];

        // Editar
        if ($this->ref_cod_curso) {
            $obj_serie = new clsPmieducarSerie();
            $obj_serie->setOrderby('nm_serie ASC');
            $lst_serie = $obj_serie->lista(
                [
                    'ref_cod_curso' => $this->ref_cod_curso,
                    'ativo' => 1,
                ]
            );

            if (is_array($lst_serie) && count($lst_serie)) {
                foreach ($lst_serie as $serie) {
                    $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
                }
            }
        }

        $this->campoLista(
            nome: 'ref_cod_serie',
            campo: 'Série',
            valor: $opcoes_serie,
            default: $this->ref_cod_serie,
            desabilitado: $this->ref_cod_serie ? true : false
        );

        $helperOptions = [
            'objectName' => 'anos_letivos',
        ];

        $this->anos_letivos = array_values(array_intersect($this->anos_letivos ?? [], $this->getAnosLetivosDisponiveis()));

        $options = [
            'label' => 'Anos letivos',
            'required' => true,
            'size' => 50,
            'options' => [
                'values' => $this->anos_letivos,
                'all_values' => $this->getAnosLetivosDisponiveis(),
            ],
        ];
        $this->inputsHelper()->multipleSearchCustom(attrName: '', inputOptions: $options, helperOptions: $helperOptions);

        $this->hora_inicial = substr(string: $this->hora_inicial, offset: 0, length: 5);
        $this->hora_final = substr(string: $this->hora_final, offset: 0, length: 5);
        $this->hora_inicio_intervalo = substr(string: $this->hora_inicio_intervalo, offset: 0, length: 5);
        $this->hora_fim_intervalo = substr(string: $this->hora_fim_intervalo, offset: 0, length: 5);

        // hora
        $this->campoHora(nome: 'hora_inicial', campo: 'Hora Inicial', valor: $this->hora_inicial);
        $this->campoHora(nome: 'hora_final', campo: 'Hora Final', valor: $this->hora_final);
        $this->campoHora(nome: 'hora_inicio_intervalo', campo: 'Hora Início Intervalo', valor: $this->hora_inicio_intervalo);
        $this->campoHora(nome: 'hora_fim_intervalo', campo: 'Hora Fim Intervalo', valor: $this->hora_fim_intervalo);
        $this->campoCheck(nome: 'bloquear_enturmacao_sem_vagas', campo: 'Bloquear enturmação após atingir limite de vagas', valor: $this->bloquear_enturmacao_sem_vagas);
        $this->campoCheck(nome: 'bloquear_cadastro_turma_para_serie_com_vagas', campo: 'Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)', valor: $this->bloquear_cadastro_turma_para_serie_com_vagas);
        $this->campoQuebra();

        // Inclui disciplinas
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $obj = new clsPmieducarEscolaSerieDisciplina();
            $registros = $obj->lista(int_ref_ref_cod_serie: $this->ref_cod_serie, int_ref_ref_cod_escola: $this->ref_cod_escola, int_ativo: 1);

            if ($registros) {
                $registros = array_map(callback: function ($item) {
                    foreach ($item as $k => $v) {
                        if (is_numeric($k)) {
                            unset($item[$k]);
                        }
                    }

                    $item['anos_letivos'] = json_decode($item['anos_letivos']);
                    $item['carga_horaria'] = (float) $item['carga_horaria'];

                    return $item;
                }, array: $registros);

                $this->campoOculto(nome: 'componentes_sombra', valor: json_encode($registros));

                foreach ($registros as $campo) {
                    $this->escola_serie_disciplina[$campo['ref_cod_disciplina']] = $campo['ref_cod_disciplina'];
                    $this->escola_serie_disciplina_carga[$campo['ref_cod_disciplina']] = (float) $campo['carga_horaria'];
                    $this->escola_serie_disciplina_hora_falta[$campo['ref_cod_disciplina']] = round($campo['hora_falta'] * 60);
                    $this->escola_serie_disciplina_anos_letivos[$campo['ref_cod_disciplina']] = $campo['anos_letivos'] ?: [];

                    if ($this->definirComponentePorEtapa) {
                        $this->escola_serie_disciplina_etapa_especifica[$campo['ref_cod_disciplina']] = intval($campo['etapas_especificas']);
                        $this->escola_serie_disciplina_etapa_utilizada[$campo['ref_cod_disciplina']] = $campo['etapas_utilizadas'];
                    }
                }
            }
        }

        // Editar
        $disciplinas = 'Nenhum ano letivo selecionado';

        if ($this->ref_cod_serie) {
            $disciplinas = '';
            $conteudo = '';
            $lista = LegacyDisciplineAcademicYear::query()
                ->whereGrade($this->ref_cod_serie)
                ->with('discipline')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item['componente_curricular_id'] => $item];
                })->sort();

            if (is_iterable($lista)) {

                $firstKey = $lista->keys()->first();

                $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Nome abreviado</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga horária</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 180px;" >Usar padrão do componente?</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Hora falta (min)</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 180px;" >Usar hora falta padrão do componente?</span>';

                if ($this->definirComponentePorEtapa) {
                    $conteudo .= '  <span style="display: block; width: 280px; float: left; margin-left: 30px;">Usado em etapas específicas?(Exemplo: 1,2 / 1,3)</span>';
                }
                $conteudo .= '  <span style="display: block; float: left; width: 150px;">Anos letivos</span>';

                $conteudo .= '</div>';
                $conteudo .= '<br style="clear: left" />';
                $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                $conteudo .= '  <label style=\'display: block; float: left; width: 250px;\'><input type=\'checkbox\' name=\'CheckTodos\' onClick=\'marcarCheck(' . '"disciplinas[]"' . ');\'/>Marcar Todos</label>';
                $conteudo .= '<label style="display: block; float: left; width: 100px">&nbsp;</label>
                                     <label style="display: block; float: left; width: 100px;">
                                        <a class="clone-values"
                                            onclick="cloneValues(' . $firstKey . ',\'carga_horaria\')">
                                            <i class="fa fa-clone" aria-hidden="true"></i>
                                        </a>
                                     </label>';
                $conteudo .= '  <label style=\'display: block; float: left; width: 280px;\'><input type=\'checkbox\' name=\'CheckTodos2\' onClick=\'marcarCheck(' . '"usar_componente[]"' . ');\';/>Marcar Todos</label>';
                $conteudo .= '  <label style=\'display: block; float: left; width: 180px;\'><input type=\'checkbox\' name=\'CheckTodos4\' onClick=\'marcarCheck(' . '"usar_componente_hora_falta[]"' . ');\';/>Marcar Todos</label>';
                $conteudo .= '<label style="display: block; float: left; width: 0px">&nbsp;</label>';

                if ($this->definirComponentePorEtapa) {
                    $conteudo .= '  <label style=\'display: block; float: left; width: 283px; margin-left: 27px;\'><input type=\'checkbox\' name=\'CheckTodos3\' onClick=\'marcarCheck(' . '"etapas_especificas[]"' . ');\';/>Marcar Todos</label>';
                }

                if ($lista) {
                    $conteudo .= '<label style="display: block; float: left; width: 231px">
                                        <a class="clone-values"
                                            onclick="cloneValues(' . $firstKey . ',\'anos_letivos\')">
                                        <i class="fa fa-clone" aria-hidden="true"></i>
                                        </a>
                                     </label>';
                }

                $conteudo .= '</div>';
                $conteudo .= '<br style="clear: left" />';

                $row = 1;
                foreach ($lista as $registro) {
                    $checked = '';
                    $checkedEtapaEspecifica = '';
                    $usarComponente = false;
                    $usarHoraFaltaComponente = false;
                    $anosLetivosComponente = [];

                    if ($this->escola_serie_disciplina[$registro->id] == $registro->id) {
                        $checked = 'checked="checked"';

                        if ($this->escola_serie_disciplina_etapa_especifica[$registro->id] == '1') {
                            $checkedEtapaEspecifica = 'checked="checked"';
                        }
                    }

                    if (is_null($this->escola_serie_disciplina_carga[$registro->id]) || $this->escola_serie_disciplina_carga[$registro->id] == 0) {
                        $usarComponente = true;
                    } else {
                        $cargaHoraria = $this->escola_serie_disciplina_carga[$registro->id];
                    }

                    if (is_null($this->escola_serie_disciplina_hora_falta[$registro->id]) || $this->escola_serie_disciplina_hora_falta[$registro->id] == 0) {
                        $usarHoraFaltaComponente = true;
                    } else {
                        $horaFalta = $this->escola_serie_disciplina_hora_falta[$registro->id];
                    }

                    if (!empty($this->escola_serie_disciplina_anos_letivos[$registro->id])) {
                        $anosLetivosComponente = $this->escola_serie_disciplina_anos_letivos[$registro->id];
                    }

                    $cargaComponente = (float) $registro->carga_horaria;
                    $etapas_utilizadas = $this->escola_serie_disciplina_etapa_utilizada[$registro->id];

                    $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                    $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" class='check_{$registro->id}' id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro->discipline->nome}</label>";
                    $conteudo .= "  <span style='display: block; float: left; width: 100px'>{$registro->discipline->abreviatura}</span>";
                    $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' class='carga_horaria' id='carga_horaria_{$registro->id}' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7' data-id='$registro->id'></label>";
                    $conteudo .= "  <label style='display: block; float: left;  width: 180px;'><input type='checkbox' id='usar_componente[]' name='usar_componente[$registro->id]' value='1' " . ($usarComponente == true ? $checked : '') . ">($cargaComponente h)</label>";
                    $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' class='hora_falta' id='hora_falta_{$registro->id}' name='hora_falta[$registro->id]' value='{$horaFalta}' size='5' maxlength='7' data-id='$registro->id'></label>";
                    $conteudo .= "  <label style='display: block; float: left;  width: 180px;'><input type='checkbox' id='usar_componente_hora_falta[]' name='usar_componente_hora_falta[$registro->id]' value='1' " . ($usarHoraFaltaComponente == true ? $checked : '') . '></label>';

                    $conteudo .= "
                            <select name='componente_anos_letivos[{$registro->id}][]'
                                style='width: 150px;'
                                multiple='multiple' class='anos_letivos' id='anos_letivos_{$registro->id}' data-id='$registro->id'> ";

                    foreach ($this->anos_letivos as $anoLetivo) {
                        $seletected = in_array(needle: $anoLetivo, haystack: $anosLetivosComponente, strict: true) ? 'selected=selected' : '';
                        $conteudo .= "<option value='{$anoLetivo}' {$seletected}>{$anoLetivo}</option>";
                    }
                    $conteudo .= ' </select>';

                    if ($this->definirComponentePorEtapa) {
                        $conteudo .= "  <input style='margin-left:30px; float:left;margin-top: 13px' type='checkbox' id='etapas_especificas[]' name='etapas_especificas[$registro->id]' value='1' " . $checkedEtapaEspecifica . '></label>';
                        $conteudo .= "  <label style='display: block; float: left; width: 260px;'>Etapas utilizadas: <input type='text' class='etapas_utilizadas' name='etapas_utilizadas[$registro->id]' value='{$etapas_utilizadas}' size='5' maxlength='7'></label>";
                    }

                    $row++;

                    $conteudo .= '</div>';

                    $conteudo .= '<br style="clear: left" />';

                    $cargaHoraria = '';
                    $horaFalta = '';
                }

                $disciplinas = '<table cellspacing="0" cellpadding="0" border="0">';
                $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
                $disciplinas .= '</table>';
            } else {
                $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
            }

            $this->campoLista(
                nome: 'ano_letivo',
                campo: 'Ano letivo',
                valor: $anosLetivos,
                default: $anoLetivoSelected,
                descricao: 'Usado para recuperar a regra de avalição que será usada para verificações dos campos abaixo',
                obrigatorio: false
            );
        }

        $this->campoRotulo(nome: 'disciplinas_', campo: 'Componentes curriculares', valor: "<div id='disciplinas'>$disciplinas</div>");
        $this->campoQuebra();

        $obj_permissoes = new clsPermissoes();
        $permissaoConsultaDispensas = $obj_permissoes->permissao_cadastra(int_processo_ap: Process::EXEMPTION_LIST, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: null);
        $this->campoOculto(nome: 'permissao_consulta_dispensas', valor: intval($permissaoConsultaDispensas));
    }

    public function Novo()
    {
        /*
         * Se houve erro na primeira tentativa de cadastro, irá considerar apenas
         * os valores enviados de forma oculta.
         */
        if (isset($this->ref_cod_instituicao_)) {
            $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
            $this->ref_cod_escola = $this->ref_cod_escola_;
            $this->ref_cod_curso = $this->ref_cod_curso_;
            $this->ref_cod_serie = $this->ref_cod_serie_;
        }

        $this->bloquear_enturmacao_sem_vagas = is_null($this->bloquear_enturmacao_sem_vagas) ? 0 : 1;
        $this->bloquear_cadastro_turma_para_serie_com_vagas = is_null($this->bloquear_cadastro_turma_para_serie_com_vagas) ? 0 : 1;

        $obj = new clsPmieducarEscolaSerie(
            ref_cod_escola: $this->ref_cod_escola,
            ref_cod_serie: $this->ref_cod_serie,
            ref_usuario_exc: $this->pessoa_logada,
            ref_usuario_cad: $this->pessoa_logada,
            hora_inicial: $this->hora_inicial,
            hora_final: $this->hora_final,
            ativo: 1,
            hora_inicio_intervalo: $this->hora_inicio_intervalo,
            hora_fim_intervalo: $this->hora_fim_intervalo,
            bloquear_enturmacao_sem_vagas: $this->bloquear_enturmacao_sem_vagas,
            bloquear_cadastro_turma_para_serie_com_vagas: $this->bloquear_cadastro_turma_para_serie_com_vagas,
            anos_letivos: $this->anos_letivos ?: []
        );

        if ($obj->existe()) {
            $cadastrou = $obj->edita();
        } else {
            $cadastrou = $obj->cadastra();
        }

        if ($cadastrou) {
            if ($this->disciplinas) {
                foreach ($this->disciplinas as $key => $campo) {
                    $obj = new clsPmieducarEscolaSerieDisciplina(
                        ref_ref_cod_serie: $this->ref_cod_serie,
                        ref_ref_cod_escola: $this->ref_cod_escola,
                        ref_cod_disciplina: $campo,
                        ativo: 1,
                        carga_horaria: $this->carga_horaria[$key],
                        etapas_especificas: $this->etapas_especificas[$key],
                        etapas_utilizadas: $this->etapas_utilizadas[$key],
                        anos_letivos: $this->componente_anos_letivos[$key] ?: [],
                        hora_falta: empty($this->hora_falta[$key]) ? null : $this->hora_falta[$key] / 60
                    );

                    if ($obj->existe()) {
                        $cadastrou1 = $obj->edita();
                    } else {
                        $cadastrou1 = $obj->cadastra();
                    }

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro não realizado.<br>';

                        return false;
                    }
                }
            }

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_escola_serie_lst.php');
        }

        $this->mensagem = 'Cadastro não rrealizado.<br>';

        return false;
    }

    public function Editar()
    {
        /*
         * Atribui valor para atributos usados em Gerar(), senão o formulário volta
         * a liberar os campos Instituição, Escola e Curso que devem ser read-only
         * quando em modo de edição
         */
        $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
        $this->ref_cod_escola = $this->ref_cod_escola_;
        $this->ref_cod_curso = $this->ref_cod_curso_;
        $this->ref_cod_serie = $this->ref_cod_serie_;

        $this->bloquear_enturmacao_sem_vagas = is_null($this->bloquear_enturmacao_sem_vagas) ? 0 : 1;
        $this->bloquear_cadastro_turma_para_serie_com_vagas = is_null($this->bloquear_cadastro_turma_para_serie_com_vagas) ? 0 : 1;

        $obj = new clsPmieducarEscolaSerie(
            ref_cod_escola: $this->ref_cod_escola,
            ref_cod_serie: $this->ref_cod_serie,
            ref_usuario_exc: $this->pessoa_logada,
            hora_inicial: $this->hora_inicial,
            hora_final: $this->hora_final,
            ativo: 1,
            hora_inicio_intervalo: $this->hora_inicio_intervalo,
            hora_fim_intervalo: $this->hora_fim_intervalo,
            bloquear_enturmacao_sem_vagas: $this->bloquear_enturmacao_sem_vagas,
            bloquear_cadastro_turma_para_serie_com_vagas: $this->bloquear_cadastro_turma_para_serie_com_vagas,
            anos_letivos: $this->anos_letivos ?: []
        );

        $sombra = json_decode(json: urldecode($this->componentes_sombra), associative: true) ?? [];
        $disciplinas = $this->montaDisciplinas();
        $analise = $this->analisaAlteracoes(originais: $sombra, novos: $disciplinas);

        try {
            $this->validaAlteracoes($analise);
        } catch (Exception $e) {
            $this->mensagem = explode(separator: "\n", string: $e->getMessage());

            $this->simpleRedirect(\Request::getRequestUri());
        }

        $editou = $obj->edita();

        $obj = new clsPmieducarEscolaSerieDisciplina(
            ref_ref_cod_serie: $this->ref_cod_serie,
            ref_ref_cod_escola: $this->ref_cod_escola,
            ref_cod_disciplina: null,
            ativo: 1
        );

        $obj->excluirNaoSelecionados($this->disciplinas);

        if ($editou) {
            if ($this->disciplinas) {
                foreach ($this->disciplinas as $key => $campo) {
                    if (isset($this->usar_componente[$key])) {
                        $carga_horaria = null;
                    } else {
                        $carga_horaria = $this->carga_horaria[$key];
                    }

                    if (isset($this->usar_componente_hora_falta[$key])) {
                        $hora_falta = null;
                    } else {
                        $hora_falta = $this->hora_falta[$key];
                    }

                    $etapas_especificas = $this->etapas_especificas[$key];
                    $etapas_utilizadas = $this->etapas_utilizadas[$key];

                    $obj = new clsPmieducarEscolaSerieDisciplina(
                        ref_ref_cod_serie: $this->ref_cod_serie,
                        ref_ref_cod_escola: $this->ref_cod_escola,
                        ref_cod_disciplina: $campo,
                        ativo: 1,
                        carga_horaria: $carga_horaria,
                        etapas_especificas: $etapas_especificas,
                        etapas_utilizadas: $etapas_utilizadas,
                        anos_letivos: $this->componente_anos_letivos[$key] ?: [],
                        hora_falta: empty($hora_falta) ? null : $hora_falta / 60
                    );

                    $existe = $obj->existe();

                    if ($existe) {
                        $editou1 = $obj->edita();

                        if (!$editou1) {
                            $this->mensagem = 'Edição não realizada.<br>';

                            return false;
                        }
                    } else {
                        $cadastrou = $obj->cadastra();

                        if (!$cadastrou) {
                            $this->mensagem = 'Cadastro não realizada.<br>';

                            return false;
                        }
                    }
                }
            }

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_escola_serie_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarEscolaSerie(
            ref_cod_escola: $this->ref_cod_escola_,
            ref_cod_serie: $this->ref_cod_serie_,
            ref_usuario_exc: $this->pessoa_logada,
            ativo: 0
        );

        $objEscolaSerieDisciplina = new clsPmieducarEscolaSerieDisciplina(
            ref_ref_cod_serie: $this->ref_cod_serie_,
            ref_ref_cod_escola: $this->ref_cod_escola_,
            ref_cod_disciplina: null,
            ativo: 1
        );

        $existeDependencia = $objEscolaSerieDisciplina->existeDependencia(listaComponentesSelecionados: $this->disciplinas, exclusao: true);

        if ($existeDependencia) {
            $this->mensagem = 'Não foi possível remover o componente. Existe registros de dependência neste componente.<br>';
            $this->simpleRedirect("educar_escola_serie_cad.php?ref_cod_escola={$this->ref_cod_escola_}&ref_cod_serie={$this->ref_cod_serie_}");

            return false;
        }

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $obj = new clsPmieducarEscolaSerieDisciplina(ref_ref_cod_serie: $this->ref_cod_serie_, ref_ref_cod_escola: $this->ref_cod_escola_, ref_cod_disciplina: null, ativo: 0);
            $excluiu1 = $obj->excluirTodos();

            if ($excluiu1) {
                $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
                $this->simpleRedirect('educar_escola_serie_lst.php');
            }
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $scripts = [
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/EscolaSerie.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/ModalDispensas.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    private function getAnosLetivosDisponiveis()
    {
        $anosLetivosDisponiveis = [];

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso)) {
            $objEscolaCurso = new clsPmieducarEscolaCurso(ref_cod_escola: $this->ref_cod_escola, ref_cod_curso: $this->ref_cod_curso);
            if ($escolaCurso = $objEscolaCurso->detalhe()) {
                $anosLetivosDisponiveis = json_decode($escolaCurso['anos_letivos']) ?: [];
            }
        }

        return array_combine(keys: $anosLetivosDisponiveis, values: $anosLetivosDisponiveis);
    }

    private function montaDisciplinas()
    {
        $disciplinas = [];

        foreach ($this->disciplinas as $componenteId) {
            if (isset($this->usar_componente[$componenteId])) {
                $carga_horaria = null;
            } else {
                $carga_horaria = $this->carga_horaria[$componenteId];
            }

            if (isset($this->usar_componente_hora_falta[$componenteId])) {
                $hora_falta = null;
            } else {
                $hora_falta = $this->hora_falta[$componenteId];
            }

            $anosLetivos = $this->componente_anos_letivos[$componenteId] ?: [];
            $anosLetivos = array_map(callback: function ($ano) {
                return (int) $ano;
            }, array: $anosLetivos);

            $disciplinas[] = [
                'ref_ref_cod_serie' => $this->ref_cod_serie,
                'ref_ref_cod_escola' => $this->ref_cod_escola,
                'ref_cod_disciplina' => $componenteId,
                'carga_horaria' => $carga_horaria,
                'hora_falta' => $hora_falta,
                'etapas_especificas' => $this->etapas_especificas[$componenteId],
                'etapas_utilizadas' => $this->etapas_utilizadas[$componenteId],
                'anos_letivos' => $anosLetivos,
            ];
        }

        return $disciplinas;
    }

    private function analisaAlteracoes($originais, $novos)
    {
        $remover = [];
        $inserir = [];
        $atualizar = [];

        foreach ($novos as $novo) {
            $original = Arr::where(array: $originais, callback: function ($v, $k) use ($novo) {
                return (int) $v['ref_cod_disciplina'] === (int) $novo['ref_cod_disciplina'];
            });

            if ($original) {
                $key = array_keys($original)[0];
                $original = $original[$key];
            } else {
                $original = null;
            }

            if (is_null($original)) {
                $inserir[] = $novo;

                continue;
            }

            $novo['anos_letivos_acrescentar'] = array_diff($novo['anos_letivos'], $original['anos_letivos']);
            $novo['anos_letivos_remover'] = array_diff($original['anos_letivos'], $novo['anos_letivos']);

            $atualizar[] = $novo;
        }

        $idsOriginais = Arr::pluck(array: $originais, value: 'ref_cod_disciplina');
        $idsNovos = Arr::pluck(array: $novos, value: 'ref_cod_disciplina');
        $remover = array_diff($idsOriginais, $idsNovos);

        return compact('remover', 'inserir', 'atualizar');
    }

    private function validaAlteracoes($analise)
    {
        $erros = [];
        $iDiarioService = $this->getIdiarioService();

        if ($analise['inserir']) {
            foreach ($analise['inserir'] as $insert) {
                $anos = $insert['anos_letivos'] ?? [];
                $componente = Portabilis_Utils_Database::fetchPreparedQuery(
                    sql: 'SELECT nome FROM modules.componente_curricular WHERE id = $1',
                    options: ['params' => [(int) $insert['ref_cod_disciplina']]]
                )[0]['nome'];

                foreach ($anos as $ano) {
                    $info = Portabilis_Utils_Database::fetchPreparedQuery(sql: '
                        SELECT COUNT(*)
                        FROM modules.componente_curricular_ano_escolar
                        WHERE TRUE
                            AND componente_curricular_id = $1
                            AND ano_escolar_id = $2
                            AND $3 = ANY(anos_letivos)
                    ', options: ['params' => [
                        (int) $insert['ref_cod_disciplina'],
                        $this->ref_cod_serie,
                        $ano,
                    ]]);

                    $count = (int) $info[0]['count'] ?? 0;

                    if ($count < 1) {
                        $erros[] = sprintf('O ano %d de "%s" precisa estar devidamente cadastrado nesta série em Componentes da série (Escola > Cadastros > Tipos > Séries > Componentes da série).', $ano, $componente);
                    }
                }
            }
        }

        if ($analise['remover']) {
            $service = new CheckPostedDataService();
            $schoolClass = LegacyGrade::find($this->ref_cod_serie)->schoolClass()
                ->where('ref_ref_cod_escola', $this->ref_cod_escola)
                ->pluck('cod_turma');

            foreach ($analise['remover'] as $componenteId) {
                $info = Portabilis_Utils_Database::fetchPreparedQuery(sql: '
                    SELECT COUNT(cct.*), cc.nome
                    FROM modules.componente_curricular_turma cct
                    INNER JOIN modules.componente_curricular cc ON cc.id = cct.componente_curricular_id
                    INNER JOIN pmieducar.turma t ON t.cod_turma = cct.turma_id
                    WHERE TRUE
                        AND cct.componente_curricular_id = $1
                        AND cct.ano_escolar_id = $2
                        AND cct.escola_id = $3
                        AND t.ativo = 1
                    GROUP BY cc.nome
                ', options: ['params' => [
                    (int) $componenteId,
                    $this->ref_cod_serie,
                    $this->ref_cod_escola,
                ]]);

                $count = (int) $info[0]['count'] ?? 0;

                if ($count > 0) {
                    $erros[] = sprintf('Não é possível desvincular "%s" pois existem turmas vinculadas a este componente.', $info[0]['nome']);
                }

                $hasDataPosted = $service->hasDataPostedInGrade(discipline: (int) $componenteId, level: $this->ref_cod_serie, school: $this->ref_cod_escola);

                if ($hasDataPosted) {
                    $discipline = LegacyDiscipline::find((int) $componenteId);
                    $erros[] = sprintf('Não é possível desvincular "%s" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série e escola.', $discipline->nome);
                }

                if ($iDiarioService && $schoolClass->count() && $iDiarioService->getClassroomsActivityByDiscipline(classroomId: $schoolClass->toArray(), disciplineId: $componenteId)) {
                    $discipline = LegacyDiscipline::find($componenteId);
                    $erros[] = sprintf('Não é possível desvincular "%s" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série e escola no iDiário', $discipline->nome);
                }
            }
        }

        if ($analise['atualizar']) {
            $service = new CheckPostedDataService();
            foreach ($analise['atualizar'] as $update) {
                if (!empty($update['anos_letivos_remover'])) {
                    foreach ($update['anos_letivos_remover'] as $ano) {
                        $hasDataPosted = $service->hasDataPostedInGrade(discipline: (int) $update['ref_cod_disciplina'], level: $this->ref_cod_serie, year: $ano, school: $this->ref_cod_escola);

                        if ($hasDataPosted) {
                            $discipline = LegacyDiscipline::find((int) $update['ref_cod_disciplina']);
                            $erros[] = sprintf('Não é possível desvincular o ano %d de "%s" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série, ano e escola.', $ano, $discipline->nome);
                        }

                        $schoolClass = LegacyGrade::find($this->ref_cod_serie)->schoolClass()
                            ->where('ref_ref_cod_escola', $this->ref_cod_escola)
                            ->where('ano', $ano)
                            ->pluck('cod_turma');

                        if ($iDiarioService && $schoolClass->count() && $iDiarioService->getClassroomsActivityByDiscipline(classroomId: $schoolClass->toArray(), disciplineId: $update['ref_cod_disciplina'])) {
                            $discipline = LegacyDiscipline::find($update['ref_cod_disciplina']);
                            $erros[] = sprintf('Não é possível desvincular o ano %d de "%s" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série, ano e escola no iDiário', $ano, $discipline->nome);
                        }
                    }
                }

                if (!empty($update['anos_letivos_acrescentar'])) {
                    $componente = Portabilis_Utils_Database::fetchPreparedQuery(
                        sql: 'SELECT nome FROM modules.componente_curricular WHERE id = $1',
                        options: ['params' => [(int) $update['ref_cod_disciplina']]]
                    )[0]['nome'];

                    foreach ($update['anos_letivos_acrescentar'] as $ano) {
                        $info = Portabilis_Utils_Database::fetchPreparedQuery(sql: '
                            SELECT COUNT(*)
                            FROM modules.componente_curricular_ano_escolar
                            WHERE TRUE
                                AND componente_curricular_id = $1
                                AND ano_escolar_id = $2
                                AND $3 = ANY(anos_letivos)
                        ', options: ['params' => [
                            (int) $update['ref_cod_disciplina'],
                            $this->ref_cod_serie,
                            $ano,
                        ]]);

                        $count = (int) $info[0]['count'] ?? 0;

                        if ($count < 1) {
                            $erros[] = sprintf('O ano %d de "%s" precisa estar devidamente cadastrado nesta série em Componentes da série (Escola > Cadastros > Tipos > Séries > Componentes da série).', $ano, $componente);
                        }
                    }
                }
            }
        }

        if ($erros) {
            $msg = implode(separator: "\n", array: $erros);

            throw new \Exception($msg);
        }

        return true;
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

    public function Formular()
    {
        $this->title = 'Escola Série';
        $this->processoAp = 585;
    }
};
