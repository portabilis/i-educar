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
 * @package   iEd_Public
 * @since     ?
 * @version   $Id$
 */

require_once 'include/public/geral.inc.php';

/**
 * clsPublicSetorBai class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Public
 * @since     ?
 * @version   @@package_version@@
 */
class clsPublicSetorBai
{
  var $idsetorbai;
  var $nome;

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

  function __construct($idsetorbai = NULL, $nome = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'public.';
    $this->_tabela = $this->_schema . 'setor_bai ';

    $this->_campos_lista = $this->_todos_campos = ' idsetorbai, nome ';

    if (is_numeric($idsetorbai)) {
      $this->idsetorbai = $idsetorbai;
    }

    if (is_string($nome)) {
      $this->nome = $nome;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_string($this->nome)) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      $campos  .= "{$gruda}nome";
      $valores .= "{$gruda}'{$this->nome}'";
      $gruda    = ', ';
 
      $db->Consulta(sprintf(
        "INSERT INTO %s (%s) VALUES (%s)",
        $this->_tabela, $campos, $valores
      ));

      return $db->InsertId('seq_setor_bai');
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->idsetorbai)) {
      $db  = new clsBanco();
      $set = '';

      if (is_string($this->nome)) {
        $set  .= "{$gruda}nome = '{$this->nome}'";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta(sprintf(
          'UPDATE %s SET %s WHERE idsetorbai = \'%d\'',
          $this->_tabela, $set, $this->idsetorbai
        ));

        return TRUE;
      }
    }

    return FALSE;
  }

  function lista($int_idsetorbai = NULL, $nome = NULL)
  {
    $select = "SELECT {$this->_todos_campos} FROM {$this->_tabela} ";

    $sql = $select;

    $whereAnd = ' WHERE ';
    $filtros = '';
    if (is_numeric($int_idsetorbai)) {
      $filtros .= "{$whereAnd} idsetorbai = '{$int_idsetorbai}'";      
      $whereAnd = ' AND ';
    }

    if (is_string($nome)) {
      $filtros .= "{$whereAnd} nome LIKE '%{$nome}%'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();

    $countCampos = count(explode(', ', $this->_campos_lista));
    $resultado   = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico(sprintf(
      'SELECT COUNT(0) FROM %s %s', $this->_tabela, $filtros
    ));

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla           = $db->Tupla();
        $tupla['_total'] = $this->_total;
        $resultado[]     = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla       = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->idsetorbai)) {
      $db = new clsBanco();

      $sql = sprintf(
        'SELECT %s FROM %s WHERE idsetorbai = \'%d\'',
        $this->_todos_campos, $this->_tabela, $this->idsetorbai
      );

      $db->Consulta($sql);
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
    if (is_numeric($this->idsetorbai)) {
      $db = new clsBanco();

      $sql = sprintf(
        'SELECT 1 FROM %s WHERE idsetorbai = \'%d\'',
        $this->_tabela, $this->idsetorbai
      );

      $db->Consulta($sql);

      if ($db->ProximoRegistro()) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Exclui um registro
   *
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->idsetorbai)) {
      $db = new clsBanco();

      $sql = sprintf(
        'DELETE FROM %s WHERE idsetorbai = \'%d\'',
        $this->_tabela, $this->idsetorbai
      );

      $db->Consulta($sql);
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
}