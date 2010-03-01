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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Session
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Session/Storage/Default.php';

/**
 * CoreExt_Session_Storage_DefaultTest class.
 *
 * @backupGlobals disabled
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Session
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @todo        Verificar se o problema de envio de headers ocorre em versões
 *   mais recentes do PHPUnit (> 3.4.0), ver ticket PHPUnit #946 relacionado:
 *   {@link http://www.phpunit.de/ticket/946 Tap Output makes Session Tests fail}
 * @version     @@package_version@@
 */
class CoreExt_Session_Storage_DefaultTest extends UnitBaseTest
{
  protected $_storage = NULL;
  protected static $_oldSessionId = NULL;
  protected static $_newSessionId = NULL;

  public function __construct()
  {
    static $iniSet = FALSE;
    $this->_storage = new CoreExt_Session_Storage_Default();

    // Workaround para testar o regenerate de session, já que em um test case
    // a função iria lançar um erro de "headers already sent", que ocorre no
    // arquivo PHPUnit/Util/Printer.php:173.
    // Depende da configuração session.use_cookies definida em bootstrap.php
    if (FALSE == $iniSet) {
      self::$_oldSessionId = $this->_storage->getSessionId();
      $this->_storage->regenerate(TRUE);
      self::$_newSessionId = $this->_storage->getSessionId();

      $iniSet = TRUE;
    }
  }

  public function testInstanciaESubclasseDeCountable()
  {
    $this->assertType('Countable', $this->_storage);
  }

  public function testEscreveDadosNaSession()
  {
    $this->_storage->write('foo', 'bar');
    $this->_storage->write('foo/1', 'bar/1');
    $this->_storage->write('foo/2', 'bar/2');
    $this->_storage->write('foo/3', 'bar/3');

    // Verifica usando o array global $_SESSION
    $this->assertEquals('bar', $_SESSION['foo']);
    $this->assertEquals('bar/1', $_SESSION['foo/1']);
    $this->assertEquals('bar/2', $_SESSION['foo/2']);
  }

  /**
   * @depends testEscreveDadosNaSession
   */
  public function testLerDadosArmazenadosNaSession()
  {
    $this->assertEquals('bar', $this->_storage->read('foo'));
    $this->assertEquals('bar/1', $this->_storage->read('foo/1'));
    $this->assertEquals('bar/2', $this->_storage->read('foo/2'));
    $this->assertEquals('bar/3', $this->_storage->read('foo/3'));
  }

  /**
   * @depends testEscreveDadosNaSession
   */
  public function testCountable()
  {
    $this->assertEquals(4, count($this->_storage));
  }

  public function testRemoveIndiceDaSession()
  {
    $this->_storage->remove('bar/3');
    $this->assertNull($this->_storage->read('bar/3'));
  }

  public function testIndiceNaoExistenteNaSessionRetornaNull()
  {
    $this->assertNull($this->_storage->read('null'));
  }

  public function testRegeneraIdDaSession()
  {
    $this->assertNotEquals(self::$_oldSessionId, self::$_newSessionId);
  }
}