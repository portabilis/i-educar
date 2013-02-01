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
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pessoa
 * @subpackage  Escolaridade
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

/**
 * clsCadastroEscolaridade class.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pessoa
 * @subpackage  Escolaridade
 * @since       Classe disponível desde a versão 1.0.0
 * @version     $Id$
 */
class clsCadastroEscolaridade
{
  var $idesco;
  var $descricao;

  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista.
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
   * Lista separada por virgula, com os campos que devem ser selecionados na próxima chamado ao método lista.
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista.
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista.
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo padrão para ser usado como padrão de ordenação no método lista.
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor (PHP 4).
   */
  function clsCadastroEscolaridade($idesco = NULL, $descricao = NULL)
  {
    $db = new clsBanco();
    $this->_schema = "cadastro.";
    $this->_tabela = "{$this->_schema}escolaridade";

    $this->_campos_lista = $this->_todos_campos = "idesco, descricao";

    if (is_numeric($idesco)) {
      $this->idesco = $idesco;
    }
    if (is_string($descricao)) {
      $this->descricao = $descricao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_string($this->descricao))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      $this->idesco = $db->CampoUnico('SELECT MAX(idesco) + 1
                      FROM cadastro.escolaridade');

      // Se for nulo, é o primeiro registro da tabela
      if (is_null($this->idesco)) {
        $this->idesco = 1;
      }

      if (is_numeric($this->idesco)) {
        $campos  .= "{$gruda}idesco";
        $valores .= "{$gruda}'{$this->idesco}'";
        $gruda = ", ";
      }
      if (is_string($this->descricao)) {
        $campos  .= "{$gruda}descricao";
        $valores .= "{$gruda}'{$this->descricao}'";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $this->idesco;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->idesco)) {
      $db  = new clsBanco();
      $set = "";

      if (is_string($this->descricao)) {
        $set  .= "{$gruda}descricao = '{$this->descricao}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idesco = '{$this->idesco}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_idesco = NULL, $str_descricao = NULL) {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_idesco)) {
      $filtros .= "{$whereAnd} idesco = '{$int_idesco}'";
      $whereAnd = " AND ";
    }
    if (is_string($str_descricao)) {
      $filtros .= "{$whereAnd} descricao ILIKE '%{$str_descricao}%'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(",", $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
    if (is_numeric($this->idesco)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idesco = '{$this->idesco}'");
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
    if (is_numeric($this->idesco)) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE idesco = '{$this->idesco}'");

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados na invocação do método lista.
   * @return null
   */
  function setCamposLista($str_campos) {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista deverá retornoar todos os campos da tabela.
   * @return null
   */
  function resetCamposLista() {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método lista.
   * @return null
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query resposável pelo limite de registros.
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
   * Define campo para ser utilizado como ordenação no método lista.
   * @return null
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query resposável pela ordenação dos registros.
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