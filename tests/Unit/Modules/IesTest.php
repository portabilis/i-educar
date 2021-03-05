<?php


/**
 * Educacenso_Model_IesTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Educacenso
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.2.0
 *
 * @version     @@package_version@@
 */
class Educacenso_Model_IesTest extends PHPUnit\Framework\TestCase
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Educacenso_Model_Ies();
    }

    public function testEntityValidators()
    {
        // Recupera os objetos CoreExt_Validate
        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['ies']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['nome']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['dependenciaAdministrativa']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['tipoInstituicao']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['uf']);
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['user']);
    }
}
