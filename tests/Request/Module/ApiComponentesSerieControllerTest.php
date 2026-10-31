<?php

use App\Models\LegacyDisciplineAcademicYear;
use App\Services\iDiarioService;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolGradeFactory;
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

test('mantém o formato antigo da resposta quando recebe apenas serie_id', function () {
    $serie = LegacyGradeFactory::new()->create();
    $componente = LegacyDisciplineFactory::new()->create();

    $this->post('/module/Api/ComponentesSerie?' . http_build_query([
        'oper' => 'post',
        'resource' => 'atualiza-componentes-serie',
        'serie_id' => $serie->getKey(),
        'componentes' => json_encode([[
            'id' => $componente->getKey(),
            'carga_horaria' => 80,
            'hora_falta' => 0.75,
            'tipo_nota' => ComponenteSerie_Model_TipoNota::NUMERICA,
            'anos_letivos' => [now()->year],
        ]]),
    ] + $this->params))
        ->assertOk()
        ->assertJsonCount(1, 'insert')
        ->assertJsonCount(0, 'update')
        ->assertJsonMissingPath('series');

    expect(LegacyDisciplineAcademicYear::query()->where('ano_escolar_id', $serie->getKey())->where('componente_curricular_id', $componente->getKey())->count())->toBe(1);
});

test('recusa séries inválidas sem gravar nada', function (string $series) {
    $componente = LegacyDisciplineFactory::new()->create();

    $this->post('/module/Api/ComponentesSerie?' . http_build_query([
        'oper' => 'post',
        'resource' => 'atualiza-componentes-serie',
        'series' => $series,
        'componentes' => json_encode([[
            'id' => $componente->getKey(),
            'carga_horaria' => 80,
            'hora_falta' => 0.75,
            'tipo_nota' => ComponenteSerie_Model_TipoNota::NUMERICA,
            'anos_letivos' => [now()->year],
        ]]),
    ] + $this->params))
        ->assertOk()
        ->assertJsonPath('msgErro', 'Nenhuma série válida foi informada.');

    expect(LegacyDisciplineAcademicYear::query()->where('componente_curricular_id', $componente->getKey())->count())->toBe(0);
})->with([
    'json inválido' => 'isso-nao-e-json',
    'lista vazia' => '[]',
    'ids não numéricos' => '["abc", 0]',
]);

test('não grava nenhuma série quando a validação falha em uma delas', function () {
    $anoAnterior = now()->year - 1;
    $serieNova = LegacyGradeFactory::new()->create();
    $vinculo = LegacyDisciplineAcademicYearFactory::new()->create(['anos_letivos' => '{' . $anoAnterior . ',' . now()->year . '}']);

    // Uma turma do ano que será removido e o iDiário respondendo que há lançamentos nela.
    $escolaSerie = LegacySchoolGradeFactory::new()->create(['ref_cod_serie' => $vinculo->ano_escolar_id]);
    LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $escolaSerie->ref_cod_escola,
        'ref_ref_cod_serie' => $vinculo->ano_escolar_id,
        'ano' => $anoAnterior,
    ]);

    LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
        'url_novo_educacao' => 'https://idiario.example',
        'token_novo_educacao' => 'token',
    ]);

    $this->mock(iDiarioService::class)->shouldReceive('getClassroomsActivityByDiscipline')->andReturn(true);

    $this->post('/module/Api/ComponentesSerie?' . http_build_query([
        'oper' => 'post',
        'resource' => 'atualiza-componentes-serie',
        'series' => json_encode([$serieNova->getKey(), $vinculo->ano_escolar_id]),
        'componentes' => json_encode([[
            'id' => $vinculo->componente_curricular_id,
            'carga_horaria' => 80,
            'hora_falta' => 0.75,
            'tipo_nota' => ComponenteSerie_Model_TipoNota::NUMERICA,
            'anos_letivos' => [now()->year],
        ]]),
    ] + $this->params))
        ->assertOk()
        ->assertJsonPath('msgErro', fn ($msg) => str_contains($msg, 'no iDiário'));

    expect(LegacyDisciplineAcademicYear::query()->where('ano_escolar_id', $serieNova->getKey())->count())->toBe(0)
        ->and(LegacyDisciplineAcademicYear::query()->where('ano_escolar_id', $vinculo->ano_escolar_id)->value('carga_horaria'))->toEqual(40);
});
