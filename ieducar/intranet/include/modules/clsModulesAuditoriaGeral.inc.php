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
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Utils/SafeJson.php';

/**
 * clsModulesAuditoriaGeral class.
 *
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   @@package_version@@
 */
class clsModulesAuditoriaGeral
{
  const OPERACAO_INCLUSAO = 1;
  const OPERACAO_ALTERACAO = 2;
  const OPERACAO_EXCLUSAO = 3;

  var $_total;
  var $_campos_lista;
  var $_tabela;

  var $id;
  var $usuario_id;
  var $codigo;
  var $rotina;

  var $_campo_order_by;

  function __construct($rotina, $usuario_id, $codigo = 'null', $id = null){
    $this->_campos_lista = 'id,
                            codigo,
                            usuario_id,
                            operacao,
                            rotina,
                            valor_novo,
                            valor_antigo,
                            data_hora';
    $this->_tabela = 'modules.auditoria_geral';

    $this->rotina = $rotina;
    $this->usuario_id = $usuario_id;
    $this->codigo = $codigo;
    $this->id = $id;

    // Seta usuário admin quando não houver usuário pois pode ser API/Novo educação
    if (!$this->usuario_id) $this->usuario_id = 1;
  }

  function removeKeyNaoNumerica($dados) {
    foreach ($dados as $key => $value) {
      if (is_int($key)) {
        unset($dados[$key]);
      }
    }
    return $dados;
  }

  function removeRegistrosNulos($dados) {
    foreach ($dados as $key => $value) {
      if (is_null($value)) {
        unset($dados[$key]);
      }
    }
    return $dados;
  }

  function removeKeysDesnecessarias($dados) {
    $keysDesnecessarias = array("ref_usuario_exc",
                                "ref_usuario_cad",
                                "data_cadastro",
                                "data_exclusao");
    foreach ($dados as $key => $value) {
      if (in_array($key, $keysDesnecessarias)) {
        unset($dados[$key]);
      }
    }
    return $dados;
  }

  function converteArrayDadosParaJson($dados) {
    $dados = $this->removeKeyNaoNumerica($dados);
    $dados = $this->removeKeysDesnecessarias($dados);
    $dados = SafeJson::encode($dados);
    $dados = str_replace("'", "''", $dados);
    return $dados;
  }

  function removeKeysDiferentes($dados, $keysEmComum) {
    foreach ($dados as $key => $value) {
      if (!array_key_exists($key, $keysEmComum)) {
        unset($dados[$key]);
      }
    }
    return $dados;
  }

  function removeKeysIguais($dados, $keysEmComum) {
    foreach ($dados as $key => $value) {
      if (array_key_exists($key, $keysEmComum)) {
        unset($dados[$key]);
      }
    }
    return $dados;
  }

  function keysComValuesIguais($array1, $array2) {
    foreach ($array1 as $key => $value) {
      if ($array1[$key] != $array2[$key]) {
        unset($array1[$key]);
      }
    }
    return $array1;
  }

  function insereAuditoria($operacao, $valorAntigo, $valorNovo) {

    if (!$valorAntigo && !$valorNovo) return;

    if ($valorAntigo) {
      $valorAntigo = "'".$this->converteArrayDadosParaJson($valorAntigo)."'";
    } else {
      $valorAntigo = 'NULL';
    }

    if ($valorNovo){
      $valorNovo = "'".$this->converteArrayDadosParaJson($valorNovo)."'";
    } else {
      $valorNovo = 'NULL';
    }

    $sql = "INSERT INTO modules.auditoria_geral (codigo,
                                                 usuario_id,
                                                 operacao,
                                                 rotina,
                                                 valor_antigo,
                                                 valor_novo,
                                                 data_hora)
                 VALUES ('{$this->codigo}',
                         {$this->usuario_id},
                         {$operacao},
                         '{$this->rotina}',
                         {$valorAntigo},
                         {$valorNovo},
                         NOW())";

       $db = new clsBanco();
     $db->Consulta($sql);
  }

  public function inclusao($dados) {
    $this->insereAuditoria(self::OPERACAO_INCLUSAO, NULL, $dados);
  }

  public function alteracao($valorAntigo, $valorNovo) {
    $this->insereAuditoria(self::OPERACAO_ALTERACAO, $valorAntigo, $valorNovo);
  }

  public function exclusao($dados) {
    $this->insereAuditoria(self::OPERACAO_EXCLUSAO, $dados, NULL);
  }

  function lista($rotina, $usuario, $dataInicial, $dataFinal, $horaInicial, $horaFinal, $operacao, $codigo)
  {
    $filtros = "";

    $whereAnd = " WHERE ";

    if(is_numeric($this->id)) {
      $filtros .= "{$whereAnd} id = {$this->id}";
      $whereAnd = " AND ";
    }

    if(is_string($rotina)) {
      $filtros .= "{$whereAnd} rotina ILIKE '%{$rotina}%'";
      $whereAnd = " AND ";
    }

    if(is_numeric($operacao)) {
      $filtros .= "{$whereAnd} operacao = {$operacao}";
      $whereAnd = " AND ";
    }

    if(is_string($codigo)) {
      $filtros .= "{$whereAnd} codigo = '{$codigo}'";
      $whereAnd = " AND ";
    }

    if(is_string($usuario)) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1
                                         FROM portal.funcionario
                                        WHERE funcionario.ref_cod_pessoa_fj = auditoria_geral.usuario_id
                                          AND funcionario.matricula = '{$usuario}')";
      $whereAnd = " AND ";
    }

    if(is_string($dataInicial)) {
      $filtros .= "{$whereAnd} data_hora::date >= '{$dataInicial}'";
      $whereAnd = " AND ";
    }

    if(is_string($dataFinal)) {
      $filtros .= "{$whereAnd} data_hora::date <= '{$dataFinal}'";
      $whereAnd = " AND ";
    }

    if(is_string($horaInicial)) {
      $filtros .= "{$whereAnd} data_hora::time >= '{$horaInicial}'";
      $whereAnd = " AND ";
    }

    if(is_string($horaFinal)) {
      $filtros .= "{$whereAnd} data_hora::time <= '{$horaFinal}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count( explode( ",", $this->_campos_lista ) );
    $resultado = array();

    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ";
    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

    $db->Consulta( $sql );

    if( $countCampos > 1 )
    {
      while ( $db->ProximoRegistro() )
      {
        $tupla = $db->Tupla();

        $tupla["_total"] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else
    {
      while ( $db->ProximoRegistro() )
      {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }
    if( count( $resultado ) )
    {
      return $resultado;
    }
    return false;
  }

  function setLimite( $intLimiteQtd, $intLimiteOffset = null )
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

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

  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}
