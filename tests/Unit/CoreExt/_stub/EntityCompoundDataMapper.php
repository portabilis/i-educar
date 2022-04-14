<?php

class CoreExt_EntityCompoundDataMapperStub extends CoreExt_DataMapper
{
    protected $_entityClass = 'CoreExt_EntityCompoundStub';
    protected $_tableName = 'matricula';
    protected $_tableSchema = '';

    protected $_attributeMap = [
        'pessoa' => 'pessoa_id',
        'curso' => 'curso_id'
    ];

    protected $_primaryKey = [
        'pessoa' => 'pessoa',
        'curso' => 'curso'
    ];

    /**
     * Cria a tabela pessoa para testes de integração.
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
CREATE TABLE matricula(
  pessoa_id integer NOT NULL,
  curso_id integer NOT NULL,
  confirmado char(1) NULL DEFAULT \'t\',
  PRIMARY KEY(pessoa_id, curso_id)
);';

        return $db->Consulta($sql);
    }
}
