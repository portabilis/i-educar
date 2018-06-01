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

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsModulesProfessorTurma class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     ?
 * @version   @@package_version@@
 */
class clsModulesProfessorTurma
{
  var $id;
  var $ano;
  var $instituicao_id;
  var $servidor_id;
  var $turma_id;
  var $funcao_exercida;
  var $tipo_vinculo;
  var $permite_lancar_faltas_componente;
  var $codUsuario;
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
  function __construct( $id = NULL,$ano = NULL, $instituicao_id = NULL, $servidor_id = NULL, $turma_id = NULL, $funcao_exercida = NULL, $tipo_vinculo = NULL, $permite_lancar_faltas_componente = NULL)
  {
    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}professor_turma";

    $this->_campos_lista = $this->_todos_campos = " pt.id, pt.ano, pt.instituicao_id, pt.servidor_id, pt.turma_id, pt.funcao_exercida, pt.tipo_vinculo, pt.permite_lancar_faltas_componente";

    if (is_numeric($id)) {
      $this->id = $id;
    }

    if (is_numeric($turma_id)) {
      $this->turma_id = $turma_id;
    }

    if (is_numeric($ano)) {
      $this->ano = $ano;
    }

    if (is_numeric($instituicao_id)) {
      $this->instituicao_id = $instituicao_id;
    }

    if (is_numeric($servidor_id)) {
      $this->servidor_id = $servidor_id;
    }

    if (is_numeric($funcao_exercida)) {
      $this->funcao_exercida = $funcao_exercida;
    }

    if (is_numeric($tipo_vinculo)) {
      $this->tipo_vinculo = $tipo_vinculo;
    }

