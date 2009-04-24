<?php
/**
 *
 * @author  Prefeitura Municipal de Itajaí
 * @version SVN: $Id$
 *
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006 PMI - Prefeitura Municipal de Itajaí
 *            ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

if (!class_exists('clsBancoSql_')) {
  require_once 'include/clsBancoPgSql.inc.php';
}

class clsBanco extends clsBancoSQL_ {

  public $strHost          = "localhost";    // Nome ou endereço IP do servidor do banco de dados
  public $strBanco         = "ieducardb";    // Nome do banco de dados
  public $strUsuario       = "ieducaruser";  // Usuário devidamente autorizado a acessar o banco
  public $strSenha         = "ieducar";      // Senha do usuário do banco

  public $bLink_ID         = 0;              // Identificador da conexão
  public $bConsulta_ID     = 0;              // Identificador do resultado da consulta
  public $arrayStrRegistro = array();        // Tupla resultante de uma consulta
  public $iLinha           = 0;              // Ponteiro interno para a tupla atual da consulta

  public $bErro_no         = 0;              // Se ocorreu erro na consulta, retorna FALSE
  public $strErro          = "";             // Frase de descrição do erro retornado
  public $bDepurar         = FALSE;          // Ativa ou desativa funções de depuração

  public $bAuto_Limpa      = FALSE;          // '1' para limpar o resultado assim que chegar ao último registro

  public $strStringSQL     = "";

  var $strType         = "";
  var $arrayStrFields  = array();
  var $arrayStrFrom    = array();
  var $arrayStrWhere   = array();
  var $arrayStrOrderBy = array();
  var $arrayStrGroupBy = array();
  var $iLimitInicio;
  var $iLimitQtd;
  var $arrayStrArquivo = "";



  /**
   * Construtor (PHP 4).
   */
  public function clsBanco($strDataBase = FALSE) {}



  /**
   * Retorna a quantidade de registros de uma tabela baseado no objeto que a
   * abstrai. Este deve ter um atributo público Object->_tabela.
   *
   * @param  object Objeto que abstrai a tabela
   * @param  string Nome da coluna para cálculo COUNT()
   * @return int    Quantidade de registros da tabela
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