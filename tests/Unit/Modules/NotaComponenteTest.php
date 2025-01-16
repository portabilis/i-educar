<?php

class NotaComponenteTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_NotaComponente;
    }

    public function test_entity_validators()
    {
        $validators = $this->_entity->getValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['nota']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['notaArredondada']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
    }
}
