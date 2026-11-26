<?php

use App\Models\LegacySchoolClass;
use App\Rules\CanCreateSchoolClass;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma A', 'max_aluno' => 2]);
    $this->schoolClass->schoolGrade->update(['bloquear_cadastro_turma_para_serie_com_vagas' => 1]);
    $this->academicYear = LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ano' => $this->schoolClass->ano,
    ]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'ativo' => 1]);

    $this->newSchoolClass = (new LegacySchoolClass)->forceFill([
        'ref_ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'turma_turno_id' => $this->schoolClass->turma_turno_id,
        'ano' => $this->schoolClass->ano,
    ]);
});

test('bloqueia a turma nova enquanto outra turma da série e do turno tem vagas', function () {
    $rule = new CanCreateSchoolClass;

    expect($rule->passes('schoolClass', $this->newSchoolClass))->toBeFalse()
        ->and($rule->message())->toBe("Não é possivel cadastrar turmas, pois ainda existem 1 vagas em aberto na turma 'Turma A' desta serie e turno. Tal limitação ocorre devido definição feita para estae scola e série.");
});

test('permite a turma nova quando a turma existente está lotada', function () {
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'ativo' => 1]);

    expect((new CanCreateSchoolClass)->passes('schoolClass', $this->newSchoolClass))->toBeTrue();
});

test('permite a turma nova quando a série não bloqueia o cadastro', function () {
    $this->schoolClass->schoolGrade->update(['bloquear_cadastro_turma_para_serie_com_vagas' => 0]);

    expect((new CanCreateSchoolClass)->passes('schoolClass', $this->newSchoolClass))->toBeTrue();
});

test('permite a turma nova quando o ano letivo da escola não está em andamento', function () {
    $this->academicYear->update(['andamento' => 0]);

    expect((new CanCreateSchoolClass)->passes('schoolClass', $this->newSchoolClass))->toBeTrue();
});

test('não bloqueia a edição de uma turma existente', function () {
    $this->newSchoolClass->cod_turma = $this->schoolClass->getKey();

    expect((new CanCreateSchoolClass)->passes('schoolClass', $this->newSchoolClass))->toBeTrue();
});
