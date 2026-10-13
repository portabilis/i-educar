<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->_session = new CoreExt_Session(['session_auto_start' => false]);
});

test('opcao de configuracao nao existente lanca excecao', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->_session->setOptions(['foo' => 'bar']);
});

test('instancia tem session instancia storage default por padrao', function () {
    $this->assertInstanceOf('CoreExt_Session_Storage_Default', $this->_session->getSessionStorage());
});

test('instancia e subclasse de array access', function () {
    $this->assertInstanceOf('ArrayAccess', $this->_session);
});

test('instancia e subclasse de countable', function () {
    $this->assertInstanceOf('Countable', $this->_session);
});

test('instancia e subclasse de iterator', function () {
    $this->assertInstanceOf('Iterator', $this->_session);
});

test('array access', function () {
    $this->assertNull($this->_session['foo'], '[foo] is not null');

    $this->_session['bar'] = 'foo';
    $this->assertEquals('foo', $this->_session['bar'], '[bar] != foo');

    // $this->_session->offsetUnset('bar');
    unset($this->_session['bar']);
    $this->assertNull($this->_session['bar'], '[bar] not unset');
});

test('countable', function () {
    $this->assertEquals(0, count($this->_session));

    $this->_session['foo'] = 'bar';
    $this->assertEquals(1, count($this->_session));
})->depends('array access');

test('overload', function () {
    $this->assertNull($this->_session->foo, '->foo is not null');

    $this->_session->bar = 'foo';
    $this->assertEquals('foo', $this->_session->bar, '->bar != foo');

    unset($this->_session->bar);
    $this->assertNull($this->_session->bar, '->bar not unset');
});

/**
 * Como CoreExt_Session_Abstract::offsetSet() converte a chave em string,
 * podemos acessá-los de forma dinâmica na forma $session->$key em um
 * iterador foreach, por exemplo.
 */
test('iterator', function () {
    $expected = [
        '_1' => 'bar1', '_2' => 'bar2', '_3' => 'bar3',
    ];

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
});
