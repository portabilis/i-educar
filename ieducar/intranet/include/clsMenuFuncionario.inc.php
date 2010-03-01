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
 * @package   iEd_Include
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'clsBanco.inc.php';

/**
 * clsMenuFuncionario class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsMenuFuncionario
{
  var $ref_ref_cod_pessoa_fj = FALSE;
  var $cadastra              = FALSE;
  var $exclui                = FALSE;
  var $ref_cod_menu_submenu  = FALSE;

  /**
   * Construtor.
   *
   * @param int $int_ref_ref_cod_pessoa_fj
   * @param bool $cadastra
   * @param bool $exclui
   * @param int $int_ref_cod_menu_submenu
   */
  function clsMenuFuncionario($int_ref_ref_cod_pessoa_fj = FALSE,
    $cadastra = FALSE, $exclui = FALSE, $int_ref_cod_menu_submenu = FALSE)
  {
    $obj = new clsPessoaFj($int_ref_ref_cod_pessoa_fj);

    if ($obj->detalhe()) {
      $this->ref_ref_cod_pessoa_fj= $int_ref_ref_cod_pessoa_fj;
    }

    $this->cadastra= $cadastra;
    $this->exclui= $exclui;
    $this->ref_cod_menu_submenu = $int_ref_cod_menu_submenu;
    $this->tabela = "menu_funcionario";
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    $db = new clsBanco();

    if ($this->ref_ref_cod_pessoa_fj && $this->ref_cod_menu_submenu) {
      $campos  = '';
      $valores = '';

      if (is_numeric($this->cadastra)) {
        $campos .= ", cadastra";
        $valores .= ", '$this->cadastra'";
      }

      if (is_numeric($this->exclui)) {
        $campos .= ", exclui";
        $valores .= ", '$this->exclui'";
      }

      $db->Consulta("INSERT INTO {$this->tabela} (ref_ref_cod_pessoa_fj, ref_cod_menu_submenu $campos) VALUES ('$this->ref_ref_cod_pessoa_fj', '$this->ref_cod_menu_submenu'  $valores)");
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric( $this->ref_cod_menu_submenu) && $this->ref_ref_cod_pessoa_fj) {
      $set = '';
      $gruda = ' ';

      if (is_numeric($this->cadastra)) {
        $set .= "{$gruda}cadastra = '{$this->cadastra}'";
        $gruda = ', ';
      }

      if (is_numeric($this->exclui)) {
        $set .= "{$gruda}exclui = '{$this->exclui}'";
        $gruda = ', ';
      }

      if ($set != '') {
        $db = new clsBanco();
        $db->Consulta("UPDATE {$this->tabela} SET $set WHERE ref_cod_menu_submenu = '{$this->ref_cod_menu_submenu}' AND ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function exclui()
  {
    if($this->ref_ref_cod_pessoa_fj &&  $this->ref_cod_menu_submenu) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM $this->tabela  WHERE ref_ref_cod_pessoa_fj ='$this->ref_ref_cod_pessoa_fj' AND ref_cod_menu_submenu = '$this->ref_cod_menu_submenu'");
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Exclui todos os registros associados (inclusive) ao menu identificado por
   * $int_cod_menu_menu.
   *
   * @param int $int_cod_menu_menu
   * @return bool
   */
  function exclui_todos($int_cod_menu_menu = FALSE)
  {
    if ($this->ref_ref_cod_pessoa_fj) {
      if (is_numeric($int_cod_menu_menu)) {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu IN (SELECT cod_menu_submenu FROM menu_submenu WHERE ref_cod_menu_menu IN (SELECT cod_menu_menu FROM menu_menu WHERE cod_menu_menu = '{$int_cod_menu_menu}' OR ref_cod_menu_pai ='{$int_cod_menu_menu}'))");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   *
   * @param int $int_ref_ref_cod_pessoa_fj
   * @param int $int_ref_cod_menu_submenu
   * @param string $str_ordenacao String com a expressão SQL de ordenação dos
   *   resultados da query SELECT
   * @param int $int_limite_ini
   * @param int $int_limite_qtd
   * @param int $int_ref_cod_menu_menu
   * @return array|bool Retorna FALSE caso nenhum registro seja encontrado
   */
  function lista($int_ref_ref_cod_pessoa_fj = FALSE,
    $int_ref_cod_menu_submenu = FALSE, $str_ordenacao = FALSE,
    $int_limite_ini =FALSE, $int_limite_qtd = FALSE,
    $int_ref_cod_menu_menu = FALSE)
  {
    $where = '';
    $and = '';

    if (is_numeric($int_ref_ref_cod_pessoa_fj)) {
      $where .= " $and ref_ref_cod_pessoa_fj = '$int_ref_ref_cod_pessoa_fj'";
      $and = " AND ";
    }

    if (is_numeric($int_ref_cod_menu_submenu)) {
      $where .= " $and ref_cod_menu_submenu  = '$int_ref_cod_menu_submenu'";
      $and = " AND ";
    }

    if (is_numeric($int_ref_cod_menu_menu)) {
      $where .= " $and ref_cod_menu_submenu  = ms.cod_menu_submenu AND ref_cod_menu_menu = '$int_ref_cod_menu_menu'";
      $tabela = ", menu_submenu ms";
      $and = " AND ";
    }

    $ordernacao = "";

    if (is_string($str_ordenacao)) {
      $ordernacao = " $str_ordenacao";
    }

    if($where) {
      $where = " WHERE $where";
    }

    if($int_limite_ini !== FALSE && $int_limite_qtd !== FALSE) {
      $limit = " LIMIT $int_limite_ini,$int_limite_qtd";
    }

    $db = new clsBanco();
    $total = $db->UnicoCampo("SELECT COUNT(0) AS total FROM {$this->tabela} ");

    $db->Consulta("SELECT ref_ref_cod_pessoa_fj, ref_cod_menu_submenu, cadastra, exclui FROM {$this->tabela} $tabela $where $ordernacao $limit" );
    $resultado = array();

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $tupla["total"] = $total;
      $tupla[4] = &$tupla["total"];

      $resultado[] = $tupla;
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
    if (is_numeric($this->ref_ref_cod_pessoa_fj) && is_numeric($this->ref_cod_menu_submenu)) {
      $db = new clsBanco();
      $db->Consulta( "SELECT ref_ref_cod_pessoa_fj, ref_cod_menu_submenu, cadastra, exclui FROM {$this->tabela} WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu = '$this->ref_cod_menu_submenu' " );

      if ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        return $tupla;
      }
    }

    return FALSE;
  }
}