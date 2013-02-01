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
 * @package   iEd_Pmicontrolesis
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';

/**
 * clsMenuSuspenso class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmicontrolesis
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsMenuSuspenso
{
  var $cod_menu;
  var $ref_cod_menu_submenu;
  var $ref_cod_menu_pai;
  var $tt_menu;
  var $ref_cod_ico;
  var $ord_menu;
  var $caminho;
  var $alvo;
  var $suprime_menu;
  var $ref_cod_tutormenu;

  var $tabela;
  var $schema;

  /**
   * Construtor.
   */
  function clsMenuSuspenso($cod_menu = FALSE, $ref_cod_menu_submenu = FALSE,
    $ref_cod_menu_pai = FALSE, $tt_menu = FALSE, $ref_cod_ico = FALSE,
    $ord_menu = FALSE, $caminho = FALSE, $alvo = FALSE, $suprime_menu = FALSE,
    $ref_cod_tutormenu = FALSE)
  {
    $this->cod_menu             = $cod_menu;
    $this->ref_cod_menu_submenu = $ref_cod_menu_submenu;
    $this->ref_cod_menu_pai     = $ref_cod_menu_pai;
    $this->tt_menu              = $tt_menu;
    $this->ref_cod_ico          = $ref_cod_ico;
    $this->ord_menu             = $ord_menu;
    $this->caminho              = $caminho;
    $this->alvo                 = $alvo;
    $this->suprime_menu         = $suprime_menu;
    $this->ref_cod_tutormenu    = $ref_cod_tutormenu;

    $this->tabela = "menu";
    $this->schema = "pmicontrolesis";
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    $db = new clsBanco();

    if (is_string($this->tt_menu) && is_numeric($this->ord_menu)) {
      $campos  = '';
      $valores = '';

      if ($this->ref_cod_menu_submenu) {
        $campos  .= ", ref_cod_menu_submenu";
        $valores .= ", '$this->ref_cod_menu_submenu' ";
      }

      if ($this->ref_cod_menu_pai) {
        $campos  .= ", ref_cod_menu_pai";
        $valores .= ", '$this->ref_cod_menu_pai' ";
      }

      if ($this->ref_cod_ico) {
        $campos  .= ", ref_cod_ico";
        $valores .= ", '$this->ref_cod_ico' ";
      }

      if ($this->caminho) {
        $campos  .= ", caminho";
        $valores .= ", '$this->caminho' ";
      }

      if ($this->alvo) {
        $campos  .= ", alvo";
        $valores .= ", '$this->alvo' ";
      }

      if ($this->suprime_menu || $this->suprime_menu == '0') {
        $campos  .= ", suprime_menu";
        $valores .= ", '$this->suprime_menu' ";
      }

      if ($this->ref_cod_tutormenu) {
        $campos  .= ", ref_cod_tutormenu";
        $valores .= ", '$this->ref_cod_tutormenu' ";
      }

      $db->Consulta("INSERT INTO {$this->schema}.{$this->tabela} (tt_menu, ord_menu {$campos}) VALUES ('$this->tt_menu', '$this->ord_menu' {$valores})");
      return $db->InsertId("{$this->schema}.menu_cod_menu_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_menu)) {
      $where_set = "SET";

      if (is_numeric($this->ref_cod_menu_submenu)) {
        $set .= " {$where_set} ref_cod_menu_submenu = '$this->ref_cod_menu_submenu' ";
        $where_set = ",";
      }

      if (is_numeric($this->ref_cod_menu_pai)) {
        $set .= " {$where_set} ref_cod_menu_pai = '$this->ref_cod_menu_pai' ";
        $where_set = ",";
      }

      if (is_string($this->tt_menu)) {
        $set .= " {$where_set} tt_menu = '$this->tt_menu' ";
        $where_set = ",";
      }

      if (is_string($this->ref_cod_ico)) {
        $set .= " {$where_set} ref_cod_ico = '$this->ref_cod_ico' ";
        $where_set = ",";
      }

      if (is_numeric($this->ord_menu)) {
        $set .= " {$where_set} ord_menu = '$this->ord_menu' ";
        $where_set = ",";
      }

      if (is_string($this->caminho)) {
        $set .= " {$where_set} caminho = '$this->caminho' ";
        $where_set = ",";
      }

      if (is_string($this->alvo)) {
        $set .= " {$where_set} alvo = '$this->alvo' ";
        $where_set = ",";
      }

      if (is_numeric($this->suprime_menu) || $this->suprime_menu == '0') {
        $set .= " {$where_set} suprime_menu = '$this->suprime_menu' ";
        $where_set = ",";
      }

      if (is_numeric($this->ref_cod_tutormenu)) {
        $set .= " {$where_set} ref_cod_tutormenu = '$this->ref_cod_tutormenu' ";
      }

      if ($set) {
        $db = new clsBanco();
        $db->Consulta("UPDATE {$this->schema}.{$this->tabela} $set WHERE cod_menu = '{$this->cod_menu}'");
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
    if (is_numeric($this->ref_cod_tutormenu)) {
      $db = new clsBanco();
      $dba = new clsBanco();
      $db->Consulta("
        SELECT
          cod_menu, ref_cod_menu_pai
        FROM
          pmicontrolesis.menu
        WHERE
          ref_cod_menu_pai IN (
            SELECT
              cod_menu
            FROM
              pmicontrolesis.menu
            WHERE
              ref_cod_menu_pai IN (
                SELECT
                  cod_menu
                FROM
                  pmicontrolesis.menu
                WHERE
                  ref_cod_menu_pai IN (
                    SELECT
                      cod_menu
                    FROM
                      pmicontrolesis.menu
                    WHERE
                      ref_cod_menu_pai IS NULL)
              )
          )
          AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'
        UNION all
          SELECT
            cod_menu, ref_cod_menu_pai
          FROM
            pmicontrolesis.menu
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu
              WHERE
                ref_cod_menu_pai IN (
                  SELECT
                    cod_menu
                  FROM
                    pmicontrolesis.menu
                  WHERE
                    ref_cod_menu_pai IS NULL
                )
            )
            AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'
        UNION all
          SELECT
            cod_menu, ref_cod_menu_pai
          FROM
            pmicontrolesis.menu
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu
              WHERE
                ref_cod_menu_pai IS NULL
            )
            AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'
        UNION all
          SELECT
            cod_menu, ref_cod_menu_pai
          FROM
            pmicontrolesis.menu
          WHERE
            ref_cod_menu_pai IS NULL
            AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'");

      while ($db->ProximoRegistro()) {
        list($cod_menu,$ref_cod_menu_pai) = $db->Tupla();

        if ($ref_cod_menu_pai) {
            $ref_cod_menu_pai = "AND ref_cod_menu_pai={$ref_cod_menu_pai}";
        }

        $dba->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE ref_cod_tutormenu = {$this->ref_cod_tutormenu} $ref_cod_menu_pai AND cod_menu={$cod_menu}");
      }

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_ref_cod_menu_submenu = FALSE, $int_ref_cod_menu_pai = FALSE,
    $str_tt_menu = FALSE, $str_ref_cod_ico = FALSE, $int_ord_menu = FALSE,
    $str_caminho = FALSE, $str_alvo = FALSE, $int_suprime_menu = FALSE,
    $int_ref_cod_tutormenu = FALSE, $int_limite_ini = FALSE,
    $int_limite_qtd = FALSE, $str_ordenacao = FALSE, $int_cod_menu = FALSE)
  {
    $whereAnd = "WHERE ";

    if (is_numeric($int_ref_cod_menu_submenu)) {
      $where .= "{$whereAnd}ref_cod_menu_submenu = '$int_ref_cod_menu_submenu'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_menu_pai)) {
      $where .= "{$whereAnd}ref_cod_menu_pai = '$int_ref_cod_menu_pai'";
      $whereAnd = " AND ";
    }

    if (is_string($str_tt_menu)) {
      $where .= "{$whereAnd}tt_menu =  '$str_tt_menu'";
      $whereAnd = " AND ";
    }

    if (is_string($str_ref_cod_ico)) {
      $where .= "{$whereAnd}ref_cod_ico >= '$str_ref_cod_ico'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ord_menu)) {
      $where .= "{$whereAnd}ord_menu >= '$int_ord_menu'";
      $whereAnd = " AND ";
    }

    if (is_string($str_caminho)) {
      $where .= "{$whereAnd}caminho >= '$str_caminho'";
      $whereAnd = " AND ";
    }

    if (is_string($str_alvo)) {
      $where .= "{$whereAnd}alvo <= '$str_alvo'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_suprime_menu)) {
      $where .= "{$whereAnd}suprime_menu = '$int_suprime_menu'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_tutormenu)) {
      $where .= "{$whereAnd}ref_cod_tutormenu = '$int_ref_cod_tutormenu'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_cod_menu)) {
      $where .= "{$whereAnd}cod_menu = '$int_cod_menu'";
      $whereAnd = " AND ";
    }

    $orderBy = "";
    if (is_string($str_ordenacao)) {
      $orderBy = "ORDER BY $str_ordenacao";
    }

    $limit = "";
    if (is_numeric($int_limite_ini) && is_numeric($int_limite_qtd)) {
      $limit = " LIMIT $int_limite_qtd OFFSET $int_limite_ini";
    }

    $db = new clsBanco();
    $total = $db->UnicoCampo("SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where");
    $db->Consulta("SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu FROM {$this->schema}.{$this->tabela} $where $orderBy $limit");

    $resultado = array();
    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $tupla['total'] = $total;
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
    if ($this->cod_menu) {
      $db = new clsBanco();
      $db->Consulta("SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu FROM {$this->schema}.{$this->tabela} WHERE cod_menu = '$this->cod_menu'");

      if ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        return $tupla;
      }

      return FALSE;
    }
  }

  /**
   * Retorna um array com os itens de menu liberados ao usuário de acordo
   * com as suas permissões.
   *
   * @param int $ref_cod_tutormenu Identificação do menu principal (subsistema,
   *   exemplo: 15 para i-Educar e 16 para Biblioteca
   * @param int $idpes Identificação do usuário
   * @return array|bool Retorna FALSE caso o usuário não possua privilégios de
   *   acesso a algum dos itens de menu
   */
  function listaNivel($ref_cod_tutormenu, $idpes)
  {
    $db = new clsBanco();

    if ($db->UnicoCampo("SELECT 1 FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj = '$idpes' AND ref_cod_menu_submenu ='0'")) {
      $menu_pai = "
          , (
            SELECT
              mm.ref_cod_menu_pai
            FROM
              portal.menu_submenu ms, portal.menu_menu mm
            WHERE
              ms.ref_cod_menu_menu = mm.cod_menu_menu
            AND
              ms.cod_menu_submenu = m.ref_cod_menu_submenu
          ) AS menu_menu_pai ";

      $sql = "
        SELECT
          cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico,
          ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 1 AS nivel
          $menu_pai
        FROM
          pmicontrolesis.menu m
        WHERE
          ref_cod_menu_pai IS NULL
          AND ref_cod_tutormenu = '$ref_cod_tutormenu'
        UNION
          SELECT
            cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
            ref_cod_ico, ord_menu, caminho, alvo, suprime_menu,
            ref_cod_tutormenu, 2 AS nivel
            $menu_pai
          FROM
            pmicontrolesis.menu m
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu
              WHERE
                ref_cod_menu_pai IS NULL
            )
            AND ref_cod_tutormenu = '$ref_cod_tutormenu'
        UNION
          SELECT
            cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
            ref_cod_ico, ord_menu, caminho, alvo, suprime_menu,
            ref_cod_tutormenu, 3 AS nivel
            $menu_pai
          FROM
            pmicontrolesis.menu m
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu
              WHERE
                ref_cod_menu_pai IN (
                  SELECT
                    cod_menu
                  FROM
                    pmicontrolesis.menu
                  WHERE
                    ref_cod_menu_pai IS NULL
                )
            )
            AND ref_cod_tutormenu = '$ref_cod_tutormenu'
        UNION
          SELECT
            cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
            ref_cod_ico, ord_menu, caminho, alvo, suprime_menu,
            ref_cod_tutormenu, 4 AS nivel
            $menu_pai
          FROM
            pmicontrolesis.menu m
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu
              WHERE
                ref_cod_menu_pai IN (
                  SELECT
                    cod_menu
                  FROM
                    pmicontrolesis.menu
                  WHERE
                    ref_cod_menu_pai IN (
                      SELECT
                        cod_menu
                      FROM
                        pmicontrolesis.menu
                      WHERE
                        ref_cod_menu_pai IS NULL
                    )
                )
            )
            AND ref_cod_tutormenu = '$ref_cod_tutormenu'
          ORDER BY nivel ASC, ord_menu ASC";
    }
    else {
      $menus = '';
      $juncao = '';
      $db->Consulta("SELECT ref_cod_menu_submenu FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj = '$idpes' UNION SELECT cod_menu_submenu FROM menu_submenu WHERE nivel ='2' UNION SELECT cod_menu_submenu FROM menu_submenu WHERE nivel ='2'");

      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $menus .= "$juncao {$tupla['ref_cod_menu_submenu']}";
        $juncao = ', ';
      }

      $sql = "
        SELECT
          cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico,
          ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 1 AS nivel
        FROM
          pmicontrolesis.menu m
        WHERE
          ref_cod_menu_pai IS NULL
          AND ref_cod_tutormenu = '$ref_cod_tutormenu'
          AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
        UNION
          SELECT
            cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
            ref_cod_ico, ord_menu, caminho, alvo, suprime_menu,
            ref_cod_tutormenu, 2 AS nivel
          FROM
            pmicontrolesis.menu m
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu m
              WHERE
                ref_cod_menu_pai IS NULL
                AND ref_cod_tutormenu = '$ref_cod_tutormenu'
                AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
            )
            AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
        UNION
          SELECT
            cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
            ref_cod_ico, ord_menu, caminho, alvo, suprime_menu,
            ref_cod_tutormenu, 3 AS nivel
          FROM
            pmicontrolesis.menu m
          WHERE
            ref_cod_menu_pai IN (
              SELECT
                cod_menu
              FROM
                pmicontrolesis.menu m
              WHERE
                ref_cod_menu_pai IN (
                  SELECT
                    cod_menu
                  FROM
                    pmicontrolesis.menu m
                  WHERE
                    ref_cod_menu_pai IS NULL
                    AND ref_cod_tutormenu = '$ref_cod_tutormenu'
                    AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
                )
                AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
            )
            AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
          UNION
            SELECT
              cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu,
              ref_cod_ico, ord_menu, caminho, alvo, suprime_menu,
              ref_cod_tutormenu, 4 AS nivel
            FROM
              pmicontrolesis.menu m
            WHERE
              ref_cod_menu_pai IN (
                SELECT
                  cod_menu
                FROM
                  pmicontrolesis.menu m
                WHERE ref_cod_menu_pai IN (
                  SELECT
                    cod_menu
                  FROM
                    pmicontrolesis.menu m
                  WHERE
                    ref_cod_menu_pai IN (
                      SELECT
                        cod_menu
                      FROM
                        pmicontrolesis.menu m
                      WHERE
                        ref_cod_menu_pai IS NULL
                        AND ref_cod_tutormenu = '$ref_cod_tutormenu'
                        AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
                    )
                    AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
                )
                AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
            )
            AND ((ref_cod_menu_submenu IS NULL) OR (ref_cod_menu_submenu IN ($menus)))
            ORDER BY nivel ASC, ord_menu ASC";
    }

    $db->Consulta($sql);
    $resultado = array();

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $tupla['total'] = $total;
      $resultado[] = $tupla;
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }
}