<?php

namespace Tests\SuiteTestCase;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Operation\DeleteAll;

class ForeignKeysCheckDisable extends DeleteAll
{
    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        $sql = "SET session_replication_role = replica;";
        $connection->getConnection()->query($sql);
        parent::execute($connection, $dataSet);
    }
}
