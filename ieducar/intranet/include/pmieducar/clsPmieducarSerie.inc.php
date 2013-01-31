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
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * clsPmieducarSerie class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @todo      A verificação de regra de avaliação no construtor é falha pois
 *   ignora os casos que a foreign key de curso não é informada. Atribuir
 *   um foreign key de instituição a tabelam pmieducar.serie resolveria este
 *   problema.
 * @version   @@package_version@@
 */
class clsPmieducarSerie
{
  var $cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_curso;
  var $nm_serie;
  var $etapa_curso;
  var $concluinte;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $intervalo;
  var $regra_avaliacao_id;

  var $idade_inicial;
  var $idade_final;

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
  function clsPmieducarSerie($cod_serie = NULL, $ref_usuario_exc = NULL,
    $ref_usuario_cad = NULL, $ref_cod_curso = NULL, $nm_serie = NULL,
    $etapa_curso = NULL, $concluinte = NULL, $carga_horaria = NULL,
    $data_cadastro = NULL, $data_exclusao = NULL, $ativo = NULL, $intervalo = NULL,
    $idade_inicial = NULL, $idade_final = NULL, $regra_avaliacao_id = NULL, $observacao_historico = null,
    $dias_letivos = null)
  {
    $db = new clsBanco();
    $this->_schema = "pmieducar.";
    $this->_tabela = "{$this->_schema}serie";

    $this->_campos_lista = $this->_todos_campos = "s.cod_serie, s.ref_usuario_exc, s.ref_usuario_cad, s.ref_cod_curso, s.nm_serie, s.etapa_curso, s.concluinte, s.carga_horaria, s.data_cadastro, s.data_exclusao, s.ativo, s.intervalo, s.idade_inicial, s.idade_final, s.regra_avaliacao_id, s.observacao_historico, s.dias_letivos";

    if (is_numeric($ref_cod_curso)) {
      if (class_exists("clsPmieducarCurso")) {
        $tmp_obj = new clsPmieducarCurso($ref_cod_curso);
        $curso = $tmp_obj->detalhe();
        if (FALSE != $curso) {
          $this->ref_cod_curso = $ref_cod_curso;
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.curso WHERE cod_curso = '{$ref_cod_curso}'")) {
          $this->ref_cod_curso = $ref_cod_curso;
        }
      }
    }

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        else if( method_exists($tmp_obj, "detalhe")) {
          if($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        else if(method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }
    }

    // Atribuibui a identificação de regra de avaliação
    if (!is_null($regra_avaliacao_id) && is_numeric($regra_avaliacao_id)) {
      $mapper = new RegraAvaliacao_Model_RegraDataMapper();

      if (isset($curso)) {
        $regras = $mapper->findAll(array(),
          array('id' => $regra_avaliacao_id, 'instituicao' => $curso['ref_cod_instituicao'])
        );

        if (1 == count($regras)) {
          $regra = $regras[0];
        }
      }
      else {
        $regra = $mapper->find($regra_avaliacao_id);
      }

      // Verificação fraca pois deixa ser uma regra de outra instituição
      if (isset($regra)) {
        $this->regra_avaliacao_id = $regra->id;
      }
    }

    if (is_numeric($cod_serie)) {
      $this->cod_serie = $cod_serie;
    }

    if (is_string($nm_serie)) {
      $this->nm_serie = $nm_serie;
    }

    if (is_numeric($etapa_curso)) {
      $this->etapa_curso = $etapa_curso;
    }

    if (is_numeric($concluinte)) {
      $this->concluinte = $concluinte;
    }

    if (is_numeric($carga_horaria)) {
      $this->carga_horaria = $carga_horaria;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_numeric($intervalo)) {
      $this->intervalo = $intervalo;
    }

    if (is_numeric($idade_inicial)) {
      $this->idade_inicial = $idade_inicial;
    }

    if (is_numeric($idade_final)) {
      $this->idade_final = $idade_final;
    }

    $this->observacao_historico = $observacao_historico;
    $this->dias_letivos         = $dias_letivos;
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_curso) &&
      is_string($this->nm_serie) && is_numeric($this->etapa_curso) &&
      is_numeric($this->concluinte) && is_numeric($this->carga_horaria) &&
      is_numeric($this->intervalo) && is_numeric($this->dias_letivos))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_curso)) {
        $campos .= "{$gruda}ref_cod_curso";
        $valores .= "{$gruda}'{$this->ref_cod_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_serie)) {
        $campos .= "{$gruda}nm_serie";
        $valores .= "{$gruda}'{$this->nm_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->etapa_curso)) {
        $campos .= "{$gruda}etapa_curso";
        $valores .= "{$gruda}'{$this->etapa_curso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->concluinte)) {
        $campos .= "{$gruda}concluinte";
        $valores .= "{$gruda}'{$this->concluinte}'";
        $gruda = ", ";
      }

      if (is_numeric($this->carga_horaria)) {
        $campos .= "{$gruda}carga_horaria";
        $valores .= "{$gruda}'{$this->carga_horaria}'";
        $gruda = ", ";
      }

      if (is_numeric($this->idade_inicial)) {
        $campos .= "{$gruda}idade_inicial";
        $valores .= "{$gruda}'{$this->idade_inicial}'";
        $gruda = ", ";
      }

      if (is_numeric($this->idade_final)) {
        $campos .= "{$gruda}idade_final";
        $valores .= "{$gruda}'{$this->idade_final}'";
        $gruda = ", ";
      }

      if (is_numeric($this->regra_avaliacao_id)) {
        $campos .= "{$gruda}regra_avaliacao_id";
        $valores .= "{$gruda}'{$this->regra_avaliacao_id}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ", ";

      if (is_numeric($this->intervalo)) {
        $campos  .= "{$gruda}intervalo";
        $valores .= "{$gruda}'{$this->intervalo}'";
        $gruda    = ", ";
      }

      if(is_string($this->observacao_historico)){
        $campos .= "{$gruda}observacao_historico";
        $valores .= "{$gruda}'{$this->observacao_historico}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dias_letivos)) {
        $campos  .= "{$gruda}dias_letivos";
        $valores .= "{$gruda}'{$this->dias_letivos}'";
        $gruda    = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_cod_serie_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_serie) && is_numeric($this->ref_usuario_exc)) {
      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_curso)) {
        $set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_serie)) {
        $set .= "{$gruda}nm_serie = '{$this->nm_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->etapa_curso)) {
        $set .= "{$gruda}etapa_curso = '{$this->etapa_curso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->concluinte)) {
        $set .= "{$gruda}concluinte = '{$this->concluinte}'";
        $gruda = ", ";
      }

      if (is_numeric($this->carga_horaria)) {
        $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
        $gruda = ", ";
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->intervalo)) {
        $set .= "{$gruda}intervalo = '{$this->intervalo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->idade_inicial)) {
        $set .= "{$gruda}idade_inicial = '{$this->idade_inicial}'";
        $gruda = ", ";
      }
      else {
        $set .= "{$gruda}idade_inicial = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->idade_final)) {
        $set .= "{$gruda}idade_final = '{$this->idade_final}'";
        $gruda = ", ";
      }
      else {
        $set .= "{$gruda}idade_final = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->regra_avaliacao_id)) {
        $set .= "{$gruda}regra_avaliacao_id = '{$this->regra_avaliacao_id}'";
        $gruda = ", ";
      }

      if(is_string($this->observacao_historico)){
        $set .= "{$gruda}observacao_historico = '{$this->observacao_historico}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dias_letivos)) {
        $set .= "{$gruda}dias_letivos = '{$this->dias_letivos}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_serie = '{$this->cod_serie}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_serie = NULL, $int_ref_usuario_exc = NULL,
    $int_ref_usuario_cad = NULL, $int_ref_cod_curso = NULL, $str_nm_serie = NULL,
    $int_etapa_curso = NULL, $int_concluinte = NULL, $int_carga_horaria = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
    $int_ativo = NULL, $int_ref_cod_instituicao = NULL, $int_intervalo = NULL,
    $int_idade_inicial = NULL, $int_idade_final = NULL, $int_ref_cod_escola = NULL,
    $regra_avaliacao_id = NULL)
  {
    $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao FROM {$this->_tabela} s, {$this->_schema}curso c";

    $whereAnd = " AND ";
    $filtros = " WHERE s.ref_cod_curso = c.cod_curso";

    if (is_numeric($int_cod_serie)) {
      $filtros .= "{$whereAnd} s.cod_serie = '{$int_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} s.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} s.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_serie)) {
      $filtros .= "{$whereAnd} s.nm_serie LIKE '%{$str_nm_serie}%'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_etapa_curso)) {
      $filtros .= "{$whereAnd} s.etapa_curso = '{$int_etapa_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_concluinte)) {
      $filtros .= "{$whereAnd} s.concluinte = '{$int_concluinte}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_carga_horaria)) {
      $filtros .= "{$whereAnd} s.carga_horaria = '{$int_carga_horaria}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} s.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} s.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} s.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} s.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($regra_avaliacao_id)) {
      $filtros .= "{$whereAnd} s.regra_avaliacao_id = '{$regra_avaliacao_id}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} s.ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} s.ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} c.ref_cod_instituicao = '$int_ref_cod_instituicao'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_intervalo)) {
      $filtros .= "{$whereAnd} intervalo = '{$int_intervalo}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_idade_inicial)) {
      $filtros .= "{$whereAnd} idade_inicial = '{$int_idade_inicial}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_idade_final)) {
      $filtros .= "{$whereAnd} idade_final= '{$int_idade_final}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} EXISTS ( SELECT 1 FROM pmieducar.escola_serie es WHERE s.cod_serie = es.ref_cod_serie AND es.ref_cod_escola = '{$int_ref_cod_escola}') ";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} s, "
                        . "{$this->_schema}curso c {$filtros}");

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
    if (is_numeric($this->cod_serie) && is_numeric($this->ref_cod_curso)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_serie = '{$this->cod_serie}' AND s.ref_cod_curso = '{$this->ref_cod_curso}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    elseif (is_numeric($this->cod_serie)) {
      $db = new clsBanco();
      $db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} s WHERE s.cod_serie = '{$this->cod_serie}'" );
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro ou FALSE caso não exista.
   * @return array|bool
   */
  function existe()
  {
    if (is_numeric($this->cod_serie)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_serie = '{$this->cod_serie}'");
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
    if (is_numeric($this->cod_serie) && is_numeric($this->ref_usuario_exc)) {
      $this->ativo = 0;
      return $this->edita();
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

  /**
   * Seleciona as série que não estejam cadastradas na escola.
   *
   * @param int $ref_cod_curso
   * @param int $ref_cod_escola
   * @return array
   */
  function getNotEscolaSerie($ref_cod_curso, $ref_cod_escola)
  {
    $db = new clsBanco();
    $sql = "SELECT *
            FROM
              pmieducar.serie s
            WHERE s.ref_cod_curso = '{$ref_cod_curso}'
            AND s.cod_serie NOT IN
            (
              SELECT es.ref_cod_serie
              FROM pmieducar.escola_serie es
              WHERE es.ref_cod_escola = '{$ref_cod_escola}'
            )";

    $db->Consulta($sql);

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $resultado[] = $tupla;
    }

    return $resultado;
  }
}
