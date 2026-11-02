<?php

beforeEach(function () {
    $this->_validator = new CoreExt_Validate_String;
});

test('string somente espaco lanca excecao por ser obrigatorio', function () {
    $this->expectException(Exception::class);
    // São três espaços ascii 20.
    $this->assertTrue($this->_validator->isValid('   '));
});

test('string sem alterar configuracao basica', function () {
    $this->assertTrue($this->_validator->isValid('abc'));
});

test('string menor que o tamanho minimo lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->setOptions(['min' => 5]);
    $this->assertTrue($this->_validator->isValid('Foo'));
});

test('alfa string que o tamanho maximo lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->_validator->setOptions(['max' => 2]);
    $this->assertTrue($this->_validator->isValid('Foo'));
});
