<?php


/**
 * CoreExt_EntityDataMapperStub class.
 *
 * Entidade para testes de integração do componente CoreExt_DataMapper com
 * o banco de dados.
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
class CoreExt_EntityDataMapperStub extends CoreExt_DataMapper
{
    protected $_entityClass = 'CoreExt_EntityStub';
    protected $_tableName = 'pessoa';
    protected $_tableSchema = '';

    protected $_attributeMap = [
        'estadoCivil' => 'estado_civil'
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
CREATE TABLE pessoa(
  id integer primary key,
  nome character varying(100) NOT NULL,
  estado_civil character varying(20) NOT NULL DEFAULT \'solteiro\',
  doador char(1) NULL DEFAULT \'t\'
);';

        return $db->Consulta($sql);
    }
}
