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

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'App/Model/IedFinder.php';
require_once 'Calendario/Model/TurmaDataMapper.php';
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Calendário Dia');
    $this->processoAp = 620;
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
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_calendario_ano_letivo;
  var $mes;
  var $dia;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_calendario_dia_motivo;
  var $descricao;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ano;
  var $ref_cod_escola;

  /**
   * @var Calendario_Model_TurmaDataMapper
   */
  var $_calendarioTurmaDataMapper = NULL;

  /**
   * Getter.
   * @access protected
   * @return Calendario_Model_TurmaDataMapper
   */
  function _getCalendarioTurmaDataMapper()
  {
    if (is_null($this->_calendarioTurmaDataMapper)) {
      $this->_calendarioTurmaDataMapper = new Calendario_Model_TurmaDataMapper();
    }
    return $this->_calendarioTurmaDataMapper;
  }

  /**
   * Verifica se existe uma instância de Calendario_Model_Turma.
   *
   * @access protected
   * @param  int   $codCalendarioAnoLetivo Código da chave primária pmieducar.calendario_ano_letivo
   * @param  int   $mes
   * @param  int   $dia
   * @param  int   $ano
   * @param  int   $codTurma Código da chave primária de pmieducar.turma
   * @return bool
   */
  function _hasEntry($codCalendarioAnoLetivo, $mes, $dia, $ano, $codTurma)
  {
    $args = array(
      'calendarioAnoLetivo' => $codCalendarioAnoLetivo,
      'mes'                 => $mes,
      'dia'                 => $dia,
      'ano'                 => $ano,
      'turma'               => $codTurma
    );

    try {
      $this->_getCalendarioTurmaDataMapper()->find($args);
      return TRUE;
    }
    catch (Exception $e) {
    }

    return FALSE;
  }

  /**
   * Retorna um array de instâncias de Calendario_Model_Turma para um dado
   * calendário de ano letivo de escola em mês, dia e ano específicos.
   *
   * @access protected
   * @param  int   $codCalendarioAnoLetivo Código de pmieducar.calendario_ano_letivo
   * @param  int   $mes
   * @param  int   $dia
   * @param  int   $ano
   * @return array (cod_turma => Calendario_Model_Turma)
   */
  function _getEntries($codCalendarioAnoLetivo, $mes, $dia, $ano)
  {
    $where = array(
      'calendarioAnoLetivo' => $codCalendarioAnoLetivo,
      'mes'                 => $mes,
      'dia'                 => $dia,
      'ano'                 => $ano
    );

    $turmas = $this->_getCalendarioTurmaDataMapper()->findAll(array(), $where);

    $ret = array();
    foreach ($turmas as $turma) {
      $ret[$turma->turma] = $turma;
    }

    return $ret;
  }

  function Inicializar()
  {
    $retorno = 'Novo';

    $this->dia = $_GET['dia'];
    $this->mes = $_GET['mes'];
    $this->ref_cod_calendario_ano_letivo = $_GET['ref_cod_calendario_ano_letivo'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7,
      'educar_calendario_dia_lst.php');

    if (is_numeric($this->ref_cod_calendario_ano_letivo) &&
      is_numeric($this->mes) && is_numeric($this->dia)
    ) {
      $obj = new clsPmieducarCalendarioDia($this->ref_cod_calendario_ano_letivo,
        $this->mes, $this->dia);

      $registro  = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_excluir(620, $this->pessoa_logada, 7)) {
          if ($this->descricao) {
            $this->fexcluir = TRUE;
          }
        }

        $retorno = 'Editar';
      }

        $objTemp = new clsPmieducarCalendarioAnoLetivo($this->ref_cod_calendario_ano_letivo);
        $det = $objTemp->detalhe();
        $this->ano = $det['ano'];
    }

    $this->url_cancelar = sprintf(
      'educar_calendario_anotacao_lst.php?ref_cod_calendario_ano_letivo=%d&ano=%d&mes=%d&dia=%d',
      $registro['ref_cod_calendario_ano_letivo'], $this->ano, $registro['mes'], $registro['dia']
    );
    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoRotulo(
      'dia_', 'Dia', sprintf('<b>%d/%d/%d</b>', $this->dia, $this->mes, $this->ano)
    );

    $this->campoOculto(
      'ref_cod_calendario_ano_letivo', $this->ref_cod_calendario_ano_letivo
    );

    $obj_calendario_ano_letivo = new clsPmieducarCalendarioAnoLetivo(
      $this->ref_cod_calendario_ano_letivo
    );

    $det_calendario_ano_letivo = $obj_calendario_ano_letivo->detalhe();
    $ref_cod_escola = $det_calendario_ano_letivo['ref_cod_escola'];

    $this->campoRotulo('ano', 'Ano Letivo', $this->ano);

    $this->campoOculto('mes', $this->mes);
    $this->campoOculto('dia', $this->dia);

    // Foreign keys
    $opcoes = array('' => 'Selecione');
    $objTemp = new clsPmieducarCalendarioDiaMotivo();
    $lista = $objTemp->lista(NULL, $ref_cod_escola, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1
    );

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $opcoes[$registro['cod_calendario_dia_motivo']] = $registro['nm_motivo'];
      }
    }

    $this->campoLista(
      'ref_cod_calendario_dia_motivo', 'Calendário Dia Motivo', $opcoes,
      $this->ref_cod_calendario_dia_motivo, '', FALSE, '', '', FALSE, FALSE
    );

    $seletor = '<label><input id="_turmas_sel" onclick="new ied_forms.checkAll(document, \'formcadastro\', \'turmas\')" type="checkbox" /> Selecionar todas</label>';
    $this->campoRotulo('turmas_rotulo', 'Turmas', $seletor);
    $turmas = App_Model_IedFinder::getTurmas($ref_cod_escola, NULL, $this->ano);

    foreach ($turmas as $codTurma => $nomeTurma) {
      $checked = $this->_hasEntry($this->ref_cod_calendario_ano_letivo,
        $this->mes, $this->dia, $this->ano, $codTurma);

      $this->campoCheck('turmas[' . $codTurma  . ']', '', $checked, $nomeTurma, FALSE);
    }

    $this->campoMemo('descricao', 'Descrição', $this->descricao, 30, 10, TRUE);
  }

  function Novo()
  {
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7,
      'educar_calendario_dia_lst.php');

    $obj = new clsPmieducarCalendarioDia(
      $this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia,
      $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_calendario_dia_motivo,
      $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo
    );

    $cadastrou = $obj->cadastra();

    $auditoria = new clsModulesAuditoriaGeral("calendario_dia", $this->pessoa_logada);
    $auditoria->inclusao($obj->detalhe());

    foreach ($this->turmas as $codTurma => $turma) {
      $calendarioTurma = new Calendario_Model_Turma(array(
        'calendarioAnoLetivo' => $this->ref_cod_calendario_ano_letivo,
        'ano'                 => $this->ano,
        'mes'                 => $this->mes,
        'dia'                 => $this->dia,
        'turma'               => $codTurma
      ));
      $this->_getCalendarioTurmaDataMapper()->save($calendarioTurma);
    }

    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso. <br />';
      $url = sprintf(
        'educar_calendario_anotacao_lst.php?dia=%d&mes=%d&ano=%d&ref_cod_calendario_ano_letivo=%d',
        $this->dia, $this->mes, $this->ano, $this->ref_cod_calendario_ano_letivo
      );
      throw new HttpResponseException(
          new RedirectResponse($url)
      );
    }

    $this->mensagem = 'Cadastro não realizado. <br />';
    return FALSE;
  }

  function Editar()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7,
      'educar_calendario_dia_lst.php');

    $obj = new clsPmieducarCalendarioDia(
      $this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia,
      $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_calendario_dia_motivo,
      $this->descricao, $this->data_cadastro, $this->data_exclusao, 1
    );

    $detalheAntigo = $obj->detalhe();
    $editou = $obj->edita();

    $auditoria = new clsModulesAuditoriaGeral("calendario_dia", $this->pessoa_logada);
    $auditoria->alteracao($detalheAntigo, $obj->detalhe());

    // Inicialização de arrays
    $insert = $delete = $entries = $intersect = array();

    if (isset($this->turmas)) {
      foreach ($this->turmas as $codTurma => $turma) {
        $calendarioTurma = new Calendario_Model_Turma(array(
          'calendarioAnoLetivo' => $this->ref_cod_calendario_ano_letivo,
          'ano'                 => $this->ano,
          'mes'                 => $this->mes,
          'dia'                 => $this->dia,
          'turma'               => $codTurma
        ));
        $insert[$codTurma] = $calendarioTurma;
      }
    }

    // Instâncias persistidas de Calendario_Model_Turma
    $entries = $this->_getEntries($this->ref_cod_calendario_ano_letivo,
      $this->mes, $this->dia, $this->ano);

    // Instâncias para apagar
    $delete = array_diff(array_keys($entries), array_keys($insert));

    // Instâncias já persistidas
    $intersect = array_intersect(array_keys($entries), array_keys($insert));

    foreach ($delete as $id) {
      $this->_getCalendarioTurmaDataMapper()->delete($entries[$id]);
    }

    foreach ($insert as $key => $entry) {
      if (in_array($key, $intersect)) {
        continue;
      }
      $this->_getCalendarioTurmaDataMapper()->save($entry);
    }

    if ($editou) {
      $this->mensagem .= 'Edição efetuada com sucesso. <br />';
      $url = sprintf(
        'educar_calendario_anotacao_lst.php?dia=%d&mes=%d&ano=%d&ref_cod_calendario_ano_letivo=%d',
        $this->dia, $this->mes, $this->ano, $this->ref_cod_calendario_ano_letivo
      );
      throw new HttpResponseException(
        new RedirectResponse($url)
      );
    }

    $this->mensagem = 'Edição não realizada. <br />';
    return FALSE;
  }

  function Excluir()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(620, $this->pessoa_logada, 7,
      'educar_calendario_dia_lst.php');

    $obj = new clsPmieducarCalendarioDia(
      $this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia,
      $this->pessoa_logada, $this->pessoa_logada, NULL, NULL,
      $this->data_cadastro, $this->data_exclusao, 0
    );

    $detalhe = $obj->detalhe();

    $excluiu = $obj->edita();

    $auditoria = new clsModulesAuditoriaGeral("calendario_dia", $this->pessoa_logada);
    $auditoria->exclusao($detalhe);

    $entries = $this->_getEntries(
      $this->ref_cod_calendario_ano_letivo, $this->mes, $this->dia, $this->ano
    );

    foreach ($entries as $entry) {
      $this->_getCalendarioTurmaDataMapper()->delete($entry);
    }

    if ($excluiu) {
      $this->mensagem .= 'Exclusão efetuada com sucesso. <br />';
      $url = sprintf(
        'educar_calendario_anotacao_lst.php?dia=%d&mes=%d&ano=%d&ref_cod_calendario_ano_letivo=%d',
        $this->dia, $this->mes, $this->ano, $this->ref_cod_calendario_ano_letivo
      );
      throw new HttpResponseException(
          new RedirectResponse($url)
      );
    }

    $this->mensagem = 'Exclusão não realizada. <br />';
    return FALSE;
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
