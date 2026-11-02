<?php

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyRegistration;
use App\Services\Exemption\ExemptionService;
use App\User;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey());

    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 2);
    $this->createDisciplines($this->schoolClass, 2);

    [$this->arts, $this->botany] = $this->schoolClass->disciplines()->orderBy('id')->get()->all();

    $this->type = LegacyExemptionTypeFactory::new()->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao]);
    $this->service = new ExemptionService($this->user);

    $this->exemption = fn ($discipline) => LegacyDisciplineExemptionFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'ref_cod_disciplina' => $discipline->getKey(),
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_cod_tipo_dispensa' => $this->type,
    ]);
});

test('ao dispensar de novo apaga as notas e faltas do componente na etapa, a menos que peça para mantê-las', function () {
    $scores = LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $this->registration]);
    $absences = LegacyStudentAbsenceFactory::new()->discipline()->create(['matricula_id' => $this->registration]);
    $score = fn ($stage) => LegacyDisciplineScoreFactory::new()->create(['nota_aluno_id' => $scores, 'componente_curricular_id' => $this->arts->getKey(), 'etapa' => $stage]);
    $absence = fn ($stage) => LegacyDisciplineAbsenceFactory::new()->create(['falta_aluno_id' => $absences, 'componente_curricular_id' => $this->arts->getKey(), 'etapa' => $stage]);

    $score(1);
    $absence(1);
    ($this->exemption)($this->arts);

    $this->service->createExemption($this->registration, $this->arts->getKey(), $this->type->getKey(), 'Atestado', ['1', '2']);

    expect(LegacyDisciplineScore::query()->where('nota_aluno_id', $scores->getKey())->exists())->toBeFalse()
        ->and(LegacyDisciplineAbsence::query()->where('falta_aluno_id', $absences->getKey())->exists())->toBeFalse();

    $score(2);
    $absence(2);
    $this->service->keepScores = true;
    $this->service->keepAbsences = true;
    $this->service->createExemption($this->registration, $this->arts->getKey(), $this->type->getKey(), 'Atestado', ['2']);

    expect(LegacyDisciplineScore::query()->where('nota_aluno_id', $scores->getKey())->exists())->toBeTrue()
        ->and(LegacyDisciplineAbsence::query()->where('falta_aluno_id', $absences->getKey())->exists())->toBeTrue();
});

test('não grava, não altera e não apaga dispensa sem os dados completos', function () {
    $orphan = new LegacyDisciplineExemption(['ref_cod_disciplina' => null]);
    $ghost = new LegacyRegistration([
        'cod_matricula' => 0,
        'ref_ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
    ]);

    expect($this->service->updateExemption($orphan, $this->registration, $this->type->getKey(), 'Obs'))->toBeFalse()
        ->and($this->service->deleteExemption($orphan, $this->registration))->toBeFalse()
        ->and($this->service->findExemptionByRegistration(null, 1, 1, 1))->toBeNull()
        ->and(fn () => $this->service->createExemption($this->registration, $this->botany->getKey(), 0, null, []))->toThrow(Exception::class)
        ->and(fn () => $this->service->createExemption($ghost, $this->botany->getKey(), $this->type->getKey(), null, []))->toThrow(Exception::class)
        ->and(LegacyDisciplineExemption::query()->where('ref_cod_disciplina', $this->botany->getKey())->exists())->toBeFalse();
});
