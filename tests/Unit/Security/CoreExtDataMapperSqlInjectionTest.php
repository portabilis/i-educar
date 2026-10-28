<?php

use Tests\TestCase;

/**
 * CVE-2025-10011 e relacionadas: o DataMapper dos módulos concatenava a chave
 * recebida na consulta sem escape.
 */
uses(TestCase::class);

test('busca por chave escapa o valor', function () {
    $mapper = new TabelaArredondamento_Model_TabelaDataMapper;

    $sql = (fn ($pkey) => $this->_getFindStatment($pkey))->call($mapper, "1' OR '1'='1");

    $this->assertStringContainsString("id = '1'' OR ''1''=''1'", $sql);
});

test('busca por chave composta escapa os valores', function () {
    $mapper = new TabelaArredondamento_Model_TabelaDataMapper;

    $sql = (fn ($pkey) => $this->_getFindStatment($pkey))->call($mapper, ['id' => "1' OR '1'='1"]);

    $this->assertStringContainsString("id = '1'' OR ''1''=''1'", $sql);
});
