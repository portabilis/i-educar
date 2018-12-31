<?php

namespace Tests\SuiteTestCase;

use clsBanco;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\TestCaseTrait;
use \Tests\TestCase as AbstractTestCase;

class TestCase extends AbstractTestCase
{
    use TestCaseTrait;
    /**
     * @var Connection
     */
    private static $connection;
    protected function setUp() :void
    {
        $_GET['etapa'] = 'Rc';
        parent::setUp();
    }

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    protected function getConnection($connection = NULL)
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
}
