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

/**
 * clsPmieducarInstituicao class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarInstituicao
{
  var $cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_idtlog;
  var $ref_sigla_uf;
  var $cep;
  var $cidade;
  var $bairro;
  var $logradouro;
  var $numero;
  var $complemento;
  var $nm_responsavel;
  var $ddd_telefone;
  var $telefone;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $nm_instituicao;

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
  function clsPmieducarInstituicao($cod_instituicao = NULL, $ref_usuario_exc = NULL,
    $ref_usuario_cad = NULL, $ref_idtlog = NULL, $ref_sigla_uf = NULL, $cep = NULL,
    $cidade = NULL, $bairro = NULL, $logradouro = NULL, $numero = NULL,
    $complemento = NULL, $nm_responsavel = NULL, $ddd_telefone = NULL,
    $telefone = NULL, $data_cadastro = NULL, $data_exclusao = NULL,
    $ativo = NULL, $nm_instituicao = NULL)
  {
    $db = new clsBanco();
    $this->_schema = "pmieducar.";
    $this->_tabela = "{$this->_schema}instituicao";

    $this->_campos_lista = $this->_todos_campos = "cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_idtlog, ref_sigla_uf, cep, cidade, bairro, logradouro, numero, complemento, nm_responsavel, ddd_telefone, telefone, data_cadastro, data_exclusao, ativo, nm_instituicao";

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
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

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
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
    if (is_string($ref_idtlog)) {
      if (class_exists('clsTipoLogradouro')) {
        $tmp_obj = new clsTipoLogradouro($ref_idtlog);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_idtlog = $ref_idtlog;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_idtlog = $ref_idtlog;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM urbano.tipo_logradouro WHERE idtlog = '{$ref_idtlog}'")) {
          $this->ref_idtlog = $ref_idtlog;
        }
      }
    }

    if (is_numeric($cod_instituicao)) {
      $this->cod_instituicao = $cod_instituicao;
    }

    if (is_string($ref_sigla_uf)) {
      $this->ref_sigla_uf = $ref_sigla_uf;
    }

    if (is_numeric($cep)) {
      $this->cep = $cep;
    }

    if (is_string($cidade)) {
      $this->cidade = $cidade;
    }

    if (is_string($bairro)) {
      $this->bairro = $bairro;
    }

    if (is_string($logradouro)) {
      $this->logradouro = $logradouro;
    }

    if (is_numeric($numero)) {
      $this->numero = $numero;
    }

    if (is_string($complemento)) {
      $this->complemento = $complemento;
    }

    if (is_string($nm_responsavel)) {
      $this->nm_responsavel = $nm_responsavel;
    }

    if (is_numeric($ddd_telefone)) {
      $this->ddd_telefone = $ddd_telefone;
    }

    if (is_numeric($telefone)) {
      $this->telefone = $telefone;
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

    if (is_string($nm_instituicao)) {
      $this->nm_instituicao = $nm_instituicao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_usuario_cad) && is_string($this->ref_idtlog) &&
      is_string($this->ref_sigla_uf) && is_numeric($this->cep) &&
      is_string($this->cidade) && is_string($this->bairro) &&
      is_string($this->logradouro) && is_string($this->nm_responsavel) &&
      is_numeric($this->ativo) && is_string($this->nm_instituicao))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_usuario_exc)) {
        $campos .= "{$gruda}ref_usuario_exc";
        $valores .= "{$gruda}'{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_string($this->ref_idtlog)) {
        $campos .= "{$gruda}ref_idtlog";
        $valores .= "{$gruda}'{$this->ref_idtlog}'";
        $gruda = ", ";
      }

      if (is_string($this->ref_sigla_uf)) {
        $campos .= "{$gruda}ref_sigla_uf";
        $valores .= "{$gruda}'{$this->ref_sigla_uf}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cep)) {
        $campos .= "{$gruda}cep";
        $valores .= "{$gruda}'{$this->cep}'";
        $gruda = ", ";
      }

      if (is_string($this->cidade)) {
        $campos .= "{$gruda}cidade";
        $valores .= "{$gruda}'{$this->cidade}'";
        $gruda = ", ";
      }

      if (is_string($this->bairro)) {
        $campos .= "{$gruda}bairro";
        $valores .= "{$gruda}'{$this->bairro}'";
        $gruda = ", ";
      }

      if (is_string($this->logradouro)) {
        $campos .= "{$gruda}logradouro";
        $valores .= "{$gruda}'{$this->logradouro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->numero)) {
        $campos .= "{$gruda}numero";
        $valores .= "{$gruda}'{$this->numero}'";
        $gruda = ", ";
      }

      if (is_string($this->complemento)) {
        $campos .= "{$gruda}complemento";
        $valores .= "{$gruda}'{$this->complemento}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_responsavel)) {
        $campos .= "{$gruda}nm_responsavel";
        $valores .= "{$gruda}'{$this->nm_responsavel}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ddd_telefone)) {
        $campos .= "{$gruda}ddd_telefone";
        $valores .= "{$gruda}'{$this->ddd_telefone}'";
        $gruda = ", ";
      }

      if (is_numeric($this->telefone)) {
        $campos .= "{$gruda}telefone";
        $valores .= "{$gruda}'{$this->telefone}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      if (is_numeric($this->ativo)) {
        $campos .= "{$gruda}ativo";
        $valores .= "{$gruda}'{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_instituicao)) {
        $campos .= "{$gruda}nm_instituicao";
        $valores .= "{$gruda}'{$this->nm_instituicao}'";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_cod_instituicao_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_instituicao)) {
      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_string($this->ref_idtlog)) {
        $set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
        $gruda = ", ";
      }

      if (is_string($this->ref_sigla_uf)) {
        $set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cep)) {
        $set .= "{$gruda}cep = '{$this->cep}'";
        $gruda = ", ";
      }

      if (is_string($this->cidade)) {
        $set .= "{$gruda}cidade = '{$this->cidade}'";
        $gruda = ", ";
      }

      if (is_string($this->bairro)) {
        $set .= "{$gruda}bairro = '{$this->bairro}'";
        $gruda = ", ";
      }

      if (is_string($this->logradouro)) {
        $set .= "{$gruda}logradouro = '{$this->logradouro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->numero)) {
        $set .= "{$gruda}numero = '{$this->numero}'";
        $gruda = ", ";
      }

      if (is_string($this->complemento)) {
        $set .= "{$gruda}complemento = '{$this->complemento}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_responsavel)) {
        $set .= "{$gruda}nm_responsavel = '{$this->nm_responsavel}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ddd_telefone)) {
        $set .= "{$gruda}ddd_telefone = '{$this->ddd_telefone}'";
        $gruda = ", ";
      }

      if (is_numeric($this->telefone)) {
        $set .= "{$gruda}telefone = '{$this->telefone}'";
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

      if (is_string($this->nm_instituicao)) {
        $set .= "{$gruda}nm_instituicao = '{$this->nm_instituicao}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_instituicao = '{$this->cod_instituicao}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_instituicao = NULL, $str_ref_sigla_uf = NULL,
    $int_cep = NULL, $str_cidade = NULL, $str_bairro = NULL, $str_logradouro = NULL,
    $int_numero = NULL, $str_complemento = NULL, $str_nm_responsavel = NULL,
    $int_ddd_telefone = NULL, $int_telefone = NULL, $date_data_cadastro = NULL,
    $date_data_exclusao = NULL, $int_ativo = NULL, $str_nm_instituicao = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_cod_instituicao)) {
      $filtros .= "{$whereAnd} cod_instituicao = '{$int_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_ref_sigla_uf)) {
      $filtros .= "{$whereAnd} ref_sigla_uf LIKE '%{$str_ref_sigla_uf}%'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_cep)) {
      $filtros .= "{$whereAnd} cep = '{$int_cep}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_cidade)) {
      $filtros .= "{$whereAnd} cidade LIKE '%{$str_cidade}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_bairro)) {
      $filtros .= "{$whereAnd} bairro LIKE '%{$str_bairro}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_logradouro)) {
      $filtros .= "{$whereAnd} logradouro LIKE '%{$str_logradouro}%'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_numero)) {
      $filtros .= "{$whereAnd} numero = '{$int_numero}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_complemento)) {
      $filtros .= "{$whereAnd} complemento LIKE '%{$str_complemento}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_responsavel)) {
      $filtros .= "{$whereAnd} nm_responsavel LIKE '%{$str_nm_responsavel}%'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ddd_telefone)) {
      $filtros .= "{$whereAnd} ddd_telefone = '{$int_ddd_telefone}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_telefone)) {
      $filtros .= "{$whereAnd} telefone = '{$int_telefone}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_instituicao)) {
      $filtros .= "{$whereAnd} nm_instituicao LIKE '%{$str_nm_instituicao}%'";
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
    if (is_numeric($this->cod_instituicao)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos},fcn_upper_nrm(nm_instituicao) as nm_instituicao_upper FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'");
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
    if (is_numeric($this->cod_instituicao)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'");
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
    if (is_numeric($this->cod_instituicao)) {
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