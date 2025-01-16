<?php

class FaltaAbstractTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_FaltaAbstractStub;
    }

    public function test_entity_validators()
    {
        $validators = $this->_entity->getValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['quantidade']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
    }
}
