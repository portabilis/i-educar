<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->_entity = new Avaliacao_Model_FaltaAbstractStub;
});

test('entity validators', function () {
    $validators = $this->_entity->getValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['quantidade']);
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
});
