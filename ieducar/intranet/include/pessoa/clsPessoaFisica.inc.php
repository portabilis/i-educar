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
 * @package   iEd_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

/**
 * clsPessoaFisica class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPessoaFisica extends clsPessoaFj
{
  var $idpes;
  var $data_nasc;
  var $sexo;
  var $idpes_mae;
  var $idpes_pai;
  var $idpes_responsavel;
  var $idesco;
  var $ideciv;
  var $idpes_con;
  var $data_uniao;
  var $data_obito;
  var $nacionalidade;
  var $idpais_estrangeiro;
  var $data_chagada_brasil;
  var $idmun_nascimento;
  var $ultima_empresa;
  var $idocup;
  var $nome_mae;
  var $nome_pai;
  var $nome_conjuge;
  var $nome_responsavel;
  var $justificativa_provisorio;
  var $cpf;
  var $ref_cod_religiao;
  var $tipo_endereco;

  var $banco           = 'pmi';
  var $schema_cadastro = 'cadastro';

  /**
   * Construtor.
   */
  function  clsPessoaFisica($int_idpes = FALSE, $numeric_cpf = FALSE,
    $date_data_nasc = FALSE, $str_sexo = FALSE, $int_idpes_mae = FALSE,
    $int_idpes_pai = FALSE)
  {
    $this->idpes = $int_idpes;
    $this->cpf = $numeric_cpf;
  }

  function lista_simples($str_nome = FALSE, $numeric_cpf = FALSE,
    $inicio_limite = FALSE, $qtd_registros = FALSE, $str_orderBy = FALSE,
    $int_ref_cod_sistema = FALSE)
  {
    $whereAnd = '';
    $where    = '';

    if (is_string($str_nome) && $str_nome != '') {
      $str_nome = str_replace(' ', '%', $str_nome);
      $where   .= "{$whereAnd} nome ILIKE '%{$str_nome}%' ";
      $whereAnd = ' AND ';
    }

    if (is_string($numeric_cpf)) {
      $where .= "{$whereAnd} cpf ILIKE '%{$numeric_cpf}%' ";
    }

    if (is_numeric($int_ref_cod_sistema)) {
      $where .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR cpf is not null  )";
    }

    if ($inicio_limite !== FALSE && $qtd_registros) {
      $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
    }

    $orderBy = ' ORDER BY ';

    if ($str_orderBy) {
      $orderBy .= $str_orderBy . ' ';
    }
    else {
      $orderBy .= 'nome ';
    }

    if ($where) {
      $where = 'WHERE ' . $where;
    }

    $db = new clsBanco($this->banco);

    $total = $db->UnicoCampo('SELECT COUNT(0) FROM cadastro.fisica ' . $where);

    $db->Consulta(sprintf(
      'SELECT idpes, nome, cpf FROM cadastro.v_pessoa_fisica %s %s %s ', $where, $orderBy, $limite
    ));

    $resultado = array();

    while ($db->ProximoRegistro()) {
      $tupla          = $db->Tupla();
      $tupla['nome']  = transforma_minusculo($tupla['nome']);
      $tupla['total'] = $total;
      $resultado[]    = $tupla;
    }

    if (count($resultado) > 0) {
      return $resultado;
    }

    return FALSE;
  }

  function lista($str_nome = FALSE, $numeric_cpf = FALSE, $inicio_limite = FALSE,
    $qtd_registros = FALSE, $str_orderBy = FALSE, $int_ref_cod_sistema = FALSE,
    $int_idpes = FALSE
  ) {
    $whereAnd = '';
    $where    = '';

    if (is_string($str_nome) && $str_nome != '') {
      $str_nome = addslashes($str_nome);
      $str_nome = str_replace(' ', '%', $str_nome);

      $where   .= "{$whereAnd} nome ILIKE E'%{$str_nome}%' ";
      $whereAnd = ' AND ';
    }

    if (is_string($numeric_cpf)) {
      $numeric_cpf = addslashes($numeric_cpf);

      $where   .= "{$whereAnd} cpf ILIKE E'%{$numeric_cpf}%' ";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_sistema)) {
      $where   .= "{$whereAnd} (ref_cod_sistema = '{$int_ref_cod_sistema}' OR cpf is not null  )";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_idpes)) {
      $where   .= "{$whereAnd} idpes = '$int_idpes'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($this->tipo_endereco)) {
      if ($this->tipo_endereco == 1) {
        // Interno
        $where   .= "{$whereAnd} idpes IN (SELECT idpes FROM cadastro.endereco_pessoa)";
        $whereAnd = ' AND ';
      }
      elseif ($this->tipo_endereco == 2) {
        // Externo
        $where   .= "{$whereAnd} idpes IN (SELECT idpes FROM cadastro.endereco_externo)";
        $whereAnd = ' AND ';
      }
    }

    if ($inicio_limite !== FALSE && $qtd_registros) {
      $limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
    }

    $orderBy = ' ORDER BY ';

    if ($str_orderBy) {
      $orderBy .= $str_orderBy . ' ';
    }
    else {
      $orderBy .= 'nome ';
    }

    $db  = new clsBanco();
    $dba = new clsBanco();

    if ($where) {
      $where = "WHERE ".$where;
    }

    if (! $where) {
      $total = $db->CampoUnico('SELECT COUNT(0) FROM cadastro.fisica ' . $where);
    }
    else {
      $total = $db->CampoUnico('SELECT COUNT(0) FROM cadastro.v_pessoa_fisica ' . $where);
    }

    $db->Consulta(sprintf(
      'SELECT idpes, nome, url, \'F\' AS tipo, email, cpf FROM cadastro.v_pessoa_fisica %s %s %s',
      $where, $orderBy, $limite
    ));

    $resultado = array();

    while ($db->ProximoRegistro())
    {
      $tupla          = $db->Tupla();
      $tupla['nome']  = transforma_minusculo($tupla['nome']);
      $tupla['total'] = $total;

      $dba->Consulta(sprintf(
        "SELECT
          ddd_1, fone_1, ddd_2, fone_2, ddd_mov, fone_mov, ddd_fax, fone_fax
        FROM
          cadastro.v_fone_pessoa
        WHERE idpes = %d", $tupla['idpes']
      ));

      if ($dba->ProximoRegistro()) {
        $tupla_fone = $dba->Tupla();
      }
      else {
        $tupla_fone = '';
      }

      $tupla['ddd_1']    = $tupla_fone['ddd_1'];
      $tupla['fone_1']   = $tupla_fone['fone_1'];
      $tupla['ddd_2']    = $tupla_fone['ddd_2'];
      $tupla['fone_2']   = $tupla_fone['fone_2'];
      $tupla['ddd_mov']  = $tupla_fone['ddd_mov'];
      $tupla['fone_mov'] = $tupla_fone['fone_mov'];
      $tupla['ddd_fax']  = $tupla_fone['ddd_fax'];
      $tupla['fone_fax'] = $tupla_fone['fone_fax'];

      $resultado[] = $tupla;
    }

    if (count($resultado) > 0) {
      return $resultado;
    }

    return FALSE;
  }

  function detalhe()
  {
    if ($this->idpes) {
      $tupla = parent::detalhe();

      $objFisica      = new clsFisica($this->idpes);
      $detalhe_fisica = $objFisica->detalhe();

      if ($detalhe_fisica) {
        $this->data_nasc                = $detalhe_fisica['data_nasc'];
        $this->sexo                     = $detalhe_fisica['sexo'];
        $this->idpes_mae                = $detalhe_fisica['idpes_mae'];
        $this->idpes_pai                = $detalhe_fisica['idpes_pai'];
        $this->idpes_responsavel        = $detalhe_fisica['idpes_responsavel'];
        $this->idesco                   = $detalhe_fisica['idesco'];
        $this->ideciv                   = $detalhe_fisica['ideciv'];
        $this->idpes_con                = $detalhe_fisica['idpes_con'];
        $this->data_uniao               = $detalhe_fisica['data_uniao'];
        $this->data_obito               = $detalhe_fisica['data_obito'];
        $this->nacionalidade            = $detalhe_fisica['nacionalidade'];
        $this->idpais_estrangeiro       = $detalhe_fisica['idpais_estrangeiro'];
        $this->data_chagada_brasil      = $detalhe_fisica['data_chagada_brasil'];
        $this->idmun_nascimento         = $detalhe_fisica['idmun_nascimento'];
        $this->ultima_empresa           = $detalhe_fisica['ultima_empresa'];
        $this->idocup                   = $detalhe_fisica['idocup'];
        $this->nome_mae                 = $detalhe_fisica['nome_mae'];
        $this->nome_pai                 = $detalhe_fisica['nome_pai'];
        $this->nome_conjuge             = $detalhe_fisica['nome_conjuge'];
        $this->nome_responsavel         = $detalhe_fisica['nome_responsavel'];
        $this->justificativa_provisorio = $detalhe_fisica['justificativa_provisorio'];
        $this->cpf                      = $detalhe_fisica['cpf'];
        $this->ref_cod_religiao         = $detalhe_fisica['ref_cod_religiao'];

        $tupla['idpes'] = $this->idpes;
        $tupla[]        = & $tupla['idpes'];

        $tupla['cpf'] = $this->cpf;
        $tupla[]      = & $tupla['cpf'];

        $tupla['ref_cod_religiao'] = $this->ref_cod_religiao;
        $tupla[]                   = & $tupla['ref_cod_religiao'];

        $tupla['data_nasc'] = $this->data_nasc;
        $tupla[]            = & $tupla['data_nasc'];

        $tupla['sexo'] = $this->sexo;
        $tupla[]       = & $tupla['sexo'];

        $tupla['idpes_mae'] = $this->idpes_mae;
        $tupla[]            = & $tupla['idpes_mae'];

        $tupla['idpes_pai'] = $this->idpes_pai;
        $tupla[]            = & $tupla['idpes_pai'];

        $tupla['idpes_responsavel'] = $this->idpes_responsavel;
        $tupla[]                    = & $tupla['idpes_responsavel'];

        $tupla['idesco'] = $this->idesco;
        $tupla[]         = & $tupla['idesco'];

        $tupla['ideciv'] = $this->ideciv;
        $tupla[]         = & $tupla['ideciv'];

        $tupla['idpes_con'] = $this->idpes_con;
        $tupla[]            = & $tupla['idpes_con'];

        $tupla['data_uniao'] = $this->data_uniao;
        $tupla[]             = & $tupla['data_uniao'];

        $tupla['data_obito'] = $this->data_obito;
        $tupla[]             = & $tupla['data_obito'];

        $tupla['nacionalidade'] = $this->nacionalidade;
        $tupla[]                = & $tupla['nacionalidade'];

        $tupla['idpais_estrangeiro'] = $this->idpais_estrangeiro;
        $tupla[]                     = & $tupla['idpais_estrangeiro'];

        $tupla['data_chagada_brasil'] = $this->data_chagada_brasil;
        $tupla[]                      = & $tupla['data_chagada_brasil'];

        $tupla['idmun_nascimento'] = $this->idmun_nascimento;
        $tupla[]                   = & $tupla['idmun_nascimento'];

        $tupla['ultima_empresa'] = $this->ultima_empresa;
        $tupla[]                 = & $tupla['ultima_empresa'];

        $tupla['idocup'] = $this->idocup;
        $tupla[]         = & $tupla['idocup'];

        $tupla['nome_mae'] = $this->nome_mae;
        $tupla[]           = & $tupla['nome_mae'];

        $tupla['nome_pai'] = $this->nome_pai;
        $tupla[]           = & $tupla['nome_pai'];

        $tupla['nome_conjuge'] = $this->nome_conjuge;
        $tupla[]               = & $tupla['nome_conjuge'];

        $tupla['nome_responsavel'] = $this->nome_responsavel;
        $tupla[]                   = & $tupla['nome_responsavel'];

        $tupla['justificativa_provisorio'] = $this->justificativa_provisorio;
        $tupla[]                           = & $tupla['justificativa_provisorio'];

        return $tupla;
      }
    }
    elseif ($this->cpf) {
      $tupla = parent::detalhe();

      $objFisica = new clsFisica();
      $lista = $objFisica->lista(FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
        FALSE, FALSE, FALSE, FALSE, $this->cpf);

      $this->idpes = $lista[0]['idpes'];

      if ($this->idpes) {
        $objFisica = new clsFisica($this->idpes);
        $detalhe_fisica = $objFisica->detalhe();

        if ($detalhe_fisica) {
          $this->data_nasc                = $detalhe_fisica['data_nasc'];
          $this->sexo                     = $detalhe_fisica['sexo'];
          $this->idpes_mae                = $detalhe_fisica['idpes_mae'];
          $this->idpes_pai                = $detalhe_fisica['idpes_pai'];
          $this->idpes_responsavel        = $detalhe_fisica['idpes_responsavel'];
          $this->idesco                   = $detalhe_fisica['idesco'];
          $this->ideciv                   = $detalhe_fisica['ideciv'];
          $this->idpes_con                = $detalhe_fisica['idpes_con'];
          $this->data_uniao               = $detalhe_fisica['data_uniao'];
          $this->data_obito               = $detalhe_fisica['data_obito'];
          $this->nacionalidade            = $detalhe_fisica['nacionalidade'];
          $this->idpais_estrangeiro       = $detalhe_fisica['idpais_estrangeiro'];
          $this->data_chagada_brasil      = $detalhe_fisica['data_chagada_brasil'];
          $this->idmun_nascimento         = $detalhe_fisica['idmun_nascimento'];
          $this->ultima_empresa           = $detalhe_fisica['ultima_empresa'];
          $this->idocup                   = $detalhe_fisica['idocup'];
          $this->nome_mae                 = $detalhe_fisica['nome_mae'];
          $this->nome_pai                 = $detalhe_fisica['nome_pai'];
          $this->nome_conjuge             = $detalhe_fisica['nome_conjuge'];
          $this->nome_responsavel         = $detalhe_fisica['nome_responsavel'];
          $this->justificativa_provisorio = $detalhe_fisica['justificativa_provisorio'];
          $this->cpf                      = $detalhe_fisica['cpf'];

          $tupla['idpes'] = $this->idpes;
          $tupla[]        = & $tupla['idpes'];

          $tupla['cpf'] = $this->cpf;
          $tupla[]      = & $tupla['cpf'];

          $tupla['data_nasc'] = $this->data_nasc;
          $tupla[]            = & $tupla['data_nasc'];

          $tupla['sexo'] = $this->sexo;
          $tupla[]       = & $tupla['sexo'];

          $tupla['idpes_mae'] = $this->idpes_mae;
          $tupla[]            = & $tupla['idpes_mae'];

          $tupla['idpes_pai'] = $this->idpes_pai;
          $tupla[]            = & $tupla['idpes_pai'];

          $tupla['idpes_responsavel'] = $this->idpes_responsavel;
          $tupla[]                    = & $tupla['idpes_responsavel'];

          $tupla['idesco'] = $this->idesco;
          $tupla[]         = & $tupla['idesco'];

          $tupla['ideciv'] = $this->ideciv;
          $tupla[]         = & $tupla['ideciv'];

          $tupla['idpes_con'] = $this->idpes_con;
          $tupla[]            = & $tupla['idpes_con'];

          $tupla['data_uniao'] = $this->data_uniao;
          $tupla[]             = & $tupla['data_uniao'];

          $tupla['data_obito'] = $this->data_obito;
          $tupla[]             = & $tupla['data_obito'];

          $tupla['nacionalidade'] = $this->nacionalidade;
          $tupla[]                = & $tupla['nacionalidade'];

          $tupla['idpais_estrangeiro'] = $this->idpais_estrangeiro;
          $tupla[]                     = & $tupla['idpais_estrangeiro'];

          $tupla['data_chagada_brasil'] = $this->data_chagada_brasil;
          $tupla[]                      = & $tupla['data_chagada_brasil'];

          $tupla['idmun_nascimento'] = $this->idmun_nascimento;
          $tupla[]                   = & $tupla['idmun_nascimento'];

          $tupla['ultima_empresa'] = $this->ultima_empresa;
          $tupla[]                 = & $tupla['ultima_empresa'];

          $tupla['idocup'] = $this->idocup;
          $tupla[]         = & $tupla['idocup'];

          $tupla['nome_mae'] = $this->nome_mae;
          $tupla[]           = & $tupla['nome_mae'];

          $tupla['nome_pai'] = $this->nome_pai;
          $tupla[]           = & $tupla['nome_pai'];

          $tupla['nome_conjuge'] = $this->nome_conjuge;
          $tupla[]               = & $tupla['nome_conjuge'];

          $tupla['nome_responsavel'] = $this->nome_responsavel;
          $tupla[]                   = & $tupla['nome_responsavel'];

          $tupla['justificativa_provisorio'] = $this->justificativa_provisorio;
          $tupla[]                           = & $tupla['justificativa_provisorio'];

          return $tupla;
        }
      }
    }

    return FALSE;
  }

  function queryRapida($int_idpes)
  {
    $this->idpes = $int_idpes;
    $this->detalhe();

    $resultado = array();
    $pos       = 0;

    for ($i = 1; $i< func_num_args(); $i++ ) {
      $campo = func_get_arg($i);

      $resultado[$pos]   = $this->$campo ? $this->$campo : '';
      $resultado[$campo] = & $resultado[$pos];

      $pos++;
    }

    if (count($resultado) > 0) {
      return $resultado;
    }

    return FALSE;
  }

  function queryRapidaCPF($int_cpf)
  {
    $this->cpf = $int_cpf + 0;
    $this->detalhe();

    $resultado = array();
    $pos       = 0;

    for ($i = 1; $i< func_num_args(); $i++ ) {
      $campo = func_get_arg($i);
      $resultado[$pos]   = $this->$campo ? $this->$campo : '';
      $resultado[$campo] = & $resultado[$pos];
      $pos++;
    }

    if (count($resultado) > 0) {
      return $resultado;
    }

    return FALSE;
  }

  function excluir()
  {
    if ($this->idpes) {
      $db  = new clsBanco();
      $obj = new clsFuncionario($this->idpes);

      if (! $obj->detalhe()) {
        $db->Consulta('DELETE FROM cadastro.fone_pessoa WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.fisica WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.documento WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.endereco_pessoa WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.endereco_externo WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.documento WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.documento WHERE idpes = ' . $this->idpes);
        $db->Consulta('DELETE FROM cadastro.pessoa WHERE idpes = ' . $this->idpes);
      }
    }
  }

  function setTipoEndereco($endereco)
  {
    if (is_numeric($endereco)) {
      $this->tipo_endereco = $endereco;
    }
  }
}