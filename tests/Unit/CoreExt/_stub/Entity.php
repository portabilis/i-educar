<?php


/**
 * CoreExt_EntityStub class.
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
class CoreExt_EntityStub extends CoreExt_Entity
{
    protected $_data = [
        'nome' => null,
        'estadoCivil' => null,
        'doador' => null,
    ];

    protected $_dataTypes = [
        'doador' => 'bool'
    ];

    public function getDefaultValidatorCollection()
    {
        return [
            'nome' => new CoreExt_Validate_String(),
        ];
    }
}
