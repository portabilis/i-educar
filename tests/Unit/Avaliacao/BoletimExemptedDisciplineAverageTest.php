<?php

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

/**
 * Lança a nota direto no boletim, sem a validação da API v2, que recusa etapa dispensada:
 * a API v1 e a tela de lançamento ainda chegam ao cálculo da média com o componente
 * dispensado em todas as etapas.
 */
uses(TestCase::class, CreatesScoreData::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->saveScore = function (array $data, float $score) {
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
    };

    $this->averageOf = function (array $data) {
        return LegacyDisciplineScoreAverage::query()
            ->where('componente_curricular_id', $data['discipline']->getKey());
    };
});

test('stores score without average when every stage is exempted', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);
    $this->exemptDiscipline($data, [1]);

    ($this->saveScore)($data, 6);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 1,
        'nota_original' => 6,
    ]);
    $this->assertFalse(($this->averageOf)($data)->exists());
});

test('removes the calculated average when every stage becomes exempted', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);

    ($this->saveScore)($data, 6);
    $this->assertTrue(($this->averageOf)($data)->exists());

    $this->exemptDiscipline($data, [1]);
    // As etapas dispensadas ficam em cache por um minuto; a dispensa nova precisa ser lida do banco.
    Cache::flush();
    ($this->saveScore)($data, 8);

    $this->assertFalse(($this->averageOf)($data)->exists());
});

test('keeps the locked average when every stage becomes exempted', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);

    ($this->saveScore)($data, 6);
    ($this->averageOf)($data)->update(['bloqueada' => true]);

    $this->exemptDiscipline($data, [1]);
    Cache::flush();
    ($this->saveScore)($data, 8);

    $this->assertTrue(($this->averageOf)($data)->exists());
});
