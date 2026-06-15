<?php

use PHPUnit\Framework\TestCase;

class Educacenso_Model_DocenteTest extends TestCase
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Educacenso_Model_Docente;
    }

    public function test_entity_validators()
    {
        // Recupera os objetos CoreExt_Validate
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['docente']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['docenteInep']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['nomeInep']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['fonte']);
    }
}
