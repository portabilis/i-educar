<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->_entity = new Avaliacao_Model_NotaComponente;
});

test('entity validators', function () {
    $validators = $this->_entity->getValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['nota']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['notaArredondada']);
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
});
