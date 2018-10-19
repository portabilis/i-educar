<?php

require_once 'CoreExt/DataMapper.php';
require_once 'Biblioteca/Model/TipoExemplar.php';

class Biblioteca_Model_TipoExemplarDataMapper extends CoreExt_DataMapper
{
    /**
     * @var string $_entityClass
     */
    protected $_entityClass = 'Biblioteca_Model_TipoExemplar';

    /**
     * @var string $_tableName
     */
    protected $_tableName   = 'exemplar_tipo';

    /**
     * @var string $_tableSchema
     */
    protected $_tableSchema = 'pmieducar';

    /**
     * @var array $_attributeMap
     */
    protected $_attributeMap = [
        'cod_exemplar_tipo'  => 'cod_exemplar_tipo',
        'ref_cod_biblioteca' => 'ref_cod_biblioteca',
        'ref_usuario_exc'    => 'ref_usuario_exc',
        'ref_usuario_cad'    => 'ref_usuario_cad',
        'nm_tipo'            => 'nm_tipo',
        'descricao'          => 'descricao',
        'data_cadastro'      => 'data_cadastro',
        'data_exclusao'      => 'data_exclusao',
        'ativo'              => 'ativo'
    ];

    /**
     * @var array $_notPersistable
     */
    protected $_notPersistable = [];

    /**
     * @var $_primaryKey
     */
    protected $_primaryKey = [
        'cod_exemplar_tipo' => 'cod_exemplar_tipo'
    ];
}
