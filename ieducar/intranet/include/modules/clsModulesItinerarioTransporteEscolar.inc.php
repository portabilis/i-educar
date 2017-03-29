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
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsModulesItinerarioTransporteEscolar class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   @@package_version@@
 */
class clsModulesItinerarioTransporteEscolar
{
  var $cod_itinerario_transporte_escolar;
  var $ref_cod_rota_transporte_escolar;
  var $seq;
  var $ref_cod_ponto_transporte_escolar;
  var $ref_cod_veiculo;
  var $hora;
  var $tipo;

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
  function clsModulesItinerarioTransporteEscolar($cod_itinerario_transporte_escolar = NULL, $ref_cod_rota_transporte_escolar = NULL
    , $seq = NULL,  $ref_cod_ponto_transporte_escolar = NULL, $ref_cod_veiculo = NULL,
     $hora = NULL, $tipo = NULL)
  {

    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}itinerario_transporte_escolar";

    $this->_campos_lista = $this->_todos_campos = " cod_itinerario_transporte_escolar, ref_cod_rota_transporte_escolar, ref_cod_ponto_transporte_escolar, ref_cod_veiculo, seq, hora, tipo"; 

    if (is_numeric($cod_itinerario_transporte_escolar)) {
      $this->cod_itinerario_transporte_escolar = $cod_itinerario_transporte_escolar;
    }

    if (is_numeric($ref_cod_rota_transporte_escolar)) {
      $this->ref_cod_rota_transporte_escolar = $ref_cod_rota_transporte_escolar;
    }    

    if (is_numeric($seq)) {
      $this->seq = $seq;
    }

    if (is_numeric($ref_cod_ponto_transporte_escolar)) {
      $this->ref_cod_ponto_transporte_escolar = $ref_cod_ponto_transporte_escolar;
    }   

    if (is_numeric($ref_cod_veiculo)) {
      $this->ref_cod_veiculo = $ref_cod_veiculo;
    }   

    if (is_string($hora)) {
      $this->hora = $hora;
    }  

    if (is_string($tipo)) {
      $this->tipo = $tipo;
    }   
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    
    if ( is_numeric($this->ref_cod_rota_transporte_escolar)
     && is_numeric($this->seq) && is_numeric($this->ref_cod_ponto_transporte_escolar) 
      && is_string($this->tipo))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

    if (is_numeric($this->ref_cod_rota_transporte_escolar)) {
      $campos .= "{$gruda}ref_cod_rota_transporte_escolar";
      $valores .= "{$gruda}'{$this->ref_cod_rota_transporte_escolar}'";
      $gruda = ", ";
    }    

