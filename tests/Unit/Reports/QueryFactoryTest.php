<?php

namespace Tests\unit\Reports;

use iEducar\Modules\Reports\QueryFactory\QueryFactory;
use Tests\SuiteTestCase\TestCase;

class QueryFactoryTest extends TestCase
{
    private static $pdo;

    protected function setUp() :void
    {
        if (!self::$pdo) {
            self::$pdo = self::getPdoConection();
            self::tearDownAfterClass();
        }
    }

    public static function tearDownAfterClass()
    {
        self::$pdo->exec(
            "DELETE FROM pmieducar.usuario WHERE cod_usuario IN (-1,-2);\n".
            'SET session_replication_role = DEFAULT;'
            );
    }

    public function testUnvaluedKey()
    {
        $fakeClass = new class(self::$pdo, []) extends QueryFactory {
            protected $keys = ['fake_key'];
        };

        $this->expectException(\InvalidArgumentException::class);
        $fakeClass->getData();
    }

    public function testArrayValue()
    {
        self::$pdo->exec(
            "SET session_replication_role = replica;\n".
            'INSERT INTO pmieducar.usuario (cod_usuario, ref_cod_instituicao, ref_funcionario_cad, data_cadastro, ativo) VALUES (-1, 1, 1, NOW(), 1), (-2, 1, 1, NOW(), 1);'
        );

        $fakeClass = new class(self::$pdo, []) extends QueryFactory {
            protected $keys = ['usuarios'];
            protected $query = 'SELECT * FROM pmieducar.usuario WHERE cod_usuario IN (:usuarios)';
        };
        $fakeClass->setParams(['usuarios' => [-1,-2]]);
        $data = $fakeClass->getData();
        $this->assertCount(2, $data);
    }

    public function testSingleValue()
    {
        $fakeClass = new class(self::$pdo, []) extends QueryFactory {
            protected $keys = ['usuario'];
            protected $query = 'SELECT * FROM pmieducar.usuario WHERE cod_usuario = :usuario';
        };
        $fakeClass->setParams(['usuario' => 1]);
        $this->assertEquals(1, $fakeClass->getData()[0]['cod_usuario']);
    }
}