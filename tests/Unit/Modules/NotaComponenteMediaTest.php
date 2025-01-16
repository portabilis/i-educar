<?php

class NotaComponenteMediaTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_NotaComponenteMedia;
    }

    public function test_entity_validators()
    {
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['media']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['mediaArredondada']);
    }
}
