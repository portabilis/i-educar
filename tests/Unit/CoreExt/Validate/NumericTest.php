<?php

beforeEach(function () {
    $this->_validator = new CoreExt_Validate_Numeric;
});

test('valor string vazia lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->isValid('');
});

test('valor string espaco lanca excecao', function () {
    $this->expectException(Exception::class);
    // São três espaço ascii 20
    $this->_validator->isValid('   ');
});

test('valor null lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->isValid(null);
});

test('valor nao numerico lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->isValid('zero');
});

test('valor null nao lanca excecao se required for false', function () {
    $this->_validator->setOptions(['required' => false]);
    $this->assertTrue($this->_validator->isValid(null));
});

test('valor numerico sem configurar o validador', function () {
    $this->assertTrue($this->_validator->isValid(0));
    $this->assertTrue($this->_validator->isValid(1.5));
    $this->assertTrue($this->_validator->isValid(-1.5));
});

test('valores dentro de um range configurado no validador', function () {
    $this->_validator->setOptions(['min' => -50, 'max' => 50]);
    $this->assertTrue($this->_validator->isValid(50));
    $this->assertTrue($this->_validator->isValid(50));
    $this->assertTrue($this->_validator->isValid(50.00));
    $this->assertTrue($this->_validator->isValid(-50.00));
    $this->assertTrue($this->_validator->isValid(49.9999));
    $this->assertTrue($this->_validator->isValid(-49.9999));
});

test('valor menor que o permitido lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->setOptions(['min' => 0]);
    $this->_validator->isValid(-1);
});

test('valor ponto flutuante menor que o permitido lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->setOptions(['min' => 0]);
    $this->_validator->isValid(-1.5);
});

test('valor maior que o permitido lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->setOptions(['max' => 0]);
    $this->_validator->isValid(1);
});

test('valor ponto flutuante maior que o permitido lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->setOptions(['max' => 0]);
    $this->_validator->isValid(1.5);
});
