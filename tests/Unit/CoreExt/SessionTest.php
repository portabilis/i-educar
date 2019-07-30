<?php

use Tests\TestCase;

require_once 'CoreExt/Session.php';

class CoreExt_SessionTest extends TestCase
{
  protected $_session = NULL;

  protected function setUp(): void
  {
    parent::setUp();

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
    $this->assertInstanceOf('CoreExt_Session_Storage_Default', $this->_session->getSessionStorage());
  }

  public function testInstanciaESubclasseDeArrayAccess()
  {
    $this->assertInstanceOf('ArrayAccess', $this->_session);
  }

  public function testInstanciaESubclasseDeCountable()
  {
    $this->assertInstanceOf('Countable', $this->_session);
  }

  public function testInstanciaESubclasseDeIterator()
  {
    $this->assertInstanceOf('Iterator', $this->_session);
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
      '_1' => 'bar1', '_2' => 'bar2', '_3' => 'bar3'
    );

    $this->_session['_1'] = 'bar1';
    $this->_session['_2'] = 'bar2';

    foreach ($this->_session as $key => $val) {
      $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
      $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
    }

    $this->_session['_3'] = 'bar3';
    foreach ($this->_session as $key => $val) {
      $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
      $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
    }
  }
}
