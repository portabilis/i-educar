<?php

namespace Tests\Unit\Services;

use App\Models\RegraAvaliacao;
use App\Models\Serie;
use App\Services\EscolaSerieService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EscolaSerieServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var EscolaSerieService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(EscolaSerieService::class);
        $this->disableForeignKeys();
        Serie::query()->truncate();
        RegraAvaliacao::query()->truncate();
    }

    public function tearDown()
    {
        $this->enableForeignKeys();
        parent::tearDown();
    }

    public function testRetornaRegrasAvaliacao()
    {
        $regraAvaliacaoFake = factory(RegraAvaliacao::class)->create();
        /** @var Serie $serie */
        $serie = factory(Serie::class)->create();

        $serie->regrasAvaliacao()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);

        $regrasAvaliacao = $this->service->getRegrasAvaliacaoSerie($serie->cod_serie);

        $this->assertCount(1, $regrasAvaliacao);
        $this->assertEquals($regraAvaliacaoFake->all(), $regrasAvaliacao->first()->all());
    }

    public function testSemRegrasDeveRetornarVazio()
    {
        $serie = factory(Serie::class)->create();
        $regrasAvaliacao = $this->service->getRegrasAvaliacaoSerie($serie->cod_serie);
        $this->assertEmpty($regrasAvaliacao);
    }

    public function testSemRegraAvaliacaoDeveRetornarFalse()
    {
        $result = $this->service->seriePermiteDefinirComponentesPorEtapa(null, 2019);
        $this->assertFalse($result);

        $serie = factory(Serie::class)->create();
        $result = $this->service->seriePermiteDefinirComponentesPorEtapa($serie->cod_serie, 2019);
        $this->assertFalse($result);

        $serie = factory(Serie::class)->create();
        $regraAvaliacaoFake = factory(RegraAvaliacao::class)->create([
            'definir_componente_etapa' => true,
        ]);
        $serie->regrasAvaliacao()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
        $result = $this->service->seriePermiteDefinirComponentesPorEtapa($serie->cod_serie, 2021);
        $this->assertFalse($result);
    }

    public function testRegraAvaliacaoPermiteDefinirComponentesEtapa()
    {
        $serie = factory(Serie::class)->create();
        $regraAvaliacaoFake = factory(RegraAvaliacao::class)->create([
            'definir_componente_etapa' => true,
        ]);

        $serie->regrasAvaliacao()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
        $result = $this->service->seriePermiteDefinirComponentesPorEtapa($serie->cod_serie, 2019);

        $this->assertTrue($result);

        $serie = factory(Serie::class)->create();
        $regraAvaliacaoFake = factory(RegraAvaliacao::class)->create([
            'definir_componente_etapa' => false,
        ]);

        $serie->regrasAvaliacao()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
        $result = $this->service->seriePermiteDefinirComponentesPorEtapa($serie->cod_serie, 2019);

        $this->assertFalse($result);
    }
}
