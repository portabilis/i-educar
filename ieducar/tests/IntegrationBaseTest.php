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

require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'CoreExt/DataMapper.php';

/**
 * IntegrationBaseTest abstract class.
 *
 * Cria um ambiente de testes de integração com um banco de dados sqlite em
 * memória. Útil para os testes dos novos componentes de domínio.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   IntegrationTests
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class IntegrationBaseTest extends PHPUnit_Extensions_Database_TestCase
{
  /**
   * Objeto de conexão com o banco de dados que será utilizado tanto pelas
   * classes da aplicação quanto pelos testes de integração.
   *
   * @var CustomPdo
   */
  protected $db = NULL;

  /**
   * Construtor.
   */
  public function __construct()
  {
    $this->db = new CustomPdo('sqlite::memory:');
  }

  /**
   * Usa o setUp() para configurar a todas as instâncias de CoreExt_DataMapper
   * que usem o adapter de banco dessa classe.
   */
  protected function setUp()
  {
    parent::setUp();
    CoreExt_DataMapper::setDefaultDbAdapter($this->getDbAdapter());
  }

  /**
   * Getter.
   * @return CustomPdo
   */
  protected function getDbAdapter()
  {
    return $this->db;
  }

  /**
   * Retorna a conexão usada pelos testes de integração do DbUnit. Note que
   * a conexão é criada com o objeto PDO encapsulado em CustomPdo.
   *
   * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
   */
  protected function getConnection()
  {
    return $this->createDefaultDBConnection($this->db->getPdo(), 'testdb');
  }

  /**
   * Retorna o caminho absoluto para um arquivo fixture em unit/CoreExt/.
   *
   * @param string $filename
   * @return string
   */
  public function getFixture($filename)
  {
    $path = dirname(__FILE__);
    return $path . '/unit/CoreExt/_fixtures/' . $filename;
  }

/**
   * Retorna o caminho absoluto para um arquivo fixture dentro do diretório
   * _tests de um módulo.
   *
   * @param  string $filename
   * @return string
   */
  public function getFixtureForModule($filename, $module)
  {
    $path = PROJECT_ROOT . DS . 'modules' . DS . $module . DS . '_tests';
    return $path . DS . $filename;
  }
}