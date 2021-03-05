<?php


/**
 * CoreExt_Enum1Stub class.
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
class CoreExt_Enum1Stub extends CoreExt_Enum
{
    const ONE = 1;

    protected $_data = [
        self::ONE => 1
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
