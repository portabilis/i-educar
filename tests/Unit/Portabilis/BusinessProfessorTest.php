<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->scenario = EnrollmentScenario::for(LegacyUserFactory::new()->admin()->create(), 2030);
    $this->institution = $this->scenario->institution;
    $this->school = $this->scenario->school->getKey();
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma do Professor', 'turma_turno_id' => $this->scenario->period(1)->getKey()]);
    $this->math = $this->scenario->discipline('Matemática');

    $this->teacher = EmployeeScenario::named('Bia Professora', $this->institution);
    $this->teacher->allocation($this->scenario->school, ['periodo' => 1, 'ano' => 2030]);
    $this->teacher->discipline($this->teacher->role('Professora', 'bia'), $this->math, $this->scenario->course);
    $this->id = $this->teacher->employee->getKey();

    $this->exigeVinculo = fn (bool $exige) => LegacyInstitution::query()->whereKey($this->institution)->update(['exigir_vinculo_turma_professor' => $exige ? 1 : 0]);
});

test('reconhece quem é professor e quem só é professor, na instituição ou em qualquer uma', function () {
    expect(Portabilis_Business_Professor::isProfessor($this->institution, $this->id))->toBeTrue()
        ->and(Portabilis_Business_Professor::isProfessor(null, $this->id))->toBeTrue()
        ->and((bool) Portabilis_Business_Professor::isOnlyProfessor($this->institution, $this->id))->toBeTrue()
        ->and((bool) Portabilis_Business_Professor::isOnlyProfessor(null, $this->id))->toBeTrue();

    $this->teacher->role('Secretária', 'bia-sec', teacher: false);

    expect(Portabilis_Business_Professor::isOnlyProfessor($this->institution, $this->id))->toBeFalse();
});

test('sem exigir vínculo com a turma, os cursos, turmas e componentes vêm da alocação e da habilitação', function () {
    ($this->exigeVinculo)(false);

    expect(Portabilis_Business_Professor::cursosAlocado($this->institution, $this->school, $this->id))->toBeArray()
        ->and(Portabilis_Business_Professor::canLoadSeriesAlocado($this->institution))->toBeFalse()
        ->and(Portabilis_Business_Professor::seriesAlocado($this->institution, $this->school, $this->scenario->course->getKey(), $this->id))->toBeNull()
        ->and(collect(Portabilis_Business_Professor::turmasAlocado($this->institution, $this->school, $this->scenario->grade->getKey(), $this->id))->pluck('nome')->all())->toBe(['Turma do Professor'])
        ->and(collect(Portabilis_Business_Professor::componentesCurricularesAlocado($this->institution, $this->schoolClass->getKey(), 2030, $this->id))->pluck('nome')->all())->toBe(['Matemática']);
});
