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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     ?
 * @version   $Id$
 */

require_once( "include/pmieducar/geral.inc.php" );

/**
 * clsModulesNotaExame class.
 * 
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     ?
 * @version   @@package_version@@
 */

class clsModulesNotaExame
{
  var $ref_cod_matricula;
  var $ref_cod_componente_curricular;
  var $nota_exame;

  // propriedades padrao

  /**
   * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
   *
   * @var int
   */
  var $_total;

  /**
   * Nome do schema
   *
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela
   *
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
   *
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
   *
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo metodo lista
   *
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no metodo lista
   *
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
   *
   * @var string
   */
  var $_campo_order_by;


  /**
   * Construtor (PHP 4)
   *
   * @return object
   */
  function clsModulesNotaExame($ref_cod_matricula = NULL , $ref_cod_componente_curricular = NULL, $nota_exame = NULL)
  {
    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}nota_exame";

    $this->_campos_lista = $this->_todos_campos = "ref_cod_matricula, ref_cod_componente_curricular, nota_exame";

    if( is_numeric( $ref_cod_matricula ) )
    {
      $this->ref_cod_matricula = $ref_cod_matricula;
    }
    if( is_numeric( $ref_cod_componente_curricular ) )
    {
      $this->ref_cod_componente_curricular = $ref_cod_componente_curricular;
    }
    if( is_numeric( $nota_exame ) )
    {
      $this->nota_exame = $nota_exame;
    }   
  }

  /**
   * Cria um novo registro
   *
   * @return bool
   */
  function cadastra()
  {
    if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_componente_curricular ) && is_numeric( $this->nota_exame ) )
    {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if( is_numeric( $this->ref_cod_matricula ) )
      {
        $campos .= "{$gruda}ref_cod_matricula";
        $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
        $gruda = ", ";
      }
      if( is_numeric( $this->ref_cod_componente_curricular ) )
      {
        $campos .= "{$gruda}ref_cod_componente_curricular";
        $valores .= "{$gruda}'{$this->ref_cod_componente_curricular}'";
        $gruda = ", ";
      }
      if( is_numeric( $this->nota_exame ) )
      {
        $campos .= "{$gruda}nota_exame";
        $valores .= "{$gruda}'{$this->nota_exame}'";
        $gruda = ", ";
      }
      
      $db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
      return $this->ref_cod_matricula;
    }
    return false;
  }

  /**
   * Edita os dados de um registro
   *
   * @return bool
   */
  function edita()
  {
    if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_componente_curricular ) && is_numeric( $this->nota_exame ) )
    {

      $db = new clsBanco();
      $set = "";

      if( is_numeric( $this->nota_exame ) )
      {
        $set .= "{$gruda}nota_exame = '{$this->nota_exame}'";
        $gruda = ", ";
      }

      if( $set )
      {
        $db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'" );
        return true;
      }
    }
    return false;
  }

  /**
   * Retorna um array com os dados de um registro
   *
   * @return array
   */
  function detalhe()
  {
    if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_componente_curricular ) )
    {

      $db = new clsBanco();
      $db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'" );
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return false;
  }

  /**
   * Retorna um array com os dados de um registro
   *
   * @return array
   */
  function existe()
  {
    if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_componente_curricular ) )
    {

      $db = new clsBanco();
      $db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'" );
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return false;
  }

  /**
   * Exclui um registro
   *
   * @return bool
   */
  function excluir()
  {
    if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_componente_curricular ) )
    {

      $db = new clsBanco();
      $db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_componente_curricular = '{$this->ref_cod_componente_curricular}'" );
      return true;
    }
    return false;
  }

  /**
   * Define quais campos da tabela serao selecionados na invocacao do metodo lista
   *
   * @return null
   */
  function setCamposLista( $str_campos )
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o metodo Lista devera retornoar todos os campos da tabela
   *
   * @return null
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o metodo lista
   *
   * @return null
   */
  function setLimite( $intLimiteQtd, $intLimiteOffset = null )
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query resposavel pelo Limite de registros
   *
   * @return string
   */
  function getLimite()
  {
    if( is_numeric( $this->_limite_quantidade ) )
    {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if( is_numeric( $this->_limite_offset ) )
      {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return "";
  }

  /**
   * Define campo para ser utilizado como ordenacao no metolo lista
   *
   * @return null
   */
  function setOrderby( $strNomeCampo )
  {
    // limpa a string de possiveis erros (delete, insert, etc)
    //$strNomeCampo = eregi_replace();

    if( is_string( $strNomeCampo ) && $strNomeCampo )
    {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
   *
   * @return string
   */
  function getOrderby()
  {
    if( is_string( $this->_campo_order_by ) )
    {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return "";
  }

}
?>