    if (isset($permite_lancar_faltas_componente)) {
      $this->permite_lancar_faltas_componente = '1';
    }else{
      $this->permite_lancar_faltas_componente = '0';
    }

  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {

    if (is_numeric($this->turma_id) && is_numeric($this->funcao_exercida) && is_numeric($this->ano)
          && is_numeric($this->servidor_id) && is_numeric($this->instituicao_id) )
    {

      $db = new clsBanco();
      $campos  = '';
      $valores = '';
      $gruda   = '';

    if (is_numeric($this->instituicao_id)) {
        $campos .= "{$gruda}instituicao_id";
        $valores .= "{$gruda}'{$this->instituicao_id}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ano)) {
        $campos .= "{$gruda}ano";
        $valores .= "{$gruda}'{$this->ano}'";
        $gruda = ", ";
    }

    if (is_numeric($this->servidor_id)) {
        $campos .= "{$gruda}servidor_id";
        $valores .= "{$gruda}'{$this->servidor_id}'";
        $gruda = ", ";
    }

    if (is_numeric($this->turma_id)) {
        $campos .= "{$gruda}turma_id";
        $valores .= "{$gruda}'{$this->turma_id}'";
        $gruda = ", ";
    }

    if (is_numeric($this->funcao_exercida)) {
        $campos .= "{$gruda}funcao_exercida";
        $valores .= "{$gruda}'{$this->funcao_exercida}'";
        $gruda = ", ";
    }

    if (is_numeric($this->tipo_vinculo)) {
        $campos .= "{$gruda}tipo_vinculo";
        $valores .= "{$gruda}'{$this->tipo_vinculo}'";
        $gruda = ", ";
    }

    if (is_numeric($this->permite_lancar_faltas_componente)) {
      $campos .= "{$gruda}permite_lancar_faltas_componente";
      $valores .= "{$gruda}'{$this->permite_lancar_faltas_componente}'";
      $gruda = ", ";
    }

    $campos .= "{$gruda}updated_at";
    $valores .= "{$gruda} CURRENT_TIMESTAMP";
    $gruda = ", ";

    $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
    return $db->InsertId("{$this->_tabela}_id_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {

    if (is_numeric($this->id) && is_numeric($this->turma_id) && is_numeric($this->funcao_exercida) && is_numeric($this->ano)
          && is_numeric($this->servidor_id) && is_numeric($this->instituicao_id)) {

    $db  = new clsBanco();
    $set = '';

    if (is_numeric($this->ano)) {
        $set .= "{$gruda}ano = '{$this->ano}'";
        $gruda = ", ";
    }

    if (is_numeric($this->instituicao_id)) {
        $set .= "{$gruda}instituicao_id = '{$this->instituicao_id}'";
        $gruda = ", ";
    }

    if (is_numeric($this->servidor_id)) {
        $set .= "{$gruda}servidor_id = '{$this->servidor_id}'";
        $gruda = ", ";
    }

    if (is_numeric($this->turma_id)) {

        $set .= "{$gruda}turma_id = '{$this->turma_id}'";
        $gruda = ", ";
    }

    if (is_numeric($this->funcao_exercida)) {
        $set .= "{$gruda}funcao_exercida = '{$this->funcao_exercida}'";
        $gruda = ", ";
    }

    if (is_numeric($this->tipo_vinculo)) {
        $set .= "{$gruda}tipo_vinculo = '{$this->tipo_vinculo}'";
        $gruda = ", ";
    }elseif(is_null($this->tipo_vinculo)){
      $set .= "{$gruda}tipo_vinculo = NULL";
        $gruda = ", ";
    }

    if (is_numeric($this->permite_lancar_faltas_componente)) {
      $set .= "{$gruda}permite_lancar_faltas_componente = '{$this->permite_lancar_faltas_componente}'";
      $gruda = ", ";
    }

    $set .= "{$gruda}updated_at = CURRENT_TIMESTAMP";
    $gruda = ", ";

    if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE id = '{$this->id}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($servidor_id = NULL, $instituicao_id = NULL, $ano = NULL, $ref_cod_escola = NULL, $ref_cod_curso = NULL,
                 $ref_cod_serie = NULL, $ref_cod_turma = NULL, $funcao_exercida = NULL, $tipo_vinculo = NULL)
  {

    $sql = "SELECT {$this->_campos_lista}, t.nm_turma, t.cod_turma as ref_cod_turma, t.ref_ref_cod_serie as ref_cod_serie,
            s.nm_serie, t.ref_cod_curso, c.nm_curso, t.ref_ref_cod_escola as ref_cod_escola, p.nome as nm_escola
            FROM {$this->_tabela} pt";
    $filtros = " , pmieducar.turma t, pmieducar.serie s, pmieducar.curso c, pmieducar.escola e, cadastro.pessoa p WHERE pt.turma_id = t.cod_turma AND t.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso
                  AND t.ref_ref_cod_escola = e.cod_escola AND e.ref_idpes = p.idpes ";

    $whereAnd = " AND ";

    if (is_numeric($servidor_id)) {
      $filtros .= "{$whereAnd} pt.servidor_id = '{$servidor_id}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($instituicao_id)) {
      $filtros .= "{$whereAnd} pt.instituicao_id = '{$instituicao_id}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ano)) {
      $filtros .= "{$whereAnd} pt.ano = '{$ano}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_cod_escola)) {
      $filtros .= "{$whereAnd} t.ref_ref_cod_escola = '{$ref_cod_escola}'";
      $whereAnd = " AND ";
    }elseif ($this->codUsuario) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM pmieducar.escola_usuario
                                        WHERE escola_usuario.ref_cod_escola = t.ref_ref_cod_escola
                                          AND escola_usuario.ref_cod_usuario = '{$this->codUsuario}')";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_cod_curso)) {
      $filtros .= "{$whereAnd} t.ref_cod_curso = '{$ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_cod_serie)) {
      $filtros .= "{$whereAnd} t.ref_ref_cod_serie = '{$ref_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_cod_turma)) {
      $filtros .= "{$whereAnd} t.cod_turma = '{$ref_cod_turma}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($funcao_exercida)) {
      $filtros .= "{$whereAnd} pt.funcao_exercida = '{$funcao_exercida}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($tipo_vinculo)) {
      $filtros .= "{$whereAnd} pt.tipo_vinculo = '{$tipo_vinculo}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista))+8;
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} pt {$filtros}");

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

    if (is_numeric($this->id)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_campos_lista}, t.nm_turma, s.nm_serie, c.nm_curso, p.nome as nm_escola
                     FROM {$this->_tabela} pt, pmieducar.turma t, pmieducar.serie s, pmieducar.curso c,
                     pmieducar.escola e, cadastro.pessoa p
                     WHERE pt.turma_id = t.cod_turma AND t.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso
                     AND t.ref_ref_cod_escola = e.cod_escola AND e.ref_idpes = p.idpes AND id = '{$this->id}'");
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
    if (is_numeric($this->id)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} pt WHERE id = '{$this->id}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return FALSE;
  }

  function existe2()
  {
    if (is_numeric($this->ano) && is_numeric($this->instituicao_id) && is_numeric($this->servidor_id)
        && is_numeric($this->turma_id)) {
      $db = new clsBanco();
      $sql = "SELECT id FROM {$this->_tabela} pt WHERE ano = '{$this->ano}' AND turma_id = '{$this->turma_id}'
               AND instituicao_id = '{$this->instituicao_id}' AND servidor_id = '{$this->servidor_id}' ";

      if (is_numeric($this->id))
        $sql .= " AND id <> {$this->id}";

      return $db->UnicoCampo($sql);
    }
    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {

    if (is_numeric($this->id)) {
      $sql = "DELETE FROM {$this->_tabela} pt WHERE id = '{$this->id}'";
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