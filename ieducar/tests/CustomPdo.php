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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   IntegrationTests
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * CustomPdo class.
 *
 * Sobrescreve métodos específicos de clsBanco para permitir testes de
 * integração com o DbUnit, usando uma conexão Pdo por trás das cenas.
 *
 * Os métodos sobrescritos tem comportamento semelhante ao original, exceto
 * que, ao invés de usar uma conexão ext/pgsql, a ext/pdo_pgsql é utilizada.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   IntegrationTests
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Adicionar a verificação a extensão mbstring antes de fazer a
 *   conversão em _decodeUtf8 já que não é padrão no PHP (podendo ser
 *   desabilitada). Como é usada apenas para os testes, não interfere no uso
 *   da aplicação.
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
   * Retorna a instância PDO atual.
   * @return PDO
   */
  public function getPdo()
  {
    return $this->_pdo;
  }

  /**
   * Sobrescreve o método Consulta(), usando a mesma conexão PDO utilizada
   * pelo DbUnit.
   *
   * @param  string $sql
   * @param  bool   $reescrever
   * @return CustomPdo Provê interface fluída
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
   * Callback para ProximoRegistro(), converte uma referência para ISO-8859-1
   * caso esteja em UTF-8. Isso é feito pois o SQLite não permite mudar o
   * encoding de uma conexão ativa para ISO-8859-1.
   *
   * @link http://www.sqlite.org/pragma.html Veja PRAGMA encoding para mais
   *   informações sobre os encodings suportados pelo SQLite
   * @see  Docblock de CustomPdo para mais informações sobre mbstring
   */
  protected function _decodeUtf8(&$item, $key)
  {
    if ('UTF-8' == ($actual = mb_detect_encoding($item))) {
      $item = utf8_decode($item);
    }
  }
}