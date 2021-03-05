<?php


/**
 * CoreExt_SingletonStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Singleton
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_SingletonStub extends CoreExt_Singleton
{
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
