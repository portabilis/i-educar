<?php


/**
 * CoreExt_EnumCoffeeStub class.
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
class CoreExt_EnumCoffeeStub extends CoreExt_Enum
{
    const AMERICANO = 0;
    const MOCHA = 1;
    const ESPRESSO = 2;

    protected $_data = [
        self::AMERICANO => '',
        self::MOCHA => 'Mocha',
        self::ESPRESSO => 'ESPRESSO',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
