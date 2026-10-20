<?php

namespace Tests\Feature\Repositories\Educacenso;

use App\Repositories\EducacensoRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EducacensoRepositoryRecord00Test extends TestCase
{
    use DatabaseTransactions;

    private EducacensoRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EducacensoRepository();
    }

    //TESTES DE CAIXA PRETA

    /** @test */
    public function retorna_array_para_escola_e_ano_validos()
    {
        $result = $this->repository->getDataForRecord00(1, (int) date('Y'));

        $this->assertIsArray($result);
    }

    /** @test */
    public function retorna_array_vazio_para_escola_inexistente()
    {
        $result = $this->repository->getDataForRecord00(999_999_999, 2024);

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Escola inexistente deveria retornar array vazio.');
    }

    /** @test */
    public function retorna_array_vazio_para_ano_sem_dados()
    {
        $result = $this->repository->getDataForRecord00(1, 1900);

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Ano sem matrículas deveria retornar array vazio.');
    }

    /** @test */
    public function nao_lanca_excecao_para_entradas_tipicas()
    {
        // Só queremos garantir que o método é robusto a entradas inteiras válidas.
        $this->repository->getDataForRecord00(0, 0);
        $this->assertTrue(true); 
    }

    /**
     * Só roda se houver dados de teste reais disponíveis.
     * Marque como skip caso o banco de CI esteja vazio.
     *
     * @test
     */
    public function estrutura_de_saida_contem_chaves_esperadas_quando_ha_dados()
    {
        $result = $this->repository->getDataForRecord00(1, 2024);

        if (empty($result)) {
            $this->markTestSkipped('Sem dados de escola/ano no banco para validar a estrutura.');
        }

        $row = $result[0];

        foreach ([
            'registro',
            'codigoInep',
            'situacaoFuncionamento',
            'inicioAnoLetivo',
            'fimAnoLetivo',
        ] as $key) {
            $this->assertObjectHasProperty($key, $row, "Chave '{$key}' ausente na saída.");
        }

        $this->assertSame('00', $row->registro);
    }

    // TESTES DE CAIXA BRANCA

    /** @test */
    public function passa_bindings_corretos_para_a_query()
    {
        $capturedSql      = null;
        $capturedBindings = null;

        $sut = $this->mockFetchPreparedQuery(
            function ($sql, $bindings) use (&$capturedSql, &$capturedBindings) {
                $capturedSql      = $sql;
                $capturedBindings = $bindings;
                return [];
            }
        );

        $sut->getDataForRecord00(42, 2023);

        $this->assertIsString($capturedSql);
        $this->assertStringContainsString(':school', $capturedSql);
        $this->assertStringContainsString(':year', $capturedSql);

        $this->assertSame(42,   $capturedBindings['school']);
        $this->assertSame(2023, $capturedBindings['year']);
    }

    /** @test */
    public function sql_contem_subconsulta_de_ano_letivo()
    {
        $capturedSql = null;

        $sut = $this->mockFetchPreparedQuery(
            function ($sql) use (&$capturedSql) {
                $capturedSql = $sql;
                return [];
            }
        );

        $sut->getDataForRecord00(1, 2024);

        $this->assertStringContainsStringIgnoringCase('ano_letivo_modulo', $capturedSql);
        $this->assertStringContainsStringIgnoringCase('data_inicio', $capturedSql);
        $this->assertStringContainsStringIgnoringCase('data_fim', $capturedSql);
    }

    /** @test */
    public function sql_contem_juncao_com_escola()
    {
        $capturedSql = null;

        $sut = $this->mockFetchPreparedQuery(
            function ($sql) use (&$capturedSql) {
                $capturedSql = $sql;
                return [];
            }
        );

        $sut->getDataForRecord00(1, 2024);

   
        $this->assertStringContainsStringIgnoringCase('pmieducar.escola', $capturedSql);
    }

    /** @test */
    public function delega_para_fetch_prepared_query_uma_unica_vez()
    {
        $sut = $this->getMockBuilder(EducacensoRepository::class)
            ->onlyMethods(['fetchPreparedQuery'])
            ->getMock();

        $sut->expects($this->once())
            ->method('fetchPreparedQuery')
            ->willReturn([]);

        $sut->getDataForRecord00(1, 2024);
    }


  
    // Helpers
    /**
     * Cria um mock parcial de EducacensoRepository trocando apenas
     * fetchPreparedQuery por um callback capturador.
     */
    private function mockFetchPreparedQuery(callable $onCall): EducacensoRepository
    {
        $mock = $this->getMockBuilder(EducacensoRepository::class)
            ->onlyMethods(['fetchPreparedQuery'])
            ->getMock();

        $mock->expects($this->once())
            ->method('fetchPreparedQuery')
            ->willReturnCallback($onCall);

        return $mock;
    }
}