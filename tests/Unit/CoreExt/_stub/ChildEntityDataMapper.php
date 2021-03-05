<?php


/**
 * CoreExt_ChildEntityDataMapperStub class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_ChildEntityDataMapperStub extends CoreExt_DataMapper
{
    protected $_entityClass = 'CoreExt_ChildEntityStub';
    protected $_tableName = 'child';
    protected $_tableSchema = '';

    protected $_attributeMap = [
        'tipoSanguineo' => 'tipo_sanguineo'
    ];

    /**
     * Cria a tabela "child" para testes de integração.
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
CREATE TABLE child(
  id integer primary key,
  nome character varying(100) NOT NULL,
  sexo integer default \'1\',
  tipo_sanguineo integer default \'1\',
  peso real default \'0\'
);';

        return $db->Consulta($sql);
    }
}
