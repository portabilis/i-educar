<?php

class ParecerDescritivoAlunoTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_ParecerDescritivoAluno();
    }

    public function testEntityValidators()
    {
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['matricula']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['parecerDescritivo']);
    }
}
