<?php

use App\Rules\ExistsPeriod;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->rule = new ExistsPeriod;
    $this->schoolClass = fn (int $standardCalendar) => LegacySchoolClassFactory::new()->create([
        'ref_cod_curso' => LegacyCourseFactory::new()->create(['padrao_ano_escolar' => $standardCalendar]),
    ]);
});

test('a turma de um curso com calendário próprio precisa de etapas', function () {
    $schoolClass = ($this->schoolClass)(0);

    expect($this->rule->passes('schoolClass', $schoolClass))->toBeFalse()
        ->and($this->rule->message())->toBe('Etapas não informadas.');

    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass]);

    expect($this->rule->passes('schoolClass', $schoolClass->refresh()))->toBeTrue();
});

test('a turma de um curso que segue o calendário da escola passa sem etapas', function () {
    expect($this->rule->passes('schoolClass', ($this->schoolClass)(1)))->toBeTrue();
});
