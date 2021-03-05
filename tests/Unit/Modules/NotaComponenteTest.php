<?php


/**
 * NotaComponenteTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class NotaComponenteTest extends UnitBaseTest
{
    protected $_entity = null;

    protected function setUp(): void
    {
        $this->_entity = new Avaliacao_Model_NotaComponente();
    }

    public function testEntityValidators()
    {
        $validators = $this->_entity->getValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Numeric', $validators['nota']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['notaArredondada']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['etapa']);
    }
}
