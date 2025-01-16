<?php

class Educacenso_Model_CursoSuperiorTest extends PHPUnit\Framework\TestCase
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Educacenso_Model_CursoSuperior;
    }

    public function test_instancia_de_area_retorna_o_valor_de_nome_em_contexto_de_impressao()
    {
        $this->_entity->nome = 'Curso superior';
        $this->assertEquals('Curso superior', $this->_entity->__toString());
    }

    public function test_entity_validators()
    {
        // Recupera os objetos CoreExt_Validate
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['curso']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['nome']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['classe']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['user']);
    }
}
