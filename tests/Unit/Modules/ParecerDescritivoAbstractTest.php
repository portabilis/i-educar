<?php

uses(UnitBaseTest::class);

beforeEach(function () {
    $this->_entity = new Avaliacao_Model_ParecerDescritivoAbstractStub;
});

test('entity validators', function () {
    $validators = $this->_entity->getValidatorCollection();
    $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
    $this->assertInstanceOf('CoreExt_Validate_String', $validators['parecer']);

    // Verifica se as opções de etapa incluem 'An'
    $this->assertTrue(in_array('An', $validators['etapa']->getOption('choices')));
});
