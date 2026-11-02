<?php

use App\Models\LegacySchoolClass;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->keys = fn ($query) => $query->pluck('cod_turma')->all();
});

test('filtra por situação, turno e dia da semana, e ordena pelo nome', function () {
    $beta = LegacySchoolClassFactory::new()->morning()->create(['nm_turma' => 'Beta', 'ativo' => 1, 'dias_semana' => [2, 3]]);
    $alfa = LegacySchoolClassFactory::new()->morning()->create(['nm_turma' => 'Alfa', 'ativo' => 1, 'dias_semana' => [6]]);
    $inactive = LegacySchoolClassFactory::new()->morning()->create(['nm_turma' => 'Gama', 'ativo' => 0, 'dias_semana' => [2]]);
    $afternoon = LegacySchoolClassFactory::new()->afternoon()->create(['nm_turma' => 'Delta', 'ativo' => 1, 'dias_semana' => [2], 'turma_turno_id' => LegacyPeriodFactory::new()->afternoon()]);

    $keys = fn ($query) => ($this->keys)($query->whereKey([$beta->getKey(), $alfa->getKey(), $inactive->getKey(), $afternoon->getKey()]));

    expect($keys(LegacySchoolClass::query()->whereActive(1)->orderByName()))->toBe([$alfa->getKey(), $beta->getKey(), $afternoon->getKey()])
        ->and($keys(LegacySchoolClass::query()->whereActive(0)))->toBe([$inactive->getKey()])
        ->and($keys(LegacySchoolClass::query()->wherePeriod($afternoon->turma_turno_id)))->toBe([$afternoon->getKey()])
        ->and($keys(LegacySchoolClass::query()->whereDayWeek('6')))->toBe([$alfa->getKey()])
        ->and($keys(LegacySchoolClass::query()->whereActive(1)->orderByName('desc')))->toBe([$afternoon->getKey(), $beta->getKey(), $alfa->getKey()]);
});

test('filtra pelas turmas cujo ano letivo da escola está em andamento', function () {
    $inProgress = LegacySchoolClassFactory::new()->create(['ano' => 2030]);
    LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $inProgress->ref_ref_cod_escola, 'ano' => 2030, 'andamento' => 1]);
    $finished = LegacySchoolClassFactory::new()->create(['ano' => 2029]);
    LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $finished->ref_ref_cod_escola, 'ano' => 2029, 'andamento' => 2]);

    $keys = fn ($query) => ($this->keys)($query->whereKey([$inProgress->getKey(), $finished->getKey()]));

    expect($keys(LegacySchoolClass::query()->whereInProgress()))->toBe([$inProgress->getKey()])
        ->and($keys(LegacySchoolClass::query()->whereInProgressYear(2030)))->toBe([$inProgress->getKey()])
        ->and($keys(LegacySchoolClass::query()->whereInProgressYear(2029)))->toBe([]);
});

test('só considera com alunos diferenciados a turma com matrícula ativa de aluno com deficiência', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    expect(LegacySchoolClass::query()->whereDifferentStudents($schoolClass->getKey())->exists())->toBeFalse();
});

test('o recurso para os selects traz as turmas ativas da instituição com id e nome, em ordem', function () {
    $beta = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Beta', 'ano' => 2030]);
    $alfa = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Alfa', 'ano' => 2030, 'ref_cod_instituicao' => $beta->ref_cod_instituicao]);
    LegacySchoolClassFactory::new()->create(['nm_turma' => 'Gama', 'ativo' => 0, 'ref_cod_instituicao' => $beta->ref_cod_instituicao]);

    $resource = LegacySchoolClass::query()->getResource(['institution' => $beta->ref_cod_instituicao, 'year' => 1999]);

    expect($resource->pluck('name', 'id')->all())->toBe([$alfa->getKey() => 'Alfa (2030)', $beta->getKey() => 'Beta (2030)']);
});
