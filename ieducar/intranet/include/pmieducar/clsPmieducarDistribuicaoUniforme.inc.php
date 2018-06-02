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
 * clsPmieducarDistribuicaoUniforme class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     ?
 * @version   @@package_version@@
 */
class clsPmieducarDistribuicaoUniforme
{
  var $cod_distribuicao_uniforme;
  var $ref_cod_aluno;
  var $ano;
  var $kit_completo;
  var $agasalho_qtd;
  var $camiseta_curta_qtd;
  var $camiseta_longa_qtd;
  var $meias_qtd;
  var $bermudas_tectels_qtd;
  var $bermudas_coton_qtd;
  var $tenis_qtd;
  var $data;
  var $agasalho_tm;
  var $camiseta_curta_tm;
  var $camiseta_longa_tm;
  var $meias_tm;
  var $bermudas_tectels_tm;
  var $bermudas_coton_tm;
  var $tenis_tm;
  var $ref_cod_escola;

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
  function __construct( $cod_distribuicao_uniforme = NULL, $ref_cod_aluno = NULL, $ano = NULL,
        $kit_completo = NULL, $agasalho_qtd = NULL, $camiseta_curta_qtd = NULL,
        $camiseta_longa_qtd = NULL, $meias_qtd = NULL, $bermudas_tectels_qtd = NULL,
        $bermudas_coton_qtd = NULL, $tenis_qtd = NULL, $data = NULL, $agasalho_tm = NULL, $camiseta_curta_tm = NULL,
        $camiseta_longa_tm = NULL, $meias_tm = NULL, $bermudas_tectels_tm = NULL,
        $bermudas_coton_tm = NULL, $tenis_tm = NULL, $ref_cod_escola = NULL)
  {
    $db = new clsBanco();
    $this->_schema = "pmieducar.";
    $this->_tabela = "{$this->_schema}distribuicao_uniforme";

    $this->_campos_lista = $this->_todos_campos = " cod_distribuicao_uniforme, ref_cod_aluno, ano, kit_completo, agasalho_qtd, camiseta_curta_qtd,
        camiseta_longa_qtd, meias_qtd, bermudas_tectels_qtd, bermudas_coton_qtd, tenis_qtd, data,
        agasalho_tm, camiseta_curta_tm, camiseta_longa_tm, meias_tm, bermudas_tectels_tm, bermudas_coton_tm, tenis_tm, ref_cod_escola";

    if (is_numeric($cod_distribuicao_uniforme)) {
      $this->cod_distribuicao_uniforme = $cod_distribuicao_uniforme;
    }

    if (is_numeric($ref_cod_aluno)) {
      $this->ref_cod_aluno = $ref_cod_aluno;
    }

    if (is_numeric($ano)) {
      $this->ano = $ano;
    }

    $this->kit_completo = $kit_completo;

    if (is_numeric($agasalho_qtd)) {
      $this->agasalho_qtd = $agasalho_qtd;
    }

    if (is_numeric($camiseta_curta_qtd)) {
      $this->camiseta_curta_qtd = $camiseta_curta_qtd;
    }

    if (is_numeric($camiseta_longa_qtd)) {
      $this->camiseta_longa_qtd = $camiseta_longa_qtd;
    }

    if (is_numeric($meias_qtd)) {
      $this->meias_qtd = $meias_qtd;
    }

    if (is_numeric($bermudas_tectels_qtd)) {
      $this->bermudas_tectels_qtd = $bermudas_tectels_qtd;
    }

    if (is_numeric($bermudas_coton_qtd)) {
      $this->bermudas_coton_qtd = $bermudas_coton_qtd;
    }

    if (is_numeric($tenis_qtd)) {
      $this->tenis_qtd = $tenis_qtd;
    }

    if (is_string($data)) {
      $this->data = $data;
    }
    $this->agasalho_tm = $agasalho_tm;
    $this->camiseta_curta_tm = $camiseta_curta_tm;
    $this->camiseta_longa_tm = $camiseta_longa_tm;
    $this->meias_tm = $meias_tm;
    $this->bermudas_tectels_tm = $bermudas_tectels_tm;
    $this->bermudas_coton_tm = $bermudas_coton_tm;
    $this->tenis_tm = $tenis_tm;
    $this->ref_cod_escola = $ref_cod_escola;

  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ano))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      $campos .= "{$gruda}ref_cod_aluno";
      $valores .= "{$gruda}{$this->ref_cod_aluno}";
      $gruda = ", ";

      $campos .= "{$gruda}ano";
      $valores .= "{$gruda}{$this->ano}";
      $gruda = ", ";

      if(dbBool($this->kit_completo)){
        $campos .= "{$gruda}kit_completo";
        $valores .= "{$gruda} TRUE ";
        $gruda = ", ";
      }else{
        $campos .= "{$gruda}kit_completo";
        $valores .= "{$gruda} FALSE ";
        $gruda = ", ";
      }

      if(is_numeric($this->agasalho_qtd)){
        $campos .= "{$gruda}agasalho_qtd";
        $valores .= "{$gruda}{$this->agasalho_qtd}";
        $gruda = ", ";
      }

      if(is_numeric($this->camiseta_curta_qtd)){
        $campos .= "{$gruda}camiseta_curta_qtd";
        $valores .= "{$gruda}{$this->camiseta_curta_qtd}";
        $gruda = ", ";
      }

      if(is_numeric($this->camiseta_longa_qtd)){
        $campos .= "{$gruda}camiseta_longa_qtd";
        $valores .= "{$gruda}{$this->camiseta_longa_qtd}";
        $gruda = ", ";
      }

      if(is_numeric($this->meias_qtd)){
        $campos .= "{$gruda}meias_qtd";
        $valores .= "{$gruda}{$this->meias_qtd}";
        $gruda = ", ";
      }

      if(is_numeric($this->bermudas_tectels_qtd)){
        $campos .= "{$gruda}bermudas_tectels_qtd";
        $valores .= "{$gruda}{$this->bermudas_tectels_qtd}";
        $gruda = ", ";
      }

      if(is_numeric($this->bermudas_coton_qtd)){
        $campos .= "{$gruda}bermudas_coton_qtd";
        $valores .= "{$gruda}{$this->bermudas_coton_qtd}";
        $gruda = ", ";
      }

      if(is_numeric($this->tenis_qtd)){
        $campos .= "{$gruda}tenis_qtd";
        $valores .= "{$gruda}{$this->tenis_qtd}";
        $gruda = ", ";
      }

      if(is_string($this->data)){
        $campos .= "{$gruda}data";
        $valores .= "{$gruda}'{$this->data}'";
        $gruda = ", ";
      }
      if(is_string($this->agasalho_tm)){
        $campos .= "{$gruda}agasalho_tm";
        $valores .= "{$gruda}'{$this->agasalho_tm}'";
        $gruda = ", ";
      }

      if(is_string($this->camiseta_curta_tm)){
        $campos .= "{$gruda}camiseta_curta_tm";
        $valores .= "{$gruda}'{$this->camiseta_curta_tm}'";
        $gruda = ", ";
      }

      if(is_string($this->camiseta_longa_tm)){
        $campos .= "{$gruda}camiseta_longa_tm";
        $valores .= "{$gruda}'{$this->camiseta_longa_tm}'";
        $gruda = ", ";
      }

      if(is_string($this->meias_tm)){
        $campos .= "{$gruda}meias_tm";
        $valores .= "{$gruda}'{$this->meias_tm}'";
        $gruda = ", ";
      }

      if(is_string($this->bermudas_tectels_tm)){
        $campos .= "{$gruda}bermudas_tectels_tm";
        $valores .= "{$gruda}'{$this->bermudas_tectels_tm}'";
        $gruda = ", ";
      }

      if(is_string($this->bermudas_coton_tm)){
        $campos .= "{$gruda}bermudas_coton_tm";
        $valores .= "{$gruda}'{$this->bermudas_coton_tm}'";
        $gruda = ", ";
      }

      if(is_string($this->tenis_tm)){
        $campos .= "{$gruda}tenis_tm";
        $valores .= "{$gruda}'{$this->tenis_tm}'";
        $gruda = ", ";
      }

      if($this->ref_cod_escola){
        $campos .= "{$gruda}ref_cod_escola";
        $valores .= "{$gruda}{$this->ref_cod_escola}";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->insertId("{$this->_tabela}_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_distribuicao_uniforme)) {
      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->ano))
        $set .= " ano = '{$this->ano}' ";
      else
        return false;

      if (dbBool($this->kit_completo))
        $set .= ",kit_completo = TRUE ";
      else{
        $set .= ",kit_completo = FALSE";
      }

      if (is_numeric($this->agasalho_qtd))
        $set .= ",agasalho_qtd = '{$this->agasalho_qtd}'";
      else{
        $set .= ",agasalho_qtd = NULL";
      }

      if (is_numeric($this->camiseta_curta_qtd))
        $set .= ",camiseta_curta_qtd = '{$this->camiseta_curta_qtd}'";
      else{
        $set .= ",camiseta_curta_qtd = NULL";
      }

      if (is_numeric($this->camiseta_longa_qtd))
        $set .= ",camiseta_longa_qtd = '{$this->camiseta_longa_qtd}'";
      else{
        $set .= ",camiseta_longa_qtd = NULL";
      }

      if (is_numeric($this->meias_qtd))
        $set .= ",meias_qtd = '{$this->meias_qtd}'";
      else{
        $set .= ",meias_qtd = NULL";
      }

      if (is_numeric($this->bermudas_tectels_qtd))
        $set .= ",bermudas_tectels_qtd = '{$this->bermudas_tectels_qtd}'";
      else{
        $set .= ",bermudas_tectels_qtd = NULL";
      }

      if (is_numeric($this->bermudas_coton_qtd))
        $set .= ",bermudas_coton_qtd = '{$this->bermudas_coton_qtd}'";
      else{
        $set .= ",bermudas_coton_qtd = NULL";
      }

      if (is_numeric($this->tenis_qtd))
        $set .= ",tenis_qtd = '{$this->tenis_qtd}'";
      else{
        $set .= ",tenis_qtd = NULL";
      }

      if(is_string($this->data))
        $set .= ",data = '{$this->data}'";

      if ($this->agasalho_tm)
        $set .= ",agasalho_tm = '{$this->agasalho_tm}'";
      else{
        $set .= ",agasalho_tm = NULL";
      }

      if ($this->camiseta_curta_tm)
        $set .= ",camiseta_curta_tm = '{$this->camiseta_curta_tm}'";
      else{
        $set .= ",camiseta_curta_tm = NULL";
      }

      if ($this->camiseta_longa_tm)
        $set .= ",camiseta_longa_tm = '{$this->camiseta_longa_tm}'";
      else{
        $set .= ",camiseta_longa_tm = NULL";
      }

      if ($this->meias_tm)
        $set .= ",meias_tm = '{$this->meias_tm}'";
      else{
        $set .= ",meias_tm = NULL";
      }

      if ($this->bermudas_tectels_tm)
        $set .= ",bermudas_tectels_tm = '{$this->bermudas_tectels_tm}'";
      else{
        $set .= ",bermudas_tectels_tm = NULL";
      }

      if ($this->bermudas_coton_tm)
        $set .= ",bermudas_coton_tm = '{$this->bermudas_coton_tm}'";
      else{
        $set .= ",bermudas_coton_tm = NULL";
      }

      if ($this->tenis_tm)
        $set .= ",tenis_tm = '{$this->tenis_tm}'";
      else{
        $set .= ",tenis_tm = NULL";
      }
      if ($this->ref_cod_escola)
        $set .= ",ref_cod_escola = '{$this->ref_cod_escola}'";
      else{
        $set .= ",ref_cod_escola = NULL";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($ref_cod_aluno = NULL, $ano = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = " WHERE TRUE ";
    // implementar

    if(is_numeric($ref_cod_aluno))
      $filtros .= " AND ref_cod_aluno = {$ref_cod_aluno} ";

    if(is_numeric($ano))
      $filtros .= " AND ano = {$ano} ";

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
    if (is_numeric($this->cod_distribuicao_uniforme)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhePorAlunoAno()
  {
    if (is_numeric($this->ref_cod_aluno) && is_numeric($this->ano)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ano = '{$this->ano}' AND ref_cod_aluno = '{$this->ref_cod_aluno}'");
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
    if (is_numeric($this->cod_distribuicao_uniforme)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'");
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
    if (is_numeric($this->cod_distribuicao_uniforme)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE cod_distribuicao_uniforme = '{$this->cod_distribuicao_uniforme}'";
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