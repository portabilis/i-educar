<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->_entity = new Avaliacao_Model_FaltaAluno;
});

test('entity validators', function () {
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['matricula']);
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['tipoFalta']);
});
