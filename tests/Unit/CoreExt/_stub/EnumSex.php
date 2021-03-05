<?php


/**
 * CoreExt_EnumSexStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Enum
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_EnumSexStub extends CoreExt_Enum
{
    const MALE = 1;
    const FEMALE = 2;

    protected $_data = [
        self::MALE => 'masculino',
        self::FEMALE => 'feminino'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
