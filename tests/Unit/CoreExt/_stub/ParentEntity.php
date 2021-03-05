<?php


/**
 * CoreExt_ParentEntityStub class.
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
class CoreExt_ParentEntityStub extends CoreExt_Entity
{
    protected $_data = [
        'nome' => null,
        'filho' => null
    ];

    protected $_references = [
        'filho' => [
            'value' => null,
            'class' => 'CoreExt_ChildEntityDataMapperStub',
            'file' => __DIR__ . '/ChildEntityDataMapper.php',
            'null' => true
        ]
    ];

    public function getDefaultValidatorCollection()
    {
        return [
            'filho' => new CoreExt_Validate_String(['max' => 1])
        ];
    }
}
