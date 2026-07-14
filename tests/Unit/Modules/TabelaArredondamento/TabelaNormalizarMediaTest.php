<?php

class TabelaNormalizarMediaTest extends UnitBaseTest
{
    public function test_soma_sem_normalizacao_retorna_ultimo_conceito()
    {
        $tabela = $this->criarTabela(normalizarMedia: false);
        $resultado = $tabela->round(75, 2, 1, 4);

        $this->assertEquals('A', $resultado);
    }

    public function test_soma_com_normalizacao_encontra_conceito_correto()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        // 75 / 4 = 18.75 → range C (15-18.9)
        $resultado = $tabela->round(75, 2, 1, 4);

        $this->assertEquals('C', $resultado);
    }

    public function test_nota_etapa_nao_normaliza()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        // tipoNota=1 (etapa) não normaliza
        $resultado = $tabela->round(20, 1, 1, 4);

        $this->assertEquals('B', $resultado);
    }

    public function test_normalizacao_com_2_etapas()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        // 40 / 2 = 20 → range B (19-21.9)
        $resultado = $tabela->round(40, 2, 1, 2);

        $this->assertEquals('B', $resultado);
    }

    public function test_normalizacao_soma_maxima()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        // 100 / 4 = 25 → range A (22-25)
        $resultado = $tabela->round(100, 2, 1, 4);

        $this->assertEquals('A', $resultado);
    }

    public function test_normalizacao_soma_minima()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        // 20 / 4 = 5 → range D (1-14.9)
        $resultado = $tabela->round(20, 2, 1, 4);

        $this->assertEquals('D', $resultado);
    }

    public function test_sem_qtde_etapas_nao_normaliza()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(20, 2, 1, null);

        $this->assertEquals('B', $resultado);
    }

    public function test_qtde_etapas_zero_nao_normaliza()
    {
        $tabela = $this->criarTabela(normalizarMedia: true);
        $resultado = $tabela->round(20, 2, 1, 0);

        $this->assertEquals('B', $resultado);
    }

    public function test_tabela_numerica_ignora_normalizacao()
    {
        $tabela = new TabelaArredondamento_Model_Tabela([
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
            'normalizarMedia' => true,
        ]);

        // Define valores dummy para evitar consulta ao banco
        $reflection = new ReflectionClass($tabela);
        $property = $reflection->getProperty('_tabelaValores');
        $property->setAccessible(true);
        $property->setValue($tabela, [$this->criarValor('0', 0, 0)]);

        $resultado = $tabela->round(7.5, 2, 1, 4);

        // Tabela numérica retorna valor sem normalização
        $this->assertEquals(7.5, $resultado);
    }

    private function criarTabela($normalizarMedia)
    {
        $tabela = new TabelaArredondamento_Model_Tabela([
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL,
            'normalizarMedia' => $normalizarMedia,
        ]);

        // Ranges: D(1-14.9), C(15-18.9), B(19-21.9), A(22-25)
        $valores = [
            $this->criarValor('D', 1, 14.9),
            $this->criarValor('C', 15, 18.9),
            $this->criarValor('B', 19, 21.9),
            $this->criarValor('A', 22, 25),
        ];

        $reflection = new ReflectionClass($tabela);
        $property = $reflection->getProperty('_tabelaValores');
        $property->setAccessible(true);
        $property->setValue($tabela, $valores);

        return $tabela;
    }

    private function criarValor($nome, $min, $max)
    {
        return new TabelaArredondamento_Model_TabelaValor([
            'nome' => $nome,
            'valorMinimo' => $min,
            'valorMaximo' => $max,
        ]);
    }
}
