<?php

class Educacenso_Model_DocenteTest extends PHPUnit\Framework\TestCase
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Educacenso_Model_Docente();
    }

    public function testEntityValidators()
    {
        // Recupera os objetos CoreExt_Validate
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['docente']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['docenteInep']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['nomeInep']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['fonte']);
    }
}
