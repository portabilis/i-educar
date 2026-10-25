<?php

use App\Models\LegacyDisciplineAcademicYear;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGradeFactory;
use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/Api/ComponentesSerie', [
    'existe-dispensa',
    'existe-dependencia',
    'get-escolas-by-serie',
]);

test('vincula os componentes a todas as séries informadas', function () {
    $series = LegacyGradeFactory::new()->count(2)->create();
    $componente = LegacyDisciplineFactory::new()->create();

    $this->post('/module/Api/ComponentesSerie?' . http_build_query([
        'oper' => 'post',
        'resource' => 'atualiza-componentes-serie',
        'series' => json_encode($series->modelKeys()),
        'componentes' => json_encode([[
            'id' => $componente->getKey(),
            'carga_horaria' => 80,
            'hora_falta' => 0.75,
            'tipo_nota' => ComponenteSerie_Model_TipoNota::NUMERICA,
            'anos_letivos' => [now()->year],
        ]]),
    ] + $this->params))
        ->assertOk()
        ->assertJsonCount(2, 'series')
        ->assertJsonCount(1, 'series.0.insert')
        ->assertJsonCount(1, 'series.1.insert');

    foreach ($series as $serie) {
        expect(LegacyDisciplineAcademicYear::query()->where('ano_escolar_id', $serie->getKey())->where('componente_curricular_id', $componente->getKey())->count())->toBe(1);
    }
});

test('atualiza o componente nas séries em que ele já existe, sem duplicar', function () {
    $vinculo = LegacyDisciplineAcademicYearFactory::new()->create();
    $serieNova = LegacyGradeFactory::new()->create();

    $this->post('/module/Api/ComponentesSerie?' . http_build_query([
        'oper' => 'post',
        'resource' => 'atualiza-componentes-serie',
        'series' => json_encode([$vinculo->ano_escolar_id, $serieNova->getKey()]),
        'componentes' => json_encode([[
            'id' => $vinculo->componente_curricular_id,
            'carga_horaria' => 80,
            'hora_falta' => 0.75,
            'tipo_nota' => ComponenteSerie_Model_TipoNota::NUMERICA,
            'anos_letivos' => [now()->year],
        ]]),
    ] + $this->params))
        ->assertOk()
        ->assertJsonCount(1, 'series.0.update')
        ->assertJsonCount(0, 'series.0.insert')
        ->assertJsonCount(1, 'series.1.insert');

    expect(LegacyDisciplineAcademicYear::query()->where('ano_escolar_id', $vinculo->ano_escolar_id)->where('componente_curricular_id', $vinculo->componente_curricular_id)->count())->toBe(1)
        ->and(LegacyDisciplineAcademicYear::query()->where('ano_escolar_id', $vinculo->ano_escolar_id)->value('carga_horaria'))->toEqual(80);
});
