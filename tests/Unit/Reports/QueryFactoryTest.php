<?php

namespace Tests\Unit\Reports;

use iEducar\Modules\Reports\QueryFactory\QueryFactory;
use Tests\SuiteTestCase\TestCase;

class QueryFactoryTest extends TestCase
{
    public function testUnvaluedKey()
    {
        $pdo = $this->getPdoConection();

        $fakeClass = new class($pdo, []) extends QueryFactory{
            protected $keys = ['fake_key'];
        };

        $this->expectException(\InvalidArgumentException::class);
        $fakeClass->getData();
    }

    public function testArrayValue()
    {
        $this->setupDump('fakeusers.sql');

        $pdo = $this->getPdoConection();

        $fakeClass = new class($pdo, []) extends QueryFactory{
            protected $keys = ['usuarios'];
            protected $query = 'SELECT * FROM pmieducar.usuario WHERE cod_usuario IN (:usuarios)';
        };
        $fakeClass->setParams(['usuarios' => [1,2]]);
        $this->assertCount(2, $fakeClass->getData());
    }

    public function testSingleValue()
    {
        $pdo = $this->getPdoConection();

        $fakeClass = new class($pdo, []) extends QueryFactory{
            protected $keys = ['usuario'];
            protected $query = 'SELECT * FROM pmieducar.usuario WHERE cod_usuario = :usuario';
        };
        $fakeClass->setParams(['usuario' => 1]);
        $this->assertEquals(1, $fakeClass->getData()[0]['cod_usuario']);
    }
}
