<?php

use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->_storage = new CoreExt_Session_Storage_Default;
});

test('instancia e subclasse de countable', function () {
    $this->assertInstanceOf('Countable', $this->_storage);
});

test('escreve dados na session', function () {
    $this->_storage->write('foo', 'bar');
    $this->_storage->write('foo/1', 'bar/1');
    $this->_storage->write('foo/2', 'bar/2');
    $this->_storage->write('foo/3', 'bar/3');

    $this->assertEquals('bar', Session::get('foo'));
    $this->assertEquals('bar/1', Session::get('foo/1'));
    $this->assertEquals('bar/2', Session::get('foo/2'));
    $this->assertEquals('bar/3', Session::get('foo/3'));
});

test('ler dados armazenados na session', function () {
    $this->_storage->write('foo', 'bar');
    $this->_storage->write('foo/1', 'bar/1');
    $this->_storage->write('foo/2', 'bar/2');
    $this->_storage->write('foo/3', 'bar/3');

    $this->assertEquals('bar', $this->_storage->read('foo'));
    $this->assertEquals('bar/1', $this->_storage->read('foo/1'));
    $this->assertEquals('bar/2', $this->_storage->read('foo/2'));
    $this->assertEquals('bar/3', $this->_storage->read('foo/3'));
})->depends('escreve dados na session');

test('remove indice da session', function () {
    $this->_storage->remove('bar/3');
    $this->assertNull($this->_storage->read('bar/3'));
});

test('indice nao existente na session retorna null', function () {
    $this->assertNull($this->_storage->read('null'));
});
