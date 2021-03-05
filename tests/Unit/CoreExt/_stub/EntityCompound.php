<?php


/**
 * CoreExt_EntityCompoundStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Entity
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_EntityCompoundStub extends CoreExt_Entity
{
    protected $_data = [
        'pessoa' => null,
        'curso' => null,
        'confirmado' => null
    ];

    protected $_dataTypes = [
        'confirmado' => 'bool'
    ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    public function getDefaultValidatorCollection()
    {
        return [];
    }
}
