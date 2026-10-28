<?php

use App\Services\SchoolClass\SchoolClassService;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = new SchoolClassService;

    // Os turnos dos alunos vêm de uma view do censo; aqui ela é substituída
    // pelo que a consulta devolveria.
    $this->periods = function (array $periods) {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')->with('codigoTurma', 7)->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect(array_map(fn ($period) => (object) ['turnoId' => $period], $periods)));

        DB::shouldReceive('table')->with('public.educacenso_record60')->andReturn($query);
    };
});

test('o nome está disponível quando nenhuma outra turma visível o usa na mesma escola, curso, série e ano', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma A']);
    $keys = [$schoolClass->ref_cod_curso, $schoolClass->ref_ref_cod_serie, $schoolClass->ref_ref_cod_escola, $schoolClass->ano];

    expect($this->service->isAvailableName('Turma A', ...$keys))->toBeFalse()
        ->and($this->service->isAvailableName('Turma A', ...[...$keys, $schoolClass->getKey()]))->toBeTrue()
        ->and($this->service->isAvailableName('Turma B', ...$keys))->toBeTrue();
});

test('o calendário das turmas vai da primeira à última etapa', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1, 'data_inicio' => '2030-02-01', 'data_fim' => '2030-06-30']);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 2, 'data_inicio' => '2030-08-01', 'data_fim' => '2030-12-15']);

    $calendars = $this->service->getCalendars([$schoolClass->getKey()]);

    expect($calendars)->toHaveCount(1)
        ->and($calendars->first()->start_date)->toBe('2030-02-01')
        ->and($calendars->first()->end_date)->toBe('2030-12-15');
});

test('os turnos dos alunos da turma vêm ordenados do integral ao noturno, sem repetir', function () {
    ($this->periods)([null, 3, 1, 2, 1]);

    expect($this->service->getStudentsPeriods(7)->values()->all())->toBe([Period::FULLTIME, Period::MORNING, Period::AFTERNOON, Period::NIGTH]);
});

test('a turma tem alunos em turnos parciais quando há mais de um turno ou um turno que não é o integral', function (array $periods, bool $expected) {
    ($this->periods)($periods);

    expect($this->service->hasStudentsPartials(7))->toBe($expected);
})->with([
    'sem alunos' => [[], false],
    'só integral' => [[null, null], false],
    'só matutino' => [[1], true],
    'integral e vespertino' => [[null, 2], true],
]);
