<?php


/**
 * CoreExt_Enum2Stub class.
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
class CoreExt_Enum2Stub extends CoreExt_Enum
{
    const TWO = 2;

    protected $_data = [
        self::TWO => 2
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
