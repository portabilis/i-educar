<?php

class CoreExt_ParentEntityDataMapperStub extends CoreExt_DataMapper
{
    protected $_entityClass = 'CoreExt_ParentEntityStub';
    protected $_tableName = 'parent';
    protected $_tableSchema = '';

    protected $_attributeMap = [
        'filho' => 'filho_id'
    ];

    /**
     * Cria a tabela "parent" para testes de integração.
     *
     * SQL compatível com SQLite.
     *
     * @param clsBancoPdo $db
     *
     * @return mixed Retorna FALSE em caso de erro
     */
    public static function createTable(clsBanco $db)
    {
        $sql = '
CREATE TABLE parent(
  id integer primary key,
  filho_id integer,
  nome character varying(100) NOT NULL
);';

        return $db->Consulta($sql);
    }
}
