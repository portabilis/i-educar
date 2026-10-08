<?php

namespace Tests\Unit\Avaliacao;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use Avaliacao_Model_NotaComponente;
use Avaliacao_Service_Boletim;
use CoreExt_Service_Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

/**
 * Lança a nota direto no boletim, sem a validação da API v2, que recusa etapa dispensada:
 * a API v1 e a tela de lançamento ainda chegam ao cálculo da média com o componente
 * dispensado em todas as etapas.
 */
class BoletimExemptedDisciplineAverageTest extends TestCase
{
    use CreatesScoreData;
    use DatabaseTransactions;

    private function saveScore(array $data, float $score): void
    {
        $boletim = new Avaliacao_Service_Boletim(['matricula' => $data['registration']->getKey()]);
        $boletim->addNota(new Avaliacao_Model_NotaComponente([
            'componenteCurricular' => $data['discipline']->getKey(),
            'nota' => $score,
            'etapa' => 1,
            'notaOriginal' => $score,
        ]));

        try {
            $boletim->save();
        } catch (CoreExt_Service_Exception) {
            // Mesmo tratamento do NotasController: sem mudança de situação não há promoção.
        }
    }

    private function averageOf(array $data)
    {
        return LegacyDisciplineScoreAverage::query()
            ->where('componente_curricular_id', $data['discipline']->getKey());
    }

    public function test_stores_score_without_average_when_every_stage_is_exempted()
    {
        $data = $this->createScoreData();
        $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);
        $this->exemptDiscipline($data, [1]);

        $this->saveScore($data, 6);

        $this->assertDatabaseHas(LegacyDisciplineScore::class, [
            'componente_curricular_id' => $data['discipline']->getKey(),
            'etapa' => 1,
            'nota_original' => 6,
        ]);
        $this->assertFalse($this->averageOf($data)->exists());
    }

    public function test_removes_the_calculated_average_when_every_stage_becomes_exempted()
    {
        $data = $this->createScoreData();
        $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);

        $this->saveScore($data, 6);
        $this->assertTrue($this->averageOf($data)->exists());

        $this->exemptDiscipline($data, [1]);
        // As etapas dispensadas ficam em cache por um minuto; a dispensa nova precisa ser lida do banco.
        Cache::flush();
        $this->saveScore($data, 8);

        $this->assertFalse($this->averageOf($data)->exists());
    }

    public function test_keeps_the_locked_average_when_every_stage_becomes_exempted()
    {
        $data = $this->createScoreData();
        $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);

        $this->saveScore($data, 6);
        $this->averageOf($data)->update(['bloqueada' => true]);

        $this->exemptDiscipline($data, [1]);
        Cache::flush();
        $this->saveScore($data, 8);

        $this->assertTrue($this->averageOf($data)->exists());
    }
}
