<?php

class Biblioteca_Model_TipoExemplarDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'Biblioteca_Model_TipoExemplar';
    protected $_tableName   = 'exemplar_tipo';
    protected $_tableSchema = 'pmieducar';

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

    protected $_notPersistable = [];

    protected $_primaryKey = [
    'cod_exemplar_tipo' => 'cod_exemplar_tipo'
  ];
}
