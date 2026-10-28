<?php

use Tests\TestCase;

uses(TestCase::class);

test('formatacao de valores booleanos', function () {
    $data = [
        'id' => 1,
        'hasChild' => true,
    ];

    $db = new clsBanco;

    $formatted = $db->formatValues($data);
    $this->assertSame('t', $formatted['hasChild']);

    $data['hasChild'] = false;
    $formatted = $db->formatValues($data);

    $this->assertSame('f', $formatted['hasChild']);
});

test('opcao de lancamento de excecao e false por padrao', function () {
    $db = new clsBanco;

    $this->assertFalse($db->getThrowException());
});

test('configuracao de opcao de lancamento de excecao', function () {
    $db = new clsBanco;
    $db->setThrowException(true);

    $this->assertTrue($db->getThrowException());
});

test('fetch tipo array de resultados de uma query', function () {
    $db = new clsBanco;
    $db->Consulta('SELECT spcname FROM pg_tablespace');

    $row = $db->ProximoRegistro();
    $row = $db->Tupla();

    $this->assertNotNull($row[0]);
    $this->assertNotNull($row['spcname']);
});

test('fetch tipo assoc de resultados de uma query', function () {
    $db = new clsBanco(['fetchMode' => clsBanco::FETCH_ASSOC]);
    $db->Consulta('SELECT spcname FROM pg_tablespace');

    $row = $db->ProximoRegistro();
    $row = $db->Tupla();

    $this->assertFalse(array_key_exists(0, $row));
    $this->assertNotNull($row['spcname']);
});
