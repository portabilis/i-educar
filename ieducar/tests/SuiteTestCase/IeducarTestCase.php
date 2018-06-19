<?php

namespace Tests\SuiteTestCase;

require_once __DIR__ . '/../../intranet/include/clsBanco.inc.php';

use clsBanco;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\TestCase;

class IeducarTestCase extends TestCase
{

    /**
     * @var Connection
     */
    private static $connection;

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    protected function getConnection()
    {
        if (!self::$connection) {
            $banco = new clsBanco();
            $banco->FraseConexao();
            $pdo = new \PDO('pgsql:' . $banco->getFraseConexao());
            self::$connection =  $this->createDefaultDBConnection($pdo);
        }

        return self::$connection;
    }

    /**
     * Returns the test dataset.
     *
     */
    protected function getDataSet()
    {
        return new IeducarDataSet($this);
    }

    public function getYamlDataSet()
    {
        return new DefaultDataSet();
    }

    public function getSetUpOperation()
    {
        return new IeducarForeignKeysCheckDisable();
    }

    public function getTearDownOperation()
    {
        return new IeducarForeignKeysCheckDisable();
    }
}