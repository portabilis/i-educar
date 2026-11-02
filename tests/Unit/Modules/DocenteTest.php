<?php

beforeEach(function () {
    $this->_entity = new Educacenso_Model_Docente;
});

test('entity validators', function () {
    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['docente']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['docenteInep']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['nomeInep']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['fonte']);
});