    if (is_numeric($this->seq)) {
      $campos .= "{$gruda}seq";
      $valores .= "{$gruda}'{$this->seq}'";
      $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_ponto_transporte_escolar)) {
      $campos .= "{$gruda}ref_cod_ponto_transporte_escolar";
      $valores .= "{$gruda}'{$this->ref_cod_ponto_transporte_escolar}'";
      $gruda = ", ";
    }   

    if (is_numeric($this->ref_cod_veiculo)) {
      $campos .= "{$gruda}ref_cod_veiculo";
      $valores .= "{$gruda}'{$this->ref_cod_veiculo}'";
      $gruda = ", ";
    }   

    if ($this->checktime($this->hora)) {
      $campos .= "{$gruda}hora";
      $valores .= "{$gruda}'{$this->hora}'";
      $gruda = ", ";
    } 

    if (is_string($this->tipo)) {
      $campos .= "{$gruda}tipo";
      $valores .= "{$gruda}'{$this->tipo}'";
      $gruda = ", ";
    } 

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {

    if (is_string($this->cod_itinerario_transporte_escolar)) {
      $db  = new clsBanco();
      $set = '';
      $gruda = '';
 
    if (is_numeric($this->cod_itinerario_transporte_escolar)) {
        $set .= "{$gruda}cod_itinerario_transporte_escolar = '{$this->cod_itinerario_transporte_escolar}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_rota_transporte_escolar)) {
        $set .= "{$gruda}ref_cod_rota_transporte_escolar = '{$this->ref_cod_rota_transporte_escolar}'";
        $gruda = ", ";
    }    

    if (is_numeric($this->seq)) {
        $set .= "{$gruda}seq = '{$this->seq}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_ponto_transporte_escolar)) {
        $set .= "{$gruda}ref_cod_ponto_transporte_escolar = '{$this->ref_cod_ponto_transporte_escolar}'";
        $gruda = ", ";
    }   

    if (is_numeric($this->ref_cod_veiculo)) {
        $set .= "{$gruda}ref_cod_veiculo = '{$this->ref_cod_veiculo}'";
        $gruda = ", ";
    }   

    if (is_string($this->hora)) {
        $set .= "{$gruda}hora = '{$this->hora}'";
        $gruda = ", ";
    }  

    if (is_string($this->tipo)) {
        $set .= "{$gruda}tipo = '{$this->tipo}'";
        $gruda = ", ";
    } 
      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_itinerario_transporte_escolar = '{$this->cod_itinerario_transporte_escolar}'");
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($cod_itinerario_transporte_escolar = NULL, $ref_cod_rota_transporte_escolar = NULL,
   $seq = NULL , $ref_cod_veiculo = NULL, $tipo = NULL, $ref_cod_ponto_transporte_escolar = NULL)
  {
    $sql = "SELECT {$this->_campos_lista},
     (SELECT descricao
       FROM modules.ponto_transporte_escolar
       WHERE ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar) as descricao,
     (SELECT descricao || ', Placa: ' || placa
       FROM modules.veiculo
       WHERE ref_cod_veiculo = cod_veiculo) as nome_onibus FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($cod_itinerario_transporte_escolar)) {
      $filtros .= "{$whereAnd} cod_itinerario_transporte_escolar = '{$cod_itinerario_transporte_escolar}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_cod_rota_transporte_escolar)) {
      $filtros .= "{$whereAnd} ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
      $whereAnd = " AND ";
    }  

    if (is_numeric($seq)) {
      $filtros .= "{$whereAnd} seq = '{$seq}'";
      $whereAnd = " AND ";
    }
    if (is_numeric($ref_cod_veiculo)){
      $filtros .= "{$whereAnd} ref_cod_veiculo = '{$ref_cod_veiculo}'";
      $whereAnd = " AND ";
    }

    if (is_string($tipo)){
      $filtros .= "{$whereAnd} tipo = '{$tipo}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_cod_ponto_transporte_escolar)){
      $filtros .= "{$whereAnd} ref_cod_ponto_transporte_escolar = '{$ref_cod_ponto_transporte_escolar}'";
      $whereAnd = " AND ";
    }    

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


  function listaPontos($ref_cod_rota_transporte_escolar = NULL)
  {
    $sql = "SELECT ref_cod_ponto_transporte_escolar,
     (SELECT descricao
       FROM modules.ponto_transporte_escolar
       WHERE ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar) as descricao,
       (SELECT tipo
       FROM modules.ponto_transporte_escolar
       WHERE ref_cod_ponto_transporte_escolar = cod_ponto_transporte_escolar) as tipo FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($ref_cod_rota_transporte_escolar)) {
      $filtros .= "{$whereAnd} ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
      $whereAnd = " AND ";
    }  

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
    if (is_numeric($this->cod_rota_transporte_escolar)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos}, (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes_destino
         ) AS nome_destino , (
          SELECT
            nome
          FROM
            cadastro.pessoa, modules.empresa_transporte_escolar
          WHERE
            idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar
         ) AS nome_empresa FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
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
    if (is_numeric($this->cod_rota_transporte_escolar)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
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
    if (is_numeric($this->cod_rota_transporte_escolar)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_rota_transporte_escolar = '{$this->ref_cod_rota_transporte_escolar}'";
      $db = new clsBanco();
      $db->Consulta($sql);
      return true;
    }

    return FALSE;
  }

  /**
   * Exclui todos registros.
   * @return bool
   */
  function excluirTodos($ref_cod_rota_transporte_escolar)
  {
    if (is_numeric($ref_cod_rota_transporte_escolar)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE ref_cod_rota_transporte_escolar = '{$ref_cod_rota_transporte_escolar}'";
      $db = new clsBanco();
      $db->Consulta($sql);
      return true;
    }

    return FALSE;
  } 

  function checktime($time)
  { 
    list($hour,$minute) = explode(':',$time);
 
    if ($hour > -1 && $hour < 24 && $minute > -1 && $minute < 60 && is_numeric($hour) && is_numeric($minute))
    {
      return true;
    } else 
        return false;
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