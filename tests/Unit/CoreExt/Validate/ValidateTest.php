<?php

beforeEach(function () {
    $this->_validator = new CoreExt_ValidateStub;
});

test('opcao de configuracao nao existente lanca excecao', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->_validator->setOptions(['invalidOption' => true]);
});

test('configura opcao do validator', function () {
    $this->_validator->setOptions(['required' => false]);

    $options = $this->_validator->getOptions();
    $this->assertFalse($options['required']);

    $this->assertFalse($this->_validator->getOption('required'));
});

test('valor string somente espaco requerido', function () {
    $this->expectException(Exception::class);
    // Um espaço ASCII
    $this->assertTrue($this->_validator->isValid(' '));
});

test('valor nulo lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->assertTrue($this->_validator->isValid(null));
});

test('valor array vazio lanca excecao', function () {
    $this->expectException(Exception::class);
    $this->assertTrue($this->_validator->isValid([]));
});

test('valor nao obrigatorio com configuracao na instanciacao', function () {
    $validator = new CoreExt_Validate_String(['required' => false]);
    $this->assertTrue($validator->isValid(''));
});

test('valor nao obrigatorio com configuracao via metodo', function () {
    $this->_validator->setOptions(['required' => false]);
    $this->assertTrue($this->_validator->isValid(''));
});
