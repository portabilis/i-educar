<?php

beforeEach(function () {
    $this->_entity = new Educacenso_Model_CursoSuperior;
});

test('instancia de area retorna o valor de nome em contexto de impressao', function () {
    $this->_entity->nome = 'Curso superior';
    $this->assertEquals('Curso superior', $this->_entity->__toString());
});

test('entity validators', function () {
    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['curso']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['nome']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['classe']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['user']);
});
