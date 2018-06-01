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
 * @since     09/2013
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsModulesUniformeAluno class.
 * 
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     09/2013
 * @version   @@package_version@@
 */
class clsModulesUniformeAluno
{
  var $ref_cod_aluno;
  var $recebeu_uniforme;
  var $quantidade_camiseta;
  var $tamanho_camiseta;
  var $quantidade_blusa_jaqueta;
  var $tamanho_blusa_jaqueta;
  var $quantidade_bermuda;
  var $tamanho_bermuda;
  var $quantidade_calca;
  var $tamanho_calca;
  var $quantidade_saia;
  var $tamanho_saia;
  var $quantidade_calcado;
  var $tamanho_calcado;
  var $quantidade_meia;
  var $tamanho_meia;  

  /**
   * @var int
   * Armazena o total de resultados obtidos na última chamada ao método lista().
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
  function __construct( $ref_cod_aluno = NULL, $recebeu_uniforme = NULL,
   $quantidade_camiseta = NULL, $tamanho_camiseta = NULL, $quantidade_blusa_jaqueta = NULL,
   $tamanho_blusa_jaqueta = NULL, $quantidade_bermuda = NULL, $tamanho_bermuda = NULL,
   $quantidade_calca = NULL, $tamanho_calca = NULL, $quantidade_saia = NULL, $tamanho_saia = NULL,
   $quantidade_calcado = NULL, $tamanho_calcado = NULL, $quantidade_meia = NULL, $tamanho_meia = NULL)     
  {
    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}uniforme_aluno";

    $this->_campos_lista = $this->_todos_campos = " ref_cod_aluno, recebeu_uniforme, quantidade_camiseta, 
      tamanho_camiseta, quantidade_blusa_jaqueta, tamanho_blusa_jaqueta, quantidade_bermuda, tamanho_bermuda,
      quantidade_calca, tamanho_calca, quantidade_saia, tamanho_saia, quantidade_calcado, tamanho_calcado,
      quantidade_meia, tamanho_meia"; 

    if (is_numeric($ref_cod_aluno)) {
      $this->ref_cod_aluno = $ref_cod_aluno;
    }

    if (is_string($recebeu_uniforme)) {
      $this->recebeu_uniforme = $recebeu_uniforme;
    } 

    if (is_numeric($quantidade_camiseta)) {
      $this->quantidade_camiseta = $quantidade_camiseta;
    }
    
    if (is_string($tamanho_camiseta)) {
      $this->tamanho_camiseta = $tamanho_camiseta;
    }    
   
    if (is_numeric($quantidade_blusa_jaqueta)) {
      $this->quantidade_blusa_jaqueta = $quantidade_blusa_jaqueta;
    }
    
    if (is_string($tamanho_blusa_jaqueta)) {
      $this->tamanho_blusa_jaqueta = $tamanho_blusa_jaqueta;
    }    
   
    if (is_numeric($quantidade_bermuda)) {
      $this->quantidade_bermuda = $quantidade_bermuda;
    }
    
    if (is_string($tamanho_bermuda)) {
      $this->tamanho_bermuda = $tamanho_bermuda;
    }    
   
    if (is_numeric($quantidade_calca)) {
      $this->quantidade_calca = $quantidade_calca;
    }
    
    if (is_string($tamanho_calca)) {
      $this->tamanho_calca = $tamanho_calca;
    }    
   
    if (is_numeric($quantidade_saia)) {
      $this->quantidade_saia = $quantidade_saia;
    }
    
    if (is_string($tamanho_saia)) {
      $this->tamanho_saia = $tamanho_saia;
    }    
   
    if (is_numeric($quantidade_calcado)) {
      $this->quantidade_calcado = $quantidade_calcado;
    }
    
    if (is_string($tamanho_calcado)) {
      $this->tamanho_calcado = $tamanho_calcado;
    }   

    if (is_numeric($quantidade_meia)) {
      $this->quantidade_meia = $quantidade_meia;
    }
    
    if (is_string($tamanho_meia)) {
      $this->tamanho_meia = $tamanho_meia;
    }    
   

  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_aluno))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';    

      $campos .= "{$gruda}ref_cod_aluno";
      $valores .= "{$gruda}{$this->ref_cod_aluno}";
      $gruda = ", ";

      $campos .= "{$gruda}recebeu_uniforme";
      $valores .= "{$gruda}'{$this->recebeu_uniforme}'";
      $gruda = ", ";

      if(is_numeric($this->quantidade_camiseta)){
        $campos .= "{$gruda}quantidade_camiseta";
        $valores .= "{$gruda}{$this->quantidade_camiseta}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_camiseta";
      $valores .= "{$gruda}'{$this->tamanho_camiseta}'";
      $gruda = ", ";      

      if(is_numeric($this->quantidade_blusa_jaqueta)){
        $campos .= "{$gruda}quantidade_blusa_jaqueta";
        $valores .= "{$gruda}{$this->quantidade_blusa_jaqueta}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_blusa_jaqueta";
      $valores .= "{$gruda}'{$this->tamanho_blusa_jaqueta}'";
      $gruda = ", ";   

      if(is_numeric($this->quantidade_bermuda)){
        $campos .= "{$gruda}quantidade_bermuda";
        $valores .= "{$gruda}{$this->quantidade_bermuda}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_bermuda";
      $valores .= "{$gruda}'{$this->tamanho_bermuda}'";
      $gruda = ", ";   

      if(is_numeric($this->quantidade_calca)){
        $campos .= "{$gruda}quantidade_calca";
        $valores .= "{$gruda}{$this->quantidade_calca}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_calca";
      $valores .= "{$gruda}'{$this->tamanho_calca}'";
      $gruda = ", ";   

      if(is_numeric($this->quantidade_saia)){
        $campos .= "{$gruda}quantidade_saia";
        $valores .= "{$gruda}{$this->quantidade_saia}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_saia";
      $valores .= "{$gruda}'{$this->tamanho_saia}'";
      $gruda = ", ";   

      if(is_numeric($this->quantidade_calcado)){
        $campos .= "{$gruda}quantidade_calcado";
        $valores .= "{$gruda}{$this->quantidade_calcado}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_calcado";
      $valores .= "{$gruda}'{$this->tamanho_calcado}'";
      $gruda = ", ";   

      if(is_numeric($this->quantidade_meia)){
        $campos .= "{$gruda}quantidade_meia";
        $valores .= "{$gruda}{$this->quantidade_meia}";
        $gruda = ", ";
      }

      $campos .= "{$gruda}tamanho_meia";
      $valores .= "{$gruda}'{$this->tamanho_meia}'";
      $gruda = ", ";   
      
      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $this->ref_cod_aluno;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->ref_cod_aluno)) {
      $db  = new clsBanco();
      $set = '';

      $set .= "recebeu_uniforme = '{$this->recebeu_uniforme}'";
  
      if (is_numeric($this->quantidade_camiseta))
        $set .= ",quantidade_camiseta = '{$this->quantidade_camiseta}'";
      else{
        $set .= ",quantidade_camiseta = NULL";
      }
  
      $set .= ",tamanho_camiseta = '{$this->tamanho_camiseta}'";
  
      if (is_numeric($this->quantidade_blusa_jaqueta))
        $set .= ",quantidade_blusa_jaqueta = '{$this->quantidade_blusa_jaqueta}'";
      else{
        $set .= ",quantidade_blusa_jaqueta = NULL";
      }
  
      $set .= ",tamanho_blusa_jaqueta = '{$this->tamanho_blusa_jaqueta}'";
  
      if (is_numeric($this->quantidade_bermuda))
        $set .= ",quantidade_bermuda = '{$this->quantidade_bermuda}'";
      else{
        $set .= ",quantidade_bermuda = NULL";
      }
  
      $set .= ",tamanho_bermuda = '{$this->tamanho_bermuda}'";
  
      if (is_numeric($this->quantidade_calca))
        $set .= ",quantidade_calca = '{$this->quantidade_calca}'";
      else{
        $set .= ",quantidade_calca = NULL";
      }
  
      $set .= ",tamanho_calca = '{$this->tamanho_calca}'";
  
      if (is_numeric($this->quantidade_saia))
        $set .= ",quantidade_saia = '{$this->quantidade_saia}'";
      else{
        $set .= ",quantidade_saia = NULL";
      }
  
      $set .= ",tamanho_saia = '{$this->tamanho_saia}'";
  
      if (is_numeric($this->quantidade_calcado))
        $set .= ",quantidade_calcado = '{$this->quantidade_calcado}'";
      else{
        $set .= ",quantidade_calcado = NULL";
      }
  
      $set .= ",tamanho_calcado = '{$this->tamanho_calcado}'";
  
      if (is_numeric($this->quantidade_meia))
        $set .= ",quantidade_meia = '{$this->quantidade_meia}'";
      else{
        $set .= ",quantidade_meia = NULL";
      }
  
      $set .= ",tamanho_meia = '{$this->tamanho_meia}'";

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista()
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";
    // implementar

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista))+2;
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
    if (is_numeric($this->ref_cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
    if (is_numeric($this->ref_cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
    if (is_numeric($this->ref_cod_aluno)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_aluno = '{$this->ref_cod_aluno}'";
      $db = new clsBanco();
      $db->Consulta($sql);
      return true;
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