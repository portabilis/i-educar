<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

use App\Services\SchoolLevelsService;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'Avaliacao/Fixups/CleanComponentesCurriculares.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Escola S&eacute;rie');
        $this->processoAp = 585;
        $this->addEstilo("localizacaoSistema");
    }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @todo      Ver a questão de formulários que tem campos dinamicamente
 *   desabilitados de acordo com a requisição (GET, POST ou erro de validação).
 *   A forma atual de usar valores em campos hidden leva a diversos problemas
 *   como aumento da lógica de pré-validação nos métodos Novo() e Editar().
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
    var $pessoa_logada;
    var $ref_cod_escola;
    var $ref_cod_escola_;
    var $ref_cod_serie;
    var $ref_cod_serie_;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $hora_inicial;
    var $hora_final;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $hora_inicio_intervalo;
    var $hora_fim_intervalo;
    var $hora_fim_intervalo_;
    var $ref_cod_instituicao;
    var $ref_cod_curso;
    var $escola_serie_disciplina;
    var $ref_cod_disciplina;
    var $incluir_disciplina;
    var $excluir_disciplina;
    var $disciplinas;
    var $carga_horaria;
    var $etapas_especificas;
    var $etapas_utilizadas;
    var $definirComponentePorEtapa;
    var $anos_letivos;
    var $componente_anos_letivos;

    /**
     * @var SchoolLevelsService
     */
    private $escolaSerieService;

    function Inicializar()
    {
        $retorno = 'Novo';

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $this->escolaSerieService = app(SchoolLevelsService::class);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(585, $this->pessoa_logada, 7, 'educar_escola_serie_lst.php');

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $tmp_obj = new clsPmieducarEscolaSerie();
            $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
            $registro = array_shift($lst_obj);

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->anos_letivos = json_decode($registro['anos_letivos']);

                $this->fexcluir = $obj_permissoes->permissao_excluir(585, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = ($retorno == 'Editar') ? sprintf('educar_escola_serie_det.php?ref_cod_escola=%d&ref_cod_serie=%d', $registro['ref_cod_escola'], $registro['ref_cod_serie']) : 'educar_escola_serie_lst.php';

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME'] . "/intranet" => "In&iacute;cio",
                "educar_index.php" => "Escola",
                "" => "{$nomeMenu} v&iacute;nculo entre escola e s&eacute;rie"
            )
        );

        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = 'Cancelar';
        return $retorno;
    }

    function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $regrasAvaliacao = $this->escolaSerieService->getEvaluationRules($this->ref_cod_serie);
        $anosLetivos = [];
        foreach ($regrasAvaliacao as $regraAvaliacao) {
            $anosLetivos[$regraAvaliacao->pivot->ano_letivo] = $regraAvaliacao->pivot->ano_letivo;
        }

        arsort($anosLetivos);
        $anoLetivoSelected = max($anosLetivos);

        if (request('ano_letivo')) {
            $anoLetivoSelected = request('ano_letivo');
        }

        $this->definirComponentePorEtapa = $this->escolaSerieService->levelAllowDefineDisciplinePerStage(
            $this->ref_cod_serie, $anoLetivoSelected);

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $curso_desabilitado = true;
            $serie_desabilitado = true;
            $escola_serie_desabilitado = true;

            $this->campoOculto('ref_cod_instituicao_', $this->ref_cod_instituicao);
            $this->campoOculto('ref_cod_escola_', $this->ref_cod_escola);
            $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
            $this->campoOculto('ref_cod_serie_', $this->ref_cod_serie);
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

        $opcoes_serie = array('' => 'Selecione');

        // Editar
        if ($this->ref_cod_curso) {
            $obj_serie = new clsPmieducarSerie();
            $obj_serie->setOrderby('nm_serie ASC');
            $lst_serie = $obj_serie->lista(
                array(
                    'ref_cod_curso' => $this->ref_cod_curso,
                    'ativo' => 1
                )
            );

            if (is_array($lst_serie) && count($lst_serie)) {
                foreach ($lst_serie as $serie) {
                    $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
                }
            }
        }

        $this->campoLista(
            'ref_cod_serie',
            'Série',
            $opcoes_serie,
            $this->ref_cod_serie,
            '',
            false,
            '',
            '',
            $this->ref_cod_serie ? true : false
        );

        $helperOptions = [
            'objectName' => 'anos_letivos'
        ];

        $this->anos_letivos = array_values(array_intersect($this->anos_letivos, $this->getAnosLetivosDisponiveis()));

        $options = [
            'label' => 'Anos letivos',
            'required' => true,
            'size' => 50,
            'options' => [
                'values' => $this->anos_letivos,
                'all_values' => $this->getAnosLetivosDisponiveis()
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $this->hora_inicial = substr($this->hora_inicial, 0, 5);
        $this->hora_final = substr($this->hora_final, 0, 5);
        $this->hora_inicio_intervalo = substr($this->hora_inicio_intervalo, 0, 5);
        $this->hora_fim_intervalo = substr($this->hora_fim_intervalo, 0, 5);

        // hora
        $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, false);
        $this->campoHora('hora_final', 'Hora Final', $this->hora_final, false);
        $this->campoHora('hora_inicio_intervalo', 'Hora In&iacute;cio Intervalo', $this->hora_inicio_intervalo, false);
        $this->campoHora('hora_fim_intervalo', 'Hora Fim Intervalo', $this->hora_fim_intervalo, false);
        $this->campoCheck("bloquear_enturmacao_sem_vagas", "Bloquear enturmação após atingir limite de vagas", $this->bloquear_enturmacao_sem_vagas);
        $this->campoCheck("bloquear_cadastro_turma_para_serie_com_vagas", "Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)", $this->bloquear_cadastro_turma_para_serie_com_vagas);
        $this->campoQuebra();

        // Inclui disciplinas
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $obj = new clsPmieducarEscolaSerieDisciplina();
            $registros = $obj->lista($this->ref_cod_serie, $this->ref_cod_escola, null, 1);

            if ($registros) {
                foreach ($registros as $campo) {
                    $this->escola_serie_disciplina[$campo['ref_cod_disciplina']] = $campo['ref_cod_disciplina'];
                    $this->escola_serie_disciplina_carga[$campo['ref_cod_disciplina']] = floatval($campo['carga_horaria']);
                    $this->escola_serie_disciplina_anos_letivos[$campo['ref_cod_disciplina']] = json_decode($campo['anos_letivos']) ?: [];

                    if ($this->definirComponentePorEtapa) {
                        $this->escola_serie_disciplina_etapa_especifica[$campo['ref_cod_disciplina']] = intval($campo['etapas_especificas']);
                        $this->escola_serie_disciplina_etapa_utilizada[$campo['ref_cod_disciplina']] = $campo['etapas_utilizadas'];
                    }
                }
            }
        }

        $opcoes = array('' => 'Selecione');

        // Editar
        $disciplinas = 'Nenhum ano letivo selecionado';

        if ($this->ref_cod_serie) {
            $disciplinas = '';
            $conteudo = '';

            // Instancia o mapper de ano escolar
            $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
            $lista = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

            if (is_array($lista) && count($lista)) {
                $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Nome abreviado</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga horária</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 180px;" >Usar padrão do componente?</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 150px;">Anos letivos</span>';

                if ($this->definirComponentePorEtapa) {
                    $conteudo .= '  <span style="display: block; float: left; margin-left: 30px;">Usado em etapas específicas?(Exemplo: 1,2 / 1,3)</span>';
                }

                $conteudo .= '</div>';
                $conteudo .= '<br style="clear: left" />';
                $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                $conteudo .= "  <label style='display: block; float: left; width: 450px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck(" . '"disciplinas[]"' . ");'/>Marcar Todos</label>";
                $conteudo .= "  <label style='display: block; float: left; width: 330px;'><input type='checkbox' name='CheckTodos2' onClick='marcarCheck(" . '"usar_componente[]"' . ");';/>Marcar Todos</label>";

                if ($this->definirComponentePorEtapa) {
                    $conteudo .= "  <label style='display: block; float: left; width: 100px; margin-left: 84px;'><input type='checkbox' name='CheckTodos3' onClick='marcarCheck(" . '"etapas_especificas[]"' . ");';/>Marcar Todos</label>";
                }

                $conteudo .= '</div>';
                $conteudo .= '<br style="clear: left" />';

                foreach ($lista as $registro) {
                    $checked = '';
                    $checkedEtapaEspecifica = '';
                    $usarComponente = false;
                    $anosLetivosComponente = [];

                    if ($this->escola_serie_disciplina[$registro->id] == $registro->id) {
                        $checked = 'checked="checked"';

                        if ($this->escola_serie_disciplina_etapa_especifica[$registro->id] == "1") {
                            $checkedEtapaEspecifica = 'checked="checked"';
                        }
                    }

                    if (is_null($this->escola_serie_disciplina_carga[$registro->id]) || 0 == $this->escola_serie_disciplina_carga[$registro->id]) {
                        $usarComponente = true;
                    } else {
                        $cargaHoraria = $this->escola_serie_disciplina_carga[$registro->id];
                    }

                    if (!empty($this->escola_serie_disciplina_anos_letivos[$registro->id])) {
                        $anosLetivosComponente = $this->escola_serie_disciplina_anos_letivos[$registro->id];
                    }

                    $cargaComponente = $registro->cargaHoraria;
                    $etapas_utilizadas = $this->escola_serie_disciplina_etapa_utilizada[$registro->id];

                    $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                    $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
                    $conteudo .= "  <span style='display: block; float: left; width: 100px'>{$registro->abreviatura}</span>";
                    $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
                    $conteudo .= "  <label style='display: block; float: left;  width: 180px;'><input type='checkbox' id='usar_componente[]' name='usar_componente[$registro->id]' value='1' " . ($usarComponente == true ? $checked : '') . ">($cargaComponente h)</label>";

                    $conteudo .= "
                            <select name='componente_anos_letivos[{$registro->id}][]'
                                style='width: 150px;'
                                multiple='multiple'> ";

                    foreach ($this->anos_letivos as $anoLetivo) {
                        $seletected = in_array($anoLetivo, $anosLetivosComponente) ? 'selected=selected' : '';
                        $conteudo .= "<option value='{$anoLetivo}' {$seletected}>{$anoLetivo}</option>";
                    }
                    $conteudo .= " </select>";

                    if ($this->definirComponentePorEtapa) {
                        $conteudo .= "  <input style='margin-left:140px; float:left;' type='checkbox' id='etapas_especificas[]' name='etapas_especificas[$registro->id]' value='1' " . ($usarComponente == true ? $checkedEtapaEspecifica : '') . "></label>";
                        $conteudo .= "  <label style='display: block; float: left; width: 100px;'>Etapas utilizadas: <input type='text' class='etapas_utilizadas' name='etapas_utilizadas[$registro->id]' value='{$etapas_utilizadas}' size='5' maxlength='7'></label>";
                    }

                    $conteudo .= '</div>';
                    $conteudo .= '<br style="clear: left" />';

                    $cargaHoraria = '';
                }

                $disciplinas = '<table cellspacing="0" cellpadding="0" border="0">';
                $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
                $disciplinas .= '</table>';
            } else {
                $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
            }

            $this->campoLista(
                'ano_letivo',
                'Ano letivo',
                $anosLetivos,
                $anoLetivoSelected,
                '',
                false,
                'Usado para recuperar a regra de avalição que será usada para verificações dos campos abaixo',
                '',
                false,
                false
            );
        }

        $this->campoRotulo("disciplinas_", "Componentes curriculares", "<div id='disciplinas'>$disciplinas</div>");
        $this->campoQuebra();
    }

    function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

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
            $this->ref_cod_escola,
            $this->ref_cod_serie,
            $this->pessoa_logada,
            $this->pessoa_logada,
            $this->hora_inicial,
            $this->hora_final,
            null,
            null,
            1,
            $this->hora_inicio_intervalo,
            $this->hora_fim_intervalo,
            $this->bloquear_enturmacao_sem_vagas,
            $this->bloquear_cadastro_turma_para_serie_com_vagas,
            $this->anos_letivos ?: []
        );

        if ($obj->existe()) {
            $detalheAntigo = $obj->detalhe();
            $cadastrou = $obj->edita();
            $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
            $auditoria->alteracao($detalheAntigo, $obj->detalhe());
        } else {
            $cadastrou = $obj->cadastra();

            $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
            $auditoria->inclusao($obj->detalhe());
        }

        if ($cadastrou) {
            if ($this->disciplinas) {
                foreach ($this->disciplinas as $key => $campo) {
                    $obj = new clsPmieducarEscolaSerieDisciplina(
                        $this->ref_cod_serie,
                        $this->ref_cod_escola,
                        $campo,
                        1,
                        $this->carga_horaria[$key],
                        $this->etapas_especificas[$key],
                        $this->etapas_utilizadas[$key],
                        $this->componente_anos_letivos[$key] ?: []
                    );

                    if ($obj->existe()) {
                        $cadastrou1 = $obj->edita();
                    } else {
                        $cadastrou1 = $obj->cadastra();
                    }

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
                        echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
                        return false;
                    }
                }
            }

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header('Location: educar_escola_serie_lst.php');
            die();
        }

        $this->mensagem = 'Cadastro n&atilde;o rrealizado.<br>';
        echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerie\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->pessoa_logada ) && ( $this->hora_inicial ) && ( $this->hora_final ) && ( $this->hora_inicio_intervalo ) && ( $this->hora_fim_intervalo )\n-->";
        return false;
    }

    function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

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
            $this->ref_cod_escola,
            $this->ref_cod_serie,
            $this->pessoa_logada,
            null,
            $this->hora_inicial,
            $this->hora_final,
            null,
            null,
            1,
            $this->hora_inicio_intervalo,
            $this->hora_fim_intervalo,
            $this->bloquear_enturmacao_sem_vagas,
            $this->bloquear_cadastro_turma_para_serie_com_vagas,
            $this->anos_letivos ?: []
        );

        $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();

        $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
        $auditoria->alteracao($detalheAntigo, $obj->detalhe());

        $obj = new clsPmieducarEscolaSerieDisciplina(
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $campo,
            1
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

                    $etapas_especificas = $this->etapas_especificas[$key];
                    $etapas_utilizadas = $this->etapas_utilizadas[$key];

                    $obj = new clsPmieducarEscolaSerieDisciplina(
                        $this->ref_cod_serie,
                        $this->ref_cod_escola,
                        $campo,
                        1,
                        $carga_horaria,
                        $etapas_especificas,
                        $etapas_utilizadas,
                        $this->componente_anos_letivos[$key] ?: []
                    );

                    $existe = $obj->existe();

                    if ($existe) {
                        $editou1 = $obj->edita();

                        if (!$editou1) {
                            $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
                            echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
                            return false;
                        }
                    } else {
                        $cadastrou = $obj->cadastra();

                        if (!$cadastrou) {
                            $this->mensagem = 'Cadastro n&atilde;o realizada.<br>';
                            echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
                            return false;
                        }
                    }
                }
            }

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            header('Location: educar_escola_serie_lst.php');
            die();
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
        return false;
    }

    function Excluir()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj = new clsPmieducarEscolaSerie(
            $this->ref_cod_escola_,
            $this->ref_cod_serie_,
            $this->pessoa_logada,
            null,
            null,
            null,
            null,
            null,
            0
        );

        $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
        $auditoria->exclusao($detalhe);

        if ($excluiu) {
            $obj = new clsPmieducarEscolaSerieDisciplina($this->ref_cod_serie_, $this->ref_cod_escola_, null, 0);
            $excluiu1 = $obj->excluirTodos();

            if ($excluiu1) {
                $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
                header("Location: educar_escola_serie_lst.php");
                die();
            }
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarEscolaSerie\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_escola_ ) && is_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $scripts = array(
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/EscolaSerie.js'
        );

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    private function getAnosLetivosDisponiveis()
    {
        $anosLetivosDisponiveis = [];

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_curso)) {
            $objEscolaCurso = new clsPmieducarEscolaCurso($this->ref_cod_escola, $this->ref_cod_curso);
            if ($escolaCurso = $objEscolaCurso->detalhe()) {
                $anosLetivosDisponiveis = json_decode($escolaCurso['anos_letivos']) ?: [];
            }
        }

        return array_combine($anosLetivosDisponiveis, $anosLetivosDisponiveis);
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
