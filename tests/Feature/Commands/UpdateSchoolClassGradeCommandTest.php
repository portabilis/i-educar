<?php

use App\Exceptions\Console\MissingSchoolCourseException;
use App\Exceptions\Console\MissingSchoolGradeException;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->scenario = EnrollmentScenario::for(LegacyUserFactory::new()->admin()->create(), 2030);
    $this->schoolClass = $this->scenario->schoolClass();
    $this->enrollment = $this->scenario->enrolled('Bia Borges', $this->schoolClass);
});

test('troca a série da turma e das matrículas enturmadas', function () {
    $second = $this->scenario->grade('Segundo Ano');

    $this->artisan('update:school-class-grade', ['schoolclass' => $this->schoolClass->getKey(), 'grade' => $second->getKey()])
        ->assertExitCode(0);

    expect($this->schoolClass->refresh()->ref_ref_cod_serie)->toBe($second->getKey())
        ->and($this->schoolClass->ref_cod_curso)->toBe($this->scenario->course->getKey())
        ->and($this->enrollment->registration->refresh()->ref_ref_cod_serie)->toBe($second->getKey());
});

test('exige que a escola ofereça a série', function () {
    $grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->scenario->course]);

    $this->artisan('update:school-class-grade', ['schoolclass' => $this->schoolClass->getKey(), 'grade' => $grade->getKey()]);
})->throws(MissingSchoolGradeException::class, 'Não existe registro em escola série para escola');

test('exige que a escola ofereça o curso da série', function () {
    $grade = LegacyGradeFactory::new()->create();
    LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $this->scenario->school, 'ref_cod_serie' => $grade]);

    $this->artisan('update:school-class-grade', ['schoolclass' => $this->schoolClass->getKey(), 'grade' => $grade->getKey()]);
})->throws(MissingSchoolCourseException::class, 'Não existe registro em escola curso para escola');
