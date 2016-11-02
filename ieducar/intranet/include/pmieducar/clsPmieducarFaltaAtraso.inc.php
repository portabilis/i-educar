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

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarFaltaAtraso class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarFaltaAtraso
{
  var $cod_falta_atraso;
  var $ref_cod_escola;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_servidor;
  var $tipo;
  var $data_falta_atraso;
  var $qtd_horas;
  var $qtd_min;
  var $justificada;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function clsPmieducarFaltaAtraso($cod_falta_atraso = NULL, $ref_cod_escola = NULL,
    $ref_ref_cod_instituicao = NULL, $ref_usuario_exc = NULL, $ref_usuario_cad = NULL,
    $ref_cod_servidor = NULL, $tipo = NULL, $data_falta_atraso = NULL,
    $qtd_horas = NULL, $qtd_min = NULL, $justificada = NULL, $data_cadastro = NULL,
    $data_exclusao = NULL, $ativo = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'falta_atraso';

    $this->_campos_lista = $this->_todos_campos = 'cod_falta_atraso, ref_cod_escola, ref_ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_cod_servidor, tipo, data_falta_atraso, qtd_horas, qtd_min, justificada, data_cadastro, data_exclusao, ativo';

    if (is_numeric($ref_cod_escola)) {
      if (class_exists('clsPmieducarEscola')) {
        $tmp_obj = new clsPmieducarEscola($ref_cod_escola);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_escola = $ref_cod_escola;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_escola = $ref_cod_escola;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'")) {
          $this->ref_cod_escola = $ref_cod_escola;
        }
      }
    }

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }
    }

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
        if (method_exists( $tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
      if (class_exists('clsPmieducarServidor')) {
        $tmp_obj = new clsPmieducarServidor($ref_cod_servidor, NULL, NULL, NULL,
          NULL, NULL, NULL, NULL, $ref_ref_cod_instituicao);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe() ) {
            $this->ref_cod_servidor        = $ref_cod_servidor;
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_servidor        = $ref_cod_servidor;
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
          }
        }

        $this->ref_cod_servidor        = $ref_cod_servidor;
        $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_cod_servidor}' AND ref_cod_instituicao = '{$ref_ref_cod_instituicao}'")) {
          $this->ref_cod_servidor = $ref_cod_servidor;
          $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
        }
      }
    }

    if (is_numeric($cod_falta_atraso)) {
      $this->cod_falta_atraso = $cod_falta_atraso;
    }

    if (is_numeric($tipo)) {
      $this->tipo = $tipo;
    }

    if (is_string($data_falta_atraso)) {
      $this->data_falta_atraso = $data_falta_atraso;
    }

    if (is_numeric($qtd_horas)) {
      $this->qtd_horas = $qtd_horas;
    }

    if (is_numeric($qtd_min)) {
      $this->qtd_min = $qtd_min;
    }

    if (is_numeric($justificada)) {
      $this->justificada = $justificada;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) &&
      is_numeric($this->ref_cod_servidor) && is_numeric($this->tipo) &&
      is_string($this->data_falta_atraso) && is_numeric($this->justificada)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric( $this->ref_cod_escola)) {
        $campos  .= "{$gruda}ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_cod_escola}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->ref_ref_cod_instituicao)) {
        $campos  .= "{$gruda}ref_ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->ref_usuario_cad)) {
        $campos  .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->ref_cod_servidor)) {
        $campos  .= "{$gruda}ref_cod_servidor";
        $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->tipo)) {
        $campos  .= "{$gruda}tipo";
        $valores .= "{$gruda}'{$this->tipo}'";
        $gruda    = ', ';
      }

      if (is_string( $this->data_falta_atraso)) {
        $campos  .= "{$gruda}data_falta_atraso";
        $valores .= "{$gruda}'{$this->data_falta_atraso}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->qtd_horas)) {
        $campos  .= "{$gruda}qtd_horas";
        $valores .= "{$gruda}'{$this->qtd_horas}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->qtd_min)) {
        $campos  .= "{$gruda}qtd_min";
        $valores .= "{$gruda}'{$this->qtd_min}'";
        $gruda    = ', ';
      }

      if (is_numeric( $this->justificada)) {
        $campos  .= "{$gruda}justificada";
        $valores .= "{$gruda}'{$this->justificada}'";
        $gruda    = ', ';
      }

      $campos  .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda    = ', ';

      $campos  .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda    = ', ';

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");
      return $db->InsertId("{$this->_tabela}_cod_falta_atraso_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_falta_atraso) && is_numeric($this->ref_usuario_exc)){
      $db = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_cod_escola)) {
        $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_ref_cod_instituicao)) {
        $set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_servidor)) {
        $set .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->tipo)) {
        $set .= "{$gruda}tipo = '{$this->tipo}'";
        $gruda    = ', ';
      }

      if (is_string($this->data_falta_atraso)) {
        $set .= "{$gruda}data_falta_atraso = '{$this->data_falta_atraso}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->qtd_horas)) {
        $set .= "{$gruda}qtd_horas = '{$this->qtd_horas}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->qtd_min)) {
        $set .= "{$gruda}qtd_min = '{$this->qtd_min}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->justificada)) {
        $set .= "{$gruda}justificada = '{$this->justificada}'";
        $gruda    = ', ';
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda    = ', ';
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda    = ', ';

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda    = ', ';
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_falta_atraso = '{$this->cod_falta_atraso}'");
        return true;
      }
    }
    return false;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_falta_atraso = null, $int_ref_cod_escola = null,
    $int_ref_ref_cod_instituicao = null, $int_ref_usuario_exc = null,
    $int_ref_usuario_cad = null, $int_ref_cod_servidor = null, $int_tipo = null,
    $date_data_falta_atraso_ini = null, $date_data_falta_atraso_fim = null,
    $int_qtd_horas = null, $int_qtd_min = null, $int_justificada = null,
    $date_data_cadastro_ini = null, $date_data_cadastro_fim = null,
    $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null
  ) {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_falta_atraso)) {
      $filtros .= "{$whereAnd} cod_falta_atraso = '{$int_cod_falta_atraso}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_servidor)) {
      $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_tipo)) {
      $filtros .= "{$whereAnd} tipo = '{$int_tipo}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_falta_atraso_ini)) {
      $filtros .= "{$whereAnd} data_falta_atraso >= '{$date_data_falta_atraso_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_falta_atraso_fim)) {
      $filtros .= "{$whereAnd} data_falta_atraso <= '{$date_data_falta_atraso_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_qtd_horas)) {
      $filtros .= "{$whereAnd} qtd_horas = '{$int_qtd_horas}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_qtd_min)) {
      $filtros .= "{$whereAnd} qtd_min = '{$int_qtd_min}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_justificada)) {
      $filtros .= "{$whereAnd} justificada = '{$int_justificada}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} ativo = '1'";
      $whereAnd = ' AND ';
    }
    else {
      $filtros .= "{$whereAnd} ativo = '0'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_falta_atraso)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_falta_atraso = '{$this->cod_falta_atraso}'" );
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_falta_atraso)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_falta_atraso = '{$this->cod_falta_atraso}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_falta_atraso) && is_numeric($this->ref_usuario_exc)) {
      $this->ativo = 0;
      return $this->edita();
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   * @todo Remover método (não utilizado)
   */
  function listaHorasEscola($int_ref_cod_servidor = null,
    $int_ref_ref_cod_instituicao = null, $int_ref_cod_escola = null)
  {
    $sql = '
      SELECT
        SUM(qtd_horas) AS horas,
        SUM(qtd_min) AS minutos,
        ref_cod_escola,
        ref_ref_cod_instituicao
      FROM
        ' . $this->_tabela;

    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric( $int_ref_cod_servidor ) ) {
      $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
      $whereAnd = ' AND ';
    }
    if (is_numeric( $int_ref_cod_escola ) ) {
      $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = ' AND ';
    }
    if (is_numeric( $int_ref_ref_cod_instituicao ) ) {
      $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
      $whereAnd = ' AND ';
    }

    $filtros .= "{$whereAnd} justificada <> '0'";
    $whereAnd = ' AND ';

    $filtros .= "{$whereAnd} ativo <> '0'";
    $whereAnd = ' AND ';

    $groupBy = " GROUP BY ref_cod_escola, ref_ref_cod_instituicao";


    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM ({$sql}{$filtros}{$groupBy}) AS countsubquery");

    $sql .= $filtros . $groupBy . $this->getLimite();

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla["_total"] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count( $resultado ) ) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados no método Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query responsável pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordenação no método Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pela Ordenação dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}