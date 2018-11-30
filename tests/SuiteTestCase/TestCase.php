<?php

namespace Tests\SuiteTestCase;

use clsBanco;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\Operation\Composite;
use PHPUnit\DbUnit\TestCase as AbstractTestCase;

class TestCase extends AbstractTestCase
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
            self::$connection = $this->createDefaultDBConnection($pdo);
        }

        return self::$connection;
    }

    /**
     * Returns the test dataset.
     *
     */
    protected function getDataSet()
    {
        return (new DataSet($this))->getDataSet();
    }

    public function getYamlDataSet()
    {
        return new DefaultDataSet();
    }

    protected function getSetUpOperation()
    {
        return new Composite(
            [
                new ForeignKeysCheckDisable(),
                new InsertTriggerEnable()
            ]
        );
    }

    public function getTearDownOperation()
    {
        return new ForeignKeysCheckDisable();
    }

    public function setupDump($file)
    {
        $this->getConnection()->getConnection()->exec('SET session_replication_role = replica;');
        $this->getConnection()->getConnection()->exec(file_get_contents(__DIR__ . '/../Unit/dumps/' . $file));
        $this->getConnection()->getConnection()->exec('SET session_replication_role = DEFAULT;');
    }

    public function getHtmlCodeFromFile($fileName)
    {
        return  file_get_contents(__DIR__ . '/../Unit/assets/' . $fileName);
    }

    public function getPdoConection()
    {
        return $this->getConnection()->getConnection();
    }
}
