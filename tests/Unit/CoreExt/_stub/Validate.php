<?php


/**
 * CoreExt_ValidateStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_ValidateStub extends CoreExt_Validate_Abstract
{
    protected function _getDefaultOptions()
    {
        return [];
    }

    protected function _validate($value)
    {
        return true;
    }
}
