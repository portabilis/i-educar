<?php
/**
 * Created by PhpStorm.
 * User: evertonmuniz
 * Date: 19/06/18
 * Time: 01:11
 */

namespace Tests\SuiteTestCase;


use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Operation\DeleteAll;

class IeducarForeignKeysCheckDisable extends DeleteAll
{

    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        $sql = "SET session_replication_role = replica;";
        $connection->getConnection()->query($sql);
        parent::execute($connection, $dataSet);
    }
}