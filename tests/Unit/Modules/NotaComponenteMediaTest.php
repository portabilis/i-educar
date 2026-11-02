<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->_entity = new Avaliacao_Model_NotaComponenteMedia;
});

test('entity validators', function () {
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['media']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['mediaArredondada']);
});
