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
 * @package     CoreExt_Locale
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Session.php';

/**
 * CoreExt_SessionTest class.
 *
 * Testa o componente CoreExt_Session, desabilitando o auto start (para evitar
 * erros "headers sent") e confiando na classe CoreExt_Session_Storage_Default.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Session
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_SessionTest extends UnitBaseTest
{
  protected $_session = NULL;

  protected function setUp()
  {
    $_SESSION = array();
    $this->_session = new CoreExt_Session(array('session_auto_start' => FALSE));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_session->setOptions(array('foo' => 'bar'));
  }

  public function testInstanciaTemSessionInstanciaStorageDefaultPorPadrao()
  {
    $this->assertType('CoreExt_Session_Storage_Default', $this->_session->getSessionStorage());
  }

  public function testInstanciaESubclasseDeArrayAccess()
  {
    $this->assertType('ArrayAccess', $this->_session);
  }

  public function testInstanciaESubclasseDeCountable()
  {
    $this->assertType('Countable', $this->_session);
  }

  public function testInstanciaESubclasseDeIterator()
  {
    $this->assertType('Iterator', $this->_session);
  }

  /**
   * @backupGlobals disabled
   */
  public function testArrayAccess()
  {
    $this->assertNull($this->_session['foo'], '[foo] is not null');

    $this->_session['bar'] = 'foo';
    $this->assertEquals('foo', $this->_session['bar'], '[bar] != foo');

    //$this->_session->offsetUnset('bar');
    unset($this->_session['bar']);
    $this->assertNull($this->_session['bar'], '[bar] not unset');
  }

  /**
   * @backupGlobals disabled
   * @depends testArrayAccess
   */
  public function testCountable()
  {
    $this->assertEquals(0, count($this->_session));

    $this->_session['foo'] = 'bar';
    $this->assertEquals(1, count($this->_session));
  }

  /**
   * @backupGlobals enabled
   */
  public function testOverload()
  {
    $this->assertNull($this->_session->foo, '->foo is not null');

    $this->_session->bar = 'foo';
    $this->assertEquals('foo', $this->_session->bar, '->bar != foo');

    unset($this->_session->bar);
    $this->assertNull($this->_session->bar, '->bar not unset');
  }

  /**
   * Como CoreExt_Session_Abstract::offsetSet() converte a chave em string,
   * podemos acessá-los de forma dinâmica na forma $session->$key em um
   * iterador foreach, por exemplo.
   */
  public function testIterator()
  {
    $expected = array(
      1 => 'bar1', 2 => 'bar2', 3 => 'bar3'
    );

    $this->_session[1] = 'bar1';
    $this->_session[2] = 'bar2';

    foreach ($this->_session as $key => $val) {
      $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
      $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
    }

    $this->_session[3] = 'bar3';
    foreach ($this->_session as $key => $val) {
      $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
      $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
    }
  }
}