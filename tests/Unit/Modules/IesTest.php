<?php

beforeEach(function () {
    $this->_entity = new Educacenso_Model_Ies;
});

test('entity validators', function () {
    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['ies']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['nome']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['dependenciaAdministrativa']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['tipoInstituicao']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['uf']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['user']);
});
