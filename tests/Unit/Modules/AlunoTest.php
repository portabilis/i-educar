<?php


/**
 * Educacenso_Model_AlunoTest class.
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
