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
 * @package   iEd_Public
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/public/geral.inc.php';

/**
 * clsPublicBairro class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Public
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPublicBairro
{
  var $idmun;
  var $geom;
  var $idbai;
  var $nome;
  var $idpes_rev;
  var $data_rev;
  var $origem_gravacao;
  var $idpes_cad;
  var $data_cad;
  var $operacao;
  var $idsis_rev;
  var $idsis_cad;
  var $zona_localizacao;

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
   *
   * @param int     $idmun
   * @param string  $geom
   * @param int     $idbai
   * @param string  $nome
   * @param int     $idpes_rev
   * @param string  $data_rev
   * @param string  $origem_gravacao
   * @param int     $idpes_cad
   * @param string  $data_cad
   * @param string  $operacao
   * @param int     $idsis_rev
   * @param int     $idsis_cad
   * @param int     $zona_localizacao
   */
  function clsPublicBairro($idmun = NULL, $geom = NULL, $idbai = NULL,
    $nome = NULL, $idpes_rev = NULL, $data_rev = NULL, $origem_gravacao = NULL,
    $idpes_cad = NULL, $data_cad = NULL, $operacao = NULL, $idsis_rev = NULL,
    $idsis_cad = NULL, $zona_localizacao = 1)
  {
    $db = new clsBanco();
    $this->_schema = 'public.';
    $this->_tabela = $this->_schema . 'bairro';

    $this->_campos_lista = $this->_todos_campos = 'b.idmun, b.geom, b.idbai, ' .
      'b.nome, b.idpes_rev, b.data_rev, b.origem_gravacao, b.idpes_cad, ' .
      'b.data_cad, b.operacao, b.idsis_rev, b.idsis_cad, b.zona_localizacao';

    if (is_numeric($idsis_rev)) {
      if (class_exists('clsAcessoSistema')) {
        $tmp_obj = new clsAcessoSistema($idsis_rev);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->idsis_rev = $idsis_rev;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->idsis_rev = $idsis_rev;
          }
        }
      }
      else {
        if ($db->CampoUnico(sprintf(
          'SELECT 1 FROM acesso.sistema WHERE idsis = \'%d\'', $idsis_rev
        ))) {
          $this->idsis_rev = $idsis_rev;
        }
      }
    }

    if (is_numeric($idsis_cad)) {
      if (class_exists('clsAcessoSistema')) {
        $tmp_obj = new clsAcessoSistema($idsis_cad);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->idsis_cad = $idsis_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->idsis_cad = $idsis_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico(sprintf(
          'SELECT 1 FROM acesso.sistema WHERE idsis = \'%d\'', $idsis_cad
        ))) {
          $this->idsis_cad = $idsis_cad;
        }
      }
    }

    if (is_numeric($idpes_rev)) {
      if (class_exists('clsCadastroPessoa')) {
        $tmp_obj = new clsCadastroPessoa($idpes_rev);

        if (method_exists($tmp_obj, 'existe')) {
          if($tmp_obj->existe()) {
            $this->idpes_rev = $idpes_rev;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->idpes_rev = $idpes_rev;
          }
        }
      }
      else {
        if ($db->CampoUnico(sprintf(
          'SELECT 1 FROM cadastro.pessoa WHERE idpes = \'%d\'', $idpes_rev
        ))) {
          $this->idpes_rev = $idpes_rev;
        }
      }
    }

    if (is_numeric($idpes_cad)) {
      if (class_exists('clsCadastroPessoa')) {
        $tmp_obj = new clsCadastroPessoa($idpes_cad);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->idpes_cad = $idpes_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->idpes_cad = $idpes_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico(sprintf(
          'SELECT 1 FROM cadastro.pessoa WHERE idpes = \'%d\'', $idpes_cad
        ))) {
          $this->idpes_cad = $idpes_cad;
        }
      }
    }

    if (is_numeric($idmun)) {
      if (class_exists('clsMunicipio')) {
        $tmp_obj = new clsMunicipio($idmun);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->idmun = $idmun;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->idmun = $idmun;
          }
        }
      }
      else {
        if ($db->CampoUnico(sprintf(
          'SELECT 1 FROM municipio WHERE idmun = \'%d\'', $idmun
        ))) {
          $this->idmun = $idmun;
        }
      }
    }

    if (is_string($geom)) {
      $this->geom = $geom;
    }

    if (is_numeric($idbai)) {
      $this->idbai = $idbai;
    }

    if (is_string($nome)) {
      $this->nome = $nome;
    }

    if (is_string($data_rev)) {
      $this->data_rev = $data_rev;
    }

    if (is_string($origem_gravacao)) {
      $this->origem_gravacao = $origem_gravacao;
    }

    if (is_string($data_cad)) {
      $this->data_cad = $data_cad;
    }

    if (is_string($operacao)) {
      $this->operacao = $operacao;
    }

    if (is_numeric($zona_localizacao)) {
      $this->zona_localizacao = $zona_localizacao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->idmun) && is_string($this->nome) &&
      is_string($this->origem_gravacao) && is_string($this->operacao) &&
      is_numeric($this->idsis_cad)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->idmun)) {
        $campos  .= "{$gruda}idmun";
        $valores .= "{$gruda}'{$this->idmun}'";
        $gruda    = ', ';
      }

      if (is_string($this->geom)) {
        $campos  .= "{$gruda}geom";
        $valores .= "{$gruda}'{$this->geom}'";
        $gruda    = ', ';
      }

      if (is_string($this->nome)) {
        $campos  .= "{$gruda}nome";
        $valores .= "{$gruda}'" . addslashes($this->nome) . "'";
        $gruda    = ', ';
      }

      if (is_numeric($this->idpes_rev)) {
        $campos  .= "{$gruda}idpes_rev";
        $valores .= "{$gruda}'{$this->idpes_rev}'";
        $gruda    = ', ';
      }

      if (is_string( $this->data_rev)) {
        $campos  .= "{$gruda}data_rev";
        $valores .= "{$gruda}'{$this->data_rev}'";
        $gruda    = ', ';
      }

      if (is_string($this->origem_gravacao)) {
        $campos  .= "{$gruda}origem_gravacao";
        $valores .= "{$gruda}'{$this->origem_gravacao}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->idpes_cad)) {
        $campos  .= "{$gruda}idpes_cad";
        $valores .= "{$gruda}'{$this->idpes_cad}'";
        $gruda    = ', ';
      }

      $campos  .= "{$gruda}data_cad";
      $valores .= "{$gruda}NOW()";
      $gruda    = ', ';

      if (is_string($this->operacao)) {
        $campos  .= "{$gruda}operacao";
        $valores .= "{$gruda}'{$this->operacao}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->idsis_rev)) {
        $campos  .= "{$gruda}idsis_rev";
        $valores .= "{$gruda}'{$this->idsis_rev}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->idsis_cad)) {
        $campos  .= "{$gruda}idsis_cad";
        $valores .= "{$gruda}'{$this->idsis_cad}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->zona_localizacao)) {
        $campos  .= "{$gruda}zona_localizacao";
        $valores .= "{$gruda}'{$this->zona_localizacao}'";
        $gruda    = ', ';
      }

      $db->Consulta(sprintf(
        "INSERT INTO %s (%s) VALUES (%s)",
        $this->_tabela, $campos, $valores
      ));

      return $db->InsertId('seq_bairro');
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->idbai)) {
      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->idmun)) {
        $set  .= "{$gruda}idmun = '{$this->idmun}'";
        $gruda = ', ';
      }

      if (is_string($this->geom)) {
        $set  .= "{$gruda}geom = '{$this->geom}'";
        $gruda = ', ';
      }

      if (is_string($this->nome)) {
        $set .= "{$gruda}nome = '" . addslashes($this->nome) . "'";
        $gruda = ', ';
      }

      if (is_numeric($this->idpes_rev)) {
        $set .= "{$gruda}idpes_rev = '{$this->idpes_rev}'";
        $gruda = ', ';
      }

      if (is_string($this->data_rev)) {
        $set  .= "{$gruda}data_rev = '{$this->data_rev}'";
        $gruda = ', ';
      }

      if (is_string($this->origem_gravacao)) {
        $set  .= "{$gruda}origem_gravacao = '{$this->origem_gravacao}'";
        $gruda = ', ';
      }

      if (is_numeric($this->idpes_cad)) {
        $set  .= "{$gruda}idpes_cad = '{$this->idpes_cad}'";
        $gruda = ', ';
      }

      if (is_string($this->data_cad)) {
        $set  .= "{$gruda}data_cad = '{$this->data_cad}'";
        $gruda = ', ';
      }

      if (is_string($this->operacao)) {
        $set  .= "{$gruda}operacao = '{$this->operacao}'";
        $gruda = ', ';
      }

      if (is_numeric($this->idsis_rev)) {
        $set  .= "{$gruda}idsis_rev = '{$this->idsis_rev}'";
        $gruda = ', ';
      }

      if (is_numeric($this->idsis_cad)) {
        $set  .= "{$gruda}idsis_cad = '{$this->idsis_cad}'";
        $gruda = ', ';
      }

      if (is_numeric($this->zona_localizacao)) {
        $set  .= "{$gruda}zona_localizacao = '{$this->zona_localizacao}'";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta(sprintf(
          'UPDATE %s SET %s WHERE idbai = \'%d\'',
          $this->_tabela, $set, $this->idbai
        ));

        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   *
   * @param int     $int_idmun
   * @param string  $str_geom
   * @param string  $str_nome
   * @param int     $int_idpes_rev
   * @param string  $date_data_rev_ini
   * @param string  $date_data_rev_fim
   * @param string  $str_origem_gravacao
   * @param int     $int_idpes_cad
   * @param string  $date_data_cad_ini
   * @param string  $date_data_cad_fim
   * @param string  $str_operacao
   * @param int     $int_idsis_rev
   * @param int     $int_idsis_cad
   * @param int     $zona_localizacao
   * @return array
   */
  function lista($int_idmun = NULL, $str_geom = NULL, $str_nome = NULL,
    $int_idpes_rev = NULL, $date_data_rev_ini = NULL, $date_data_rev_fim = NULL,
    $str_origem_gravacao = NULL, $int_idpes_cad = NULL, $date_data_cad_ini = NULL,
    $date_data_cad_fim = NULL, $str_operacao = NULL, $int_idsis_rev = NULL,
    $int_idsis_cad = NULL, $int_idpais = NULL, $str_sigla_uf = NULL, $int_idbai = NULL,
    $zona_localizacao = NULL)
  {
    $select = ', m.nome AS nm_municipio, m.sigla_uf, u.nome AS nm_estado, u.idpais, p.nome AS nm_pais ';
    $from   = 'b, public.municipio m, public.uf u, public.pais p ';

    $sql = sprintf(
      'SELECT %s %s FROM %s %s', $this->_campos_lista, $select, $this->_tabela, $from
    );

    $whereAnd = ' AND ';

    $filtros = ' WHERE b.idmun = m.idmun AND m.sigla_uf = u.sigla_uf AND u.idpais = p.idpais ';

    if (is_numeric($int_idmun)) {
      $filtros .= "{$whereAnd} b.idmun = '{$int_idmun}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_geom)) {
      $filtros .= "{$whereAnd} b.geom LIKE '%{$str_geom}%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idbai)) {
      $filtros .= "{$whereAnd} b.idbai = '{$int_idbai}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nome)) {
      $filtros .= "{$whereAnd} b.nome LIKE E'%" . addslashes($str_nome) . "%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idpes_rev)) {
      $filtros .= "{$whereAnd} b.idpes_rev = '{$int_idpes_rev}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_rev_ini)) {
      $filtros .= "{$whereAnd} b.data_rev >= '{$date_data_rev_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_rev_fim)) {
      $filtros .= "{$whereAnd} b.data_rev <= '{$date_data_rev_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_origem_gravacao)) {
      $filtros .= "{$whereAnd} b.origem_gravacao LIKE '%{$str_origem_gravacao}%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idpes_cad)) {
      $filtros .= "{$whereAnd} b.idpes_cad = '{$int_idpes_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cad_ini)) {
      $filtros .= "{$whereAnd} b.data_cad >= '{$date_data_cad_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cad_fim)) {
      $filtros .= "{$whereAnd} b.data_cad <= '{$date_data_cad_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_operacao)) {
      $filtros .= "{$whereAnd} b.operacao LIKE '%{$str_operacao}%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idsis_rev)) {
      $filtros .= "{$whereAnd} b.idsis_rev = '{$int_idsis_rev}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idsis_cad)) {
      $filtros .= "{$whereAnd} b.idsis_cad = '{$int_idsis_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($zona_localizacao)) {
      $filtros .= "{$whereAnd} b.zona_localizacao = '{$zona_localizacao}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idpais)) {
      $filtros .= "{$whereAnd} p.idpais = '{$int_idpais}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_sigla_uf)) {
      $filtros .= "{$whereAnd} u.sigla_uf = '{$str_sigla_uf}'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();

    $countCampos = count(explode(', ', $this->_campos_lista));
    $resultado   = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico(sprintf(
      'SELECT COUNT(0) FROM %s %s %s', $this->_tabela, $from, $filtros
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
    if (is_numeric($this->idbai)) {
      $db = new clsBanco();

      $sql = sprintf(
        'SELECT %s FROM %s b WHERE b.idbai = \'%d\'',
        $this->_todos_campos, $this->_tabela, $this->idbai
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
    if (is_numeric($this->idbai)) {
      $db = new clsBanco();

      $sql = sprintf(
        'SELECT 1 FROM %s WHERE idbai = \'%d\'',
        $this->_tabela, $this->idbai
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
    if (is_numeric($this->idbai)) {
      $db = new clsBanco();

      $sql = sprintf(
        'DELETE FROM %s WHERE idbai = \'%d\'',
        $this->_tabela, $this->idbai
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