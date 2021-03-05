<?php


/**
 * CoreExt_EnumStringStub class.
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
class CoreExt_EnumStringStub extends CoreExt_Enum
{
    const RED = 'red';

    protected $_data = [
        self::RED => '#FF0000'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
