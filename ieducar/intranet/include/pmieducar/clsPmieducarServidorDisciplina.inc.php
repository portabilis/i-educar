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
 * clsPmieducarServidorDisciplina class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarServidorDisciplina
{
  var $ref_cod_disciplina;
  var $ref_ref_cod_instituicao;
  var $ref_cod_servidor;
  var $ref_cod_curso;

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
  function clsPmieducarServidorDisciplina($ref_cod_disciplina = NULL,
    $ref_ref_cod_instituicao = NULL, $ref_cod_servidor = NULL, $ref_cod_curso = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'servidor_disciplina';

    $this->_campos_lista = $this->_todos_campos = 'ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_curso';

    if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
      $servidor = new clsPmieducarServidor($ref_cod_servidor, NULL, NULL, NULL,
        NULL, NULL, NULL, $ref_ref_cod_instituicao);

      if ($servidor->existe()) {
        $this->ref_cod_servidor = $ref_cod_servidor;
        $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
      }
    }

    if (is_numeric($ref_cod_disciplina)) {
      $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
      try {
        $componenteMapper->find($ref_cod_disciplina);
        $this->ref_cod_disciplina = $ref_cod_disciplina;
      }
      catch (Exception $e) {
      }
    }

    if (is_numeric($ref_cod_curso)) {
      $curso = new clsPmieducarCurso($ref_cod_curso);

      if ($curso->existe()) {
        $this->ref_cod_curso = $ref_cod_curso;
      }
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_disciplina) &&
      is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor) &&
      is_numeric($this->ref_cod_curso)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_cod_disciplina)) {
        $campos .= "{$gruda}ref_cod_disciplina";
        $valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_instituicao)) {
        $campos .= "{$gruda}ref_ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_servidor)) {
        $campos .= "{$gruda}ref_cod_servidor";
        $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_curso)) {
        $campos .= "{$gruda}ref_cod_curso";
        $valores .= "{$gruda}'{$this->ref_cod_curso}'";
        $gruda = ", ";
      }

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
    if (is_numeric($this->ref_cod_disciplina) &&
      is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor) &&
      is_numeric($this->ref_cod_curso)
    ) {
      $db = new clsBanco();
      $set = '';

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_curso = '{$this->ref_cod_curso}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_ref_cod_disciplina = NULL, $int_ref_ref_cod_instituicao = NULL,
    $int_ref_cod_servidor = NULL, $int_ref_cod_curso = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_ref_cod_disciplina)) {
      $filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_servidor)) {
      $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
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
    if (is_numeric($this->ref_cod_disciplina) &&
      is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor) &&
      is_numeric($this->ref_cod_curso)
    ) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'");
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
    if (is_numeric($this->ref_cod_disciplina) &&
      is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor) &&
      is_numeric($this->ref_cod_curso)
    ) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_disciplina = '{$this->ref_cod_disciplina}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_curso = '{$this->ref_cod_curso}'");
      if ($db->ProximoRegistro()) {
        return TRUE;
      }
    }
    return false;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->ref_cod_disciplina) &&
      is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor) &&
      is_numeric($this->ref_cod_curso)
    ) {
    }
    return FALSE;
  }

  /**
   * Exclui todos os registros de disciplinas de um servidor.
   * @return bool
   */
  function excluirTodos()
  {
    if (is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor)) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'");
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