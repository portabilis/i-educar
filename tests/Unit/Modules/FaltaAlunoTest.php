<?php

class FaltaAlunoTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_FaltaAluno();
    }

    public function testEntityValidators()
    {
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['matricula']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['tipoFalta']);
    }
}
