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
 * @package   iEd_Pessoa
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

/**
 * clsCadastroDeficiencia class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pessoa
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsCadastroDeficiencia
{
  var $cod_deficiencia;
  var $nm_deficiencia;

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
  function __construct($cod_deficiencia = NULL, $nm_deficiencia = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'cadastro.';
    $this->_tabela = "{$this->_schema}deficiencia";

    $this->_campos_lista = $this->_todos_campos = 'cod_deficiencia, nm_deficiencia';

    if (is_numeric($cod_deficiencia)) {
      $this->cod_deficiencia = $cod_deficiencia;
    }

    if (is_string($nm_deficiencia)) {
      $this->nm_deficiencia = $nm_deficiencia;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_string($this->nm_deficiencia)) {
      $db = new clsBanco();

      $campos = '';
      $valores = '';
      $gruda = '';

      if (is_numeric($this->cod_deficiencia)) {
        $campos .= "{$gruda}cod_deficiencia";
        $valores .= "{$gruda}'{$this->cod_deficiencia}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_deficiencia)) {
        $campos .= "{$gruda}nm_deficiencia";
        $valores .= "{$gruda}'{$this->nm_deficiencia}'";
        $gruda = ", ";
      }

      $db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
      return $db->InsertId( "{$this->_tabela}_cod_deficiencia_seq" );
    }
    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_deficiencia)) {
      $db = new clsBanco();
      $set = '';

      if (is_string($this->nm_deficiencia)) {
        $set .= "{$gruda}nm_deficiencia = '{$this->nm_deficiencia}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_deficiencia = NULL, $str_nm_deficiencia = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_deficiencia)) {
      $filtros .= "{$whereAnd} cod_deficiencia = '{$int_cod_deficiencia}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_deficiencia)) {
      $filtros .= "{$whereAnd} nm_deficiencia ILIKE '%{$str_nm_deficiencia}%'";
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
    if (is_numeric($this->cod_deficiencia)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
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
    if (is_numeric($this->cod_deficiencia)) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
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
  function setOrderby( $strNomeCampo )
  {
    if (is_string($strNomeCampo) && $strNomeCampo) {
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