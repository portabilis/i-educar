<?php

class Educacenso_Model_AlunoTest extends PHPUnit\Framework\TestCase
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Educacenso_Model_Aluno();
    }

    public function testEntityValidators()
    {
        // Recupera os objetos CoreExt_Validate
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['aluno']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['alunoInep']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['nomeInep']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['fonte']);
    }
}
