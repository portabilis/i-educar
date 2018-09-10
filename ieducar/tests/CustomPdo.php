<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   IntegrationTests
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

/**
 * CustomPdo class.
 *
 * Sobrescreve m�todos espec�ficos de clsBanco para permitir testes de
 * integra��o com o DbUnit, usando uma conex�o Pdo por tr�s das cenas.
 *
 * Os m�todos sobrescritos tem comportamento semelhante ao original, exceto
 * que, ao inv�s de usar uma conex�o ext/pgsql, a ext/pdo_pgsql � utilizada.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   IntegrationTests
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @todo      Adicionar a verifica��o a extens�o mbstring antes de fazer a
 *   convers�o em _decodeUtf8 j� que n�o � padr�o no PHP (podendo ser
 *   desabilitada). Como � usada apenas para os testes, n�o interfere no uso
 *   da aplica��o.
 * @version   @@package_version@@
 */
class CustomPdo extends clsBanco
{
  /**
   * Objeto PDO.
   * @var PDO
   */
  protected $_pdo = NULL;

  /**
   * Result set PDOStatment.
   * @var PDOStatment
   */
  protected $_rs  = NULL;

  /**
   * Array associativo de um registro de tabela de banco de dados.
   * @var array
   */
  protected $_row = array();

  /**
   * Modo de fetch utilizado por PDO::query().
   * @var int
   */
  protected $_fetchMode = PDO::FETCH_ASSOC;

  /**
   * Construtor.
   *
   * @param string $dsn
   * @param string $username
   * @param string $password
   * @param array  $driverOptions
   */
  public function __construct($dsn = '', $username = '', $password = '',
    array $driverOptions = array())
  {
    $this->_pdo = new PDO($dsn, $username, $password, $driverOptions);
  }

  /**
   * Retorna a inst�ncia PDO atual.
   * @return PDO
   */
  public function getPdo()
  {
    return $this->_pdo;
  }

  /**
   * Sobrescreve o m�todo Consulta(), usando a mesma conex�o PDO utilizada
   * pelo DbUnit.
   *
   * @param  string $sql
   * @param  bool   $reescrever
   * @return CustomPdo Prov� interface flu�da
   * @see    intranet/include/clsBancoSQL_#Consulta($consulta)
   */
  public function Consulta($sql, $reescrever = TRUE)
  {
    $this->_rs = $this->_pdo->query($sql);
    return $this;
  }

  /**
   * Move o ponteiro e retorna uma registro do result set PDOStatment.
   *
   * @return mixed|bool Retorna FALSE em caso de erro
   * @see intranet/include/clsBancoSQL_#ProximoRegistro()
   */
  public function ProximoRegistro()
  {
    $row = $this->_rs->fetch($this->_fetchMode);
    if (is_array($row)) {
      array_walk_recursive($row, array($this, '_decodeUtf8'));
    }
    return $this->_row = $row;
  }

  /**
   * Retorna o registro atual indicado por ProximoRegistro();
   *
   * @return mixed|bool
   * @see intranet/include/clsBancoSQL_#Tupla()
   */
  public function Tupla()
  {
    $row = $this->_row;
    return $row;
  }

  /**
   * Callback para ProximoRegistro(), converte uma refer�ncia para 
   * caso esteja em UTF-8. Isso � feito pois o SQLite n�o permite mudar o
   * encoding de uma conex�o ativa para .
   *
   * @link http://www.sqlite.org/pragma.html Veja PRAGMA encoding para mais
   *   informa��es sobre os encodings suportados pelo SQLite
   * @see  Docblock de CustomPdo para mais informa��es sobre mbstring
   */
  protected function _decodeUtf8(&$item, $key)
  {
    if ('UTF-8' == ($actual = mb_detect_encoding($item))) {
      $item = utf8_decode($item);
    }
  }
}