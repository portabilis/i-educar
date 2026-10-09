<?php

beforeEach(function () {
    $this->_entity = new Educacenso_Model_Aluno;
});

test('entity validators', function () {
    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['aluno']);
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['alunoInep']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['nomeInep']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['fonte']);
});
