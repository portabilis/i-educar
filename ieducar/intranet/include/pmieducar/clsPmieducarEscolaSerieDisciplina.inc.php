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
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

/**
 * clsPmieducarEscolaSerieDisciplina class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarEscolaSerieDisciplina
{
  var $ref_ref_cod_serie;
  var $ref_ref_cod_escola;
  var $ref_cod_disciplina;
  var $ativo;
  var $carga_horaria;

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
  function clsPmieducarEscolaSerieDisciplina($ref_ref_cod_serie = NULL,
    $ref_ref_cod_escola = NULL, $ref_cod_disciplina = NULL, $ativo = NULL,
    $carga_horaria = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'escola_serie_disciplina';

    $this->_campos_lista = $this->_todos_campos = 'ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina, carga_horaria';

    if (is_numeric($ref_cod_disciplina)) {
      $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
      try {
        $componenteMapper->find($ref_cod_disciplina);
        $this->ref_cod_disciplina = $ref_cod_disciplina;
      }
      catch (Exception $e) {
      }
    }

    if (is_numeric($ref_ref_cod_escola) && is_numeric($ref_ref_cod_serie)) {
      if (class_exists("clsPmieducarEscolaSerie")) {
        $tmp_obj = new clsPmieducarEscolaSerie($ref_ref_cod_escola, $ref_ref_cod_serie);

        if (method_exists( $tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
            $this->ref_ref_cod_serie = $ref_ref_cod_serie;
          }
        }
        else if (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
            $this->ref_ref_cod_serie = $ref_ref_cod_serie;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola_serie WHERE ref_cod_escola = '{$ref_ref_cod_escola}' AND ref_cod_serie = '{$ref_ref_cod_serie}'")) {
          $this->ref_ref_cod_escola = $ref_ref_cod_escola;
          $this->ref_ref_cod_serie = $ref_ref_cod_serie;
        }
      }
    }
    else {
      $this->ref_ref_cod_serie = $ref_ref_cod_serie;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_numeric($carga_horaria)) {
      $this->carga_horaria = $carga_horaria;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_cod_disciplina)
    ) {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if (is_numeric($this->ref_ref_cod_serie))
      {
        $campos .= "{$gruda}ref_ref_cod_serie";
        $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_escola))
      {
        $campos .= "{$gruda}ref_ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_disciplina))
      {
        $campos .= "{$gruda}ref_cod_disciplina";
        $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
        $gruda = ", ";
      }

      if (is_numeric($this->carga_horaria))
      {
        $campos .= "{$gruda}carga_horaria";
        $valores .= "{$gruda}'{$this->carga_horaria}'";
        $gruda = ", ";
      }
      elseif (is_null($this->carga_horaria)) {
        $campos .= "{$gruda}carga_horaria";
        $valores .= "{$gruda}NULL";
        $gruda = ", ";
      }

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ", ";

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
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
    if (is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_cod_disciplina)
    ) {

      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->carga_horaria)) {
        $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
        $gruda = ", ";
      }
      elseif (is_null($this->carga_horaria)) {
        $set .= "{$gruda}carga_horaria = NULL";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   * @todo Refatorar o primeiro if, tabela referenciada não armazena mais os
   *   componentes curriculares
   */
  function lista($int_ref_ref_cod_serie = NULL, $int_ref_ref_cod_escola = NULL,
    $int_ref_cod_disciplina = NULL, $int_ativo = NULL, $boo_nome_disc = FALSE)
  {
    $whereAnd = " WHERE ";

    if ($boo_nome_disc) {
      $join = ",pmieducar.disciplina"  ;
      $whereAnd = " WHERE ref_cod_disciplina = cod_disciplina AND disciplina.ativo = 1 AND ";
      $campos = ",disciplina.nm_disciplina";
    }

    $sql = "SELECT {$this->_campos_lista}{$campos} FROM {$this->_tabela}{$join}";
    $filtros = "";

    if (is_numeric($int_ref_ref_cod_serie)) {
      $filtros .= "{$whereAnd} ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_ref_cod_escola)) {
      $filtros .= "{$whereAnd} ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_disciplina)) {
      $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ativo)) {
      $filtros .= "{$whereAnd} escola_serie_disciplina.ativo = '{$int_ativo}'";
      $whereAnd = " AND ";
    }


    $db = new clsBanco();
    $countCampos = count(explode(",", $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela}{$join} {$filtros}");

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
    if (is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_cod_disciplina)
    ) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
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
    if (is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_cod_disciplina)
    ) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
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
    if(is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola ) &&
      is_numeric($this->ref_cod_disciplina ))
    {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_cod_disciplina}'");
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Exclui todos os registros referentes a um tipo de avaliação.
   */
  function excluirTodos()
  {
    if (is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola)) {
      $db = new clsBanco();
      $db->Consulta("UPDATE {$this->_tabela} SET ativo = '0' WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'");
      return TRUE;
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

  function diferente($disciplinas) {
    if (is_array($disciplinas) && is_numeric( $this->ref_ref_cod_serie ) &&
      is_numeric($this->ref_ref_cod_escola)
    ) {
      $disciplina_in= '';
      $conc = '';

      foreach ($disciplinas as $disciplina) {
        for ($i = 0; $i < sizeof($disciplina); $i++) {
          $disciplina_in .= "{$conc}{$disciplina[$i]}";
          $conc = ",";
        }
      }

      $db = new clsBanco();
      $db->Consulta("SELECT ref_cod_disciplina FROM {$this->_tabela} WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina not in ({$disciplina_in})");

      $resultado = array();

      while ($db->ProximoRegistro()) {
        $resultado[] = $db->Tupla();
      }
      return $resultado;
    }

    return FALSE;
  }

  function eh_usado($disciplina)
  {
    if (is_numeric($disciplina) && is_numeric($this->ref_ref_cod_serie) &&
      is_numeric($this->ref_ref_cod_escola)
    ) {
      $db = new clsBanco();
      $resultado = $db->CampoUnico("SELECT 1
               FROM pmieducar.turma_disciplina td
              WHERE td.ref_cod_disciplina = {$disciplina}
                AND td.ref_cod_escola = {$this->ref_ref_cod_escola}
                AND td.ref_cod_serie = {$this->ref_ref_cod_serie}

              UNION

              SELECT 1
               FROM pmieducar.disciplina_disciplina_topico ddt
              WHERE ddt.ref_ref_cod_disciplina = {$disciplina}
                AND ddt.ref_ref_ref_cod_escola = {$this->ref_ref_cod_escola}
                AND ddt.ref_ref_ref_cod_serie = {$this->ref_ref_cod_serie}");

      return $resultado;
    }

    return FALSE;
  }

  function setAtivoDisciplinaSerie($ativo)
  {
    if (is_numeric($this->ref_cod_disciplina) && is_numeric($this->ref_ref_cod_serie) &&
      is_numeric($ativo)
    ) {
      $db = new clsBanco();
      $db->Consulta("UPDATE {$this->_tabela} set ativo = '$ativo' WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_disciplina ='{$this->ref_cod_disciplina}'");
      return TRUE;
    }

    return FALSE;
  }

  function desativarDisciplinasSerie()
  {
    if (is_numeric($this->ref_ref_cod_serie)) {
      $db = new clsBanco();
      $db->Consulta("UPDATE {$this->_tabela} set ativo = '0' WHERE ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'");
      return TRUE;
    }

    return FALSE;
  }
}