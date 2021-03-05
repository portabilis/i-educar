<?php


/**
 * CoreExt_EnumTipoSanguineoStub class.
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
class CoreExt_EnumTipoSanguineoStub extends CoreExt_Enum
{
    const A = 1;
    const B = 2;
    const AB = 3;
    const O = 4;

    protected $_data = [
        self::A => 'A',
        self::B => 'B',
        self::AB => 'AB',
        self::O => 'O'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
