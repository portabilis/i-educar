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
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Arquivo disponível desde a versão 1.0.0
 *
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'clsCalendario.inc.php';

require_once 'Calendario/Model/TurmaDataMapper.php';
require_once 'App/Model/IedFinder.php';
require_once 'include/localizacaoSistema.php';
require_once 'include/pmieducar/clsPmieducarEscolaUsuario.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Calendários');
        $this->addScript('calendario');
        $this->processoAp = 620;
        $this->addEstilo('localizacaoSistema');
    }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class indice extends clsConfig
{
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;

    public $cod_calendario_ano_letivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastra;
    public $data_exclusao;
    public $ativo;
    public $inicio_ano_letivo;
    public $termino_ano_letivo;

    public $ref_cod_instituicao;
    public $ano;
    public $mes;

    public function renderHTML()
    {
        @session_start();

        $this->pessoa_logada = $_SESSION['id_pessoa'];
        $_SESSION['calendario']['ultimo_valido'] = 0;

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7) {
            $retorno .= '
        <table width="100%" height="40%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
          <tbody>';

            $retorno .= '
          <tr>
            <td colspan="2" valig="center" height="50">
              <center class="formdktd">Usuário sem permissão para acessar esta página</center>
            </td>
          </tr>';

            $retorno .= '
          </tbody>
        </table>';

            return $retorno;
        }

        $retorno .= '
      <table width="100%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
        <tbody>';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
         $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
         'educar_index.php'                  => 'Escola',
         ''                                  => 'Calendários'
    ]);
        $this->locale = $localizacao->montar();

        if ($this->locale) {
            $retorno .=  "<tr height='10px'>
                      <td class='fundoLocalizacao' colspan='5'>{$this->locale}</td>
                    </tr>";
        }

        if ($_POST) {
            $this->ref_cod_escola = $_POST['ref_cod_escola'] ?
        $_POST['ref_cod_escola'] : $_SESSION['calendario']['ref_cod_escola'];

            $this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ?
        $_POST['ref_cod_instituicao'] :  $_SESSION['calendario']['ref_cod_instituicao'];

            if ($_POST['mes']) {
                $this->mes = $_POST['mes'];
            }

            if ($_POST['ano']) {
                $this->ano = $_POST['ano'];
            }

            if ($_POST['cod_calendario_ano_letivo']) {
                $this->cod_calendario_ano_letivo = $_POST['cod_calendario_ano_letivo'];
            }
        } elseif (isset($_SESSION['calendario'])) {
            // passa todos os valores em SESSION para atributos do objeto
            foreach ($_SESSION['calendario'] as $var => $val) {
                $this->$var = ($val === '') ? null : $val;
            }
        }

        if ($_GET) {
            header('Location: educar_calendario_ano_letivo_lst.php');
        }

        if (!$this->mes) {
            $this->mes = date('n');
        }

        if (!$this->ano) {
            $this->ano = date('Y');
        }

        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if (! $this->ref_cod_escola) {
            $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
        }

        if (! $this->ref_cod_instituicao) {
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        }

        $get_escola  = 1;
        $obrigatorio = false;

        include 'educar_calendario_pesquisas.php';

        $obj_calendario_ano_letivo = new clsPmieducarCalendarioAnoLetivo();
        $obj_calendario_ano_letivo->setOrderby('ano ASC');
        $obj_calendario_ano_letivo->setLimite($this->limite, $this->offset);

        $lista = [];
        $obj_calendario_ano_letivo->setOrderby('ano');

        switch ($nivel_usuario) {
      // Poli-institucional
      case 1:
      case 2:
      case 4:
        if (!isset($this->ref_cod_escola)) {
            break;
        }

        $lista = $obj_calendario_ano_letivo->lista(
          $this->cod_calendario_ano_letivo,
          $this->ref_cod_escola,
          null,
          null,
          (!isset($this->cod_calendario_ano_letivo) ? $this->ano : null),
          null,
          null,
          1
        );
        break;
    }

        $total = $obj_calendario_ano_letivo->_total;

        if (empty($lista)) {
            if ($nivel_usuario == 4) {
                $retorno .= '<tr><td colspan="2" align="center" class="formdktd">Sem Calendário Letivo</td></tr>';
            } else {
                if ($_POST) {
                    $retorno .= '<tr><td colspan="2" align="center" class="formdktd">Sem Calendário para o ano selecionado</td></tr>';
                } else {
                    $retorno .= '<tr><td colspan="2" align="center" class="formdktd">Selecione uma escola para exibir o calendário</td></tr>';
                }
            }
        }

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $key => $registro) {
                // Guarda dados na $_SESSION
                $_SESSION['calendario'] = [
          'cod_calendario_ano_letivo' => $registro['cod_calendario_ano_letivo'],
          'ref_cod_instituicao'       => $this->ref_cod_instituicao,
          'ref_cod_escola'            => $this->ref_cod_escola,
          'ano'                       => $this->ano,
          'mes'                       => $this->mes
        ];

                // Nome da escola
                $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $registro['nm_escola'] = $det_ref_cod_escola['nome'];

                // Início e término do ano letivo.
                $obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();

                $inicio_ano = $obj_ano_letivo_modulo->menorData(
          $registro['ano'],
            $this->ref_cod_escola
        );

                $fim_ano = $obj_ano_letivo_modulo->maiorData(
          $registro['ano'],
            $this->ref_cod_escola
        );

                $inicio_ano = explode('/', dataFromPgToBr($inicio_ano));
                $fim_ano    = explode('/', dataFromPgToBr($fim_ano));

                // Turmas da escola
                $turmas = App_Model_IedFinder::getTurmas($registro['ref_cod_escola']);

                // Mapper de Calendario_Model_TurmaDataMapper
                $calendarioTurmaMapper = new Calendario_Model_TurmaDataMapper();

                $obj_calendario = new clsCalendario();
                $obj_calendario->setLargura(600);
                $obj_calendario->permite_trocar_ano = true;

                $obj_calendario->setCorDiaSemana([0, 6], 'ROSA');

                $obj_dia_calendario = new clsPmieducarCalendarioDia(
          $registro['cod_calendario_ano_letivo'],
            $this->mes,
            null,
            null,
            null,
          null,
            null
        );

                $lista_dia = $obj_dia_calendario->lista(
          $registro['cod_calendario_ano_letivo'],
            $this->mes,
            null,
            null,
            null,
            null
        );

                if ($lista_dia) {
                    $array_dias      = [];
                    $array_descricao = [];

                    foreach ($lista_dia as $dia) {
                        $descricao = '';

                        $url = sprintf(
              'educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo=%s&ref_cod_escola=%s&dia=%s&mes=%s&ano=%s',
              $registro['cod_calendario_ano_letivo'],
                $this->ref_cod_escola,
              $dia['dia'],
                $dia['mes'],
                $this->ano
            );

                        $botao_editar = sprintf('
              <div style="z-index: 0;">
                <br />
                <input type="button" value="Anotações" onclick="window.location=\'%s\';" class="botaolistagem"/>
              </div>', $url);

                        if ($dia['ref_cod_calendario_dia_motivo']) {
                            $array_dias[$dia['dia']] = $dia['dia'];

                            $obj_motivo = new clsPmieducarCalendarioDiaMotivo($dia['ref_cod_calendario_dia_motivo']);
                            $det_motivo = $obj_motivo->detalhe();

                            /**
                             * @todo CoreExt_Enum?
                             */
                            $tipo = strtoupper($det_motivo['tipo']) == 'E' ?
                'Dia Extra-Letivo' : 'Dia Não Letivo';

                            // Busca pelas turmas que estão marcadas para esse dia
                            $args = [
                'calendarioAnoLetivo' => $registro['cod_calendario_ano_letivo'],
                'mes'                 => $dia['mes'],
                'dia'                 => $dia['dia'],
                'ano'                 => $this->ano
              ];

                            $calendarioTurmas = $calendarioTurmaMapper->findAll([], $args);

                            $nomeTurmas = [];
                            foreach ($calendarioTurmas as $calendarioTurma) {
                                $nomeTurmas[] = $turmas[$calendarioTurma->turma];
                            }

                            if (0 == count($nomeTurmas)) {
                                $calendarioTurmas = '';
                            } else {
                                $calendarioTurmas = 'Turmas: <ul><li>' . implode('</li><li>', $nomeTurmas) . '</li></ul>';
                            }

                            $descricao = sprintf(
                '<div style="z-index: 0;">%s</div><div align="left" style="z-index: 0;">Motivo: %s<br />Descrição: %s<br />%s</div>%s',
                $tipo,
                  $det_motivo['nm_motivo'],
                  $dia['descricao'],
                  $calendarioTurmas,
                  $botao_editar
              );

                            $array_descricao[$dia['dia']] = $descricao;

                            if (strtoupper($det_motivo['tipo']) == 'E') {
                                $obj_calendario->adicionarLegenda('Extra Letivo', 'LARANJA_ESCURO');
                                $obj_calendario->adicionarArrayDias('Extra Letivo', [$dia['dia']]);
                            } elseif (strtoupper($det_motivo['tipo']) == 'N') {
                                $obj_calendario->adicionarLegenda('Não Letivo', '#VERDE_ESCURO');
                                $obj_calendario->adicionarArrayDias('Não Letivo', [$dia['dia']]);
                            }

                            $obj_calendario->diaDescricao($array_dias, $array_descricao);
                        } elseif ($dia['descricao']) {
                            $array_dias[$dia['dia']] = $dia['dia'];

                            $descricao = sprintf(
                '<div style="z-index: 0;">Descrição: %s</div>%s',
                $dia['descricao'],
                  $botao_editar
              );

                            $array_descricao[$dia['dia']] = $descricao;
                        }
                    }

                    if (! empty($array_dias)) {
                        $obj_calendario->diaDescricao($array_dias, $array_descricao);
                    }
                }

                if ($this->mes <= (int) $inicio_ano[1] && $this->ano == (int) $inicio_ano[2]) {
                    if ($this->mes == (int) $inicio_ano[1]) {
                        $obj_calendario->adicionarLegenda('Início Ano Letivo', 'AMARELO');
                        $obj_calendario->adicionarArrayDias('Início Ano Letivo', [$inicio_ano[0]]);
                    }

                    $dia_inicio = (int) $inicio_ano[0];
                    $dias = [];

                    if ($this->mes < (int) $inicio_ano[1]) {
                        $NumeroDiasMes = (int) date('t', $this->mes);

                        for ($d = 1 ; $d <= $NumeroDiasMes; $d++) {
                            $dias[] = $d;
                        }

                        $obj_calendario->setLegendaPadrao('Não Letivo');

                        if (!empty($dias)) {
                            $obj_calendario->adicionarArrayDias('Não Letivo', $dias);
                        }
                    } else {
                        $dia_inicio;

                        for ($d = 1 ; $d < $dia_inicio ; $d++) {
                            $dias[] = $d;
                        }

                        $obj_calendario->setLegendaPadrao('Dias Letivos', 'AZUL_CLARO');
                        if (! empty($dias)) {
                            $obj_calendario->adicionarLegenda('Não Letivo', '#F7F7F7');
                            $obj_calendario->adicionarArrayDias('Não Letivo', $dias);
                        }
                    }
                } elseif ($this->mes >= (int)$fim_ano[1] && $this->ano == (int)$fim_ano[2]) {
                    $dia_inicio = (int)$fim_ano[0];
                    $dias = [];

                    if ($this->mes > (int)$fim_ano[1]) {
                        $NumeroDiasMes = (int) date('t', $this->mes);

                        for ($d = 1 ; $d <= $NumeroDiasMes; $d++) {
                            $dias[] = $d;
                        }

                        $obj_calendario->setLegendaPadrao('Não Letivo');

                        if (! empty($dias)) {
                            $obj_calendario->adicionarArrayDias('Não Letivo', $dias);
                        }
                    } else {
                        $NumeroDiasMes = (int) date('t', $this->mes);

                        for ($d = $fim_ano[0] ; $d <= $NumeroDiasMes; $d++) {
                            $dias[] = $d;
                        }

                        $obj_calendario->setLegendaPadrao('Dias Letivos', 'AZUL_CLARO');

                        if (! empty($dias)) {
                            $obj_calendario->adicionarLegenda('Não Letivo', '#F7F7F7');
                            $obj_calendario->adicionarArrayDias('Não Letivo', $dias);
                        }
                    }

                    if ($this->mes == (int) $fim_ano[1]) {
                        $obj_calendario->adicionarLegenda('Término Ano Letivo', 'AMARELO');
                        $obj_calendario->adicionarArrayDias('Término Ano Letivo', [$fim_ano[0]]);
                    }
                } else {
                    $obj_calendario->setLegendaPadrao('Dias Letivos', 'AZUL_CLARO');
                }

                $obj_calendario->setCorDiaSemana([0, 6], 'ROSA');

                $obj_anotacao = new clsPmieducarCalendarioDiaAnotacao();
                $lista_anotacoes = $obj_anotacao->lista(
          null,
            $this->mes,
            $registro['cod_calendario_ano_letivo'],
            null,
            1
        );

                if ($lista_anotacoes) {
                    $dia_anotacao = [];
                    foreach ($lista_anotacoes as $anotacao) {
                        if ($this->mes == (int) $anotacao['ref_mes']) {
                            $dia_anotacao[$anotacao['ref_dia']] = $anotacao['ref_dia'];
                        }
                    }

                    $obj_calendario->adicionarIconeDias($dia_anotacao, 'A');
                }

                $obj_calendario->all_days_url = sprintf(
          'educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo=%s',
          $registro['cod_calendario_ano_letivo']
        );

                // Gera código HTML do calendário
                $calendario = $obj_calendario->getCalendario(
          $this->mes,
            $this->ano,
            'mes_corrente',
            $_GET,
          ['cod_calendario_ano_letivo' => $registro['cod_calendario_ano_letivo']]
        );

                $retorno .= sprintf(
          '<tr><td colspan="2"><center><b style="font-size:16px;">%s</b>%s</center></td></tr>',
          $registro['nm_escola'],
            $calendario
        );
            }
        }

        if ($obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7)) {
            if ($_POST && empty($lista) && $_SESSION['calendario']['ultimo_valido']) {
                $url = sprintf(
          'educar_calendario_ano_letivo_lst.php?ref_cod_instituicao=%s&ref_cod_escola=%s&ano=%s',
          $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $_SESSION['calendario']['ano']
        );

                $bt_voltar = sprintf(
          '<input type="button" value="Voltar" onclick="window.location=\'%s\';" class="botaolistagem" />',
          $url
        );
            }

            $url = sprintf(
        'educar_calendario_ano_letivo_cad.php?ref_cod_instituicao=%s&ref_cod_escola=%s',
        $this->ref_cod_instituicao,
          $this->ref_cod_escola
      );

            $retorno .= sprintf('
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center" colspan="2">
            %s
            <input type="button" value="Novo Calendário Letivo" onclick="window.location=\'%s\';" class="btn-green botaolistagem" />
          </td>
        </tr>', $bt_voltar, $url);
        }

        $retorno .='
        </tbody>
      </table>';

        return $retorno;
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
