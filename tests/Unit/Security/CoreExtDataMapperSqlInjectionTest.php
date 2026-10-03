<?php

namespace Tests\Unit\Security;

use TabelaArredondamento_Model_TabelaDataMapper;
use Tests\TestCase;

/**
 * CVE-2025-10011 e relacionadas: o DataMapper dos módulos concatenava a chave
 * recebida na consulta sem escape.
 */
class CoreExtDataMapperSqlInjectionTest extends TestCase
{
    private const INJECTION = "1' OR '1'='1";

    public function test_busca_por_chave_escapa_o_valor()
    {
        $mapper = new TabelaArredondamento_Model_TabelaDataMapper;

        $sql = (fn ($pkey) => $this->_getFindStatment($pkey))->call($mapper, self::INJECTION);

        $this->assertStringContainsString("id = '1'' OR ''1''=''1'", $sql);
    }

    public function test_busca_por_chave_composta_escapa_os_valores()
    {
        $mapper = new TabelaArredondamento_Model_TabelaDataMapper;

        $sql = (fn ($pkey) => $this->_getFindStatment($pkey))->call($mapper, ['id' => self::INJECTION]);

        $this->assertStringContainsString("id = '1'' OR ''1''=''1'", $sql);
    }
}
