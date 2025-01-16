<?php

use Tests\TestCase;

class ClsBancoTest extends TestCase
{
    public function test_formatacao_de_valores_booleanos()
    {
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
    }

    public function test_opcao_de_lancamento_de_excecao_e_false_por_padrao()
    {
        $db = new clsBanco;

        $this->assertFalse($db->getThrowException());
    }

    public function test_configuracao_de_opcao_de_lancamento_de_excecao()
    {
        $db = new clsBanco;
        $db->setThrowException(true);

        $this->assertTrue($db->getThrowException());
    }

    public function test_fetch_tipo_array_de_resultados_de_uma_query()
    {
        $db = new clsBanco;
        $db->Consulta('SELECT spcname FROM pg_tablespace');

        $row = $db->ProximoRegistro();
        $row = $db->Tupla();

        $this->assertNotNull($row[0]);
        $this->assertNotNull($row['spcname']);
    }

    public function test_fetch_tipo_assoc_de_resultados_de_uma_query()
    {
        $db = new clsBanco(['fetchMode' => clsBanco::FETCH_ASSOC]);
        $db->Consulta('SELECT spcname FROM pg_tablespace');

        $row = $db->ProximoRegistro();
        $row = $db->Tupla();

        $this->assertFalse(array_key_exists(0, $row));
        $this->assertNotNull($row['spcname']);
    }
}
