<?php

/*
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
 */

/**
 * clsBanco class.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe disponível desde a versão 1.0.0
 * @version  $Id$
 */

if (!class_exists('clsBancoSql_')) {
  require_once 'include/clsBancoPgSql.inc.php';
}

class clsBanco extends clsBancoSQL_ {

  protected $strHost       = 'localhost';    // Nome ou endereço IP do servidor do banco de dados
  protected $strBanco      = 'ieducardb';    // Nome do banco de dados
  protected $strUsuario    = 'ieducaruser';  // Usuário devidamente autorizado a acessar o banco
  protected $strSenha      = 'ieducar';      // Senha do usuário do banco
  protected $strPort       = NULL;           // Porta do servidor de banco de dados

  public $bLink_ID         = 0;              // Identificador da conexão
  public $bConsulta_ID     = 0;              // Identificador do resultado da consulta
  public $arrayStrRegistro = array();        // Tupla resultante de uma consulta
  public $iLinha           = 0;              // Ponteiro interno para a tupla atual da consulta

  public $bErro_no         = 0;              // Se ocorreu erro na consulta, retorna FALSE
  public $strErro          = "";             // Frase de descrição do erro retornado
  public $bDepurar         = FALSE;          // Ativa ou desativa funções de depuração

  public $bAuto_Limpa      = FALSE;          // '1' para limpar o resultado assim que chegar ao último registro

  public $strStringSQL     = '';

  var $strType         = '';
  var $arrayStrFields  = array();
  var $arrayStrFrom    = array();
  var $arrayStrWhere   = array();
  var $arrayStrOrderBy = array();
  var $arrayStrGroupBy = array();
  var $iLimitInicio;
  var $iLimitQtd;
  var $arrayStrArquivo = '';



  /**
   * Construtor (PHP 4).
   */
  public function clsBanco($strDataBase = FALSE) {}



  /**
   * Retorna a quantidade de registros de uma tabela baseado no objeto que a
   * abstrai. Este deve ter um atributo público Object->_tabela.
   *
   * @param   mixed   Objeto que abstrai a tabela
   * @param   string  Nome da coluna para cálculo COUNT()
   * @return  int     Quantidade de registros da tabela
   */
  public function doCountFromObj($obj, $column = '*') {
    if ($obj->_tabela == NULL) {
      return FALSE;
    }

    $sql = sprintf('SELECT COUNT(%s) FROM %s', $column, $obj->_tabela);
    $this->Consulta($sql);

    return (int)$this->UnicoCampo($sql);
  }

}