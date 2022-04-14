<?php

class NotaAlunoTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_NotaAluno();
    }

    public function testEntityValidators()
    {
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['matricula']);
    }
}
