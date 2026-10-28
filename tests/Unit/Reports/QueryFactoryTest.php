<?php

use iEducar\Modules\Reports\QueryFactory\QueryFactory;
use Tests\TestCase;

uses(TestCase::class);

$pdo = null;

$cleanUp = function () use (&$pdo) {
    $pdo->exec(
        "DELETE FROM pmieducar.usuario WHERE cod_usuario IN (-1,-2);\n" .
        'SET session_replication_role = DEFAULT;'
    );
};

beforeEach(function () use (&$pdo, $cleanUp) {
    if (!$pdo) {
        $pdo = $this->getConnection()->getPdo();
        $cleanUp();
    }
});

afterAll(function () use ($cleanUp) {
    $cleanUp();
});

test('unvalued key', function () use (&$pdo) {
    $fakeClass = new class($pdo, []) extends QueryFactory
    {
        protected $keys = ['fake_key'];
    };

    $this->expectException(InvalidArgumentException::class);
    $fakeClass->getData();
});

test('array value', function () use (&$pdo) {
    $pdo->exec(
        "SET session_replication_role = replica;\n" .
        'INSERT INTO pmieducar.usuario (cod_usuario, ref_cod_instituicao, ref_funcionario_cad, data_cadastro, ativo) VALUES (-1, 1, 1, NOW(), 1), (-2, 1, 1, NOW(), 1);'
    );

    $fakeClass = new class($pdo, []) extends QueryFactory
    {
        protected $keys = ['usuarios'];

        protected $query = 'SELECT * FROM pmieducar.usuario WHERE cod_usuario IN (:usuarios)';
    };
    $fakeClass->setParams(['usuarios' => [-1, -2]]);
    $data = $fakeClass->getData();
    $this->assertCount(2, $data);
});
