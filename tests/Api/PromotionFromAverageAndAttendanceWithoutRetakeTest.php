<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiFakeDataTestTrait::class, DiarioApiRequestTestTrait::class);

beforeEach(function () {
    $this->enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
});

test('aprove after all score and absence posted', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 8,
        2 => 8,
        3 => 8,
        4 => 8,
    ];

    $absence = [
        1 => 3,
        2 => 3,
        3 => 3,
        4 => 3,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Aprovado', $response->situacao);
    }

    $registration = $this->enrollment->registration;
    self::assertEquals(1, $registration->refresh()->aprovado);
});

test('failure after all score and absence posted', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 4.3,
        2 => 5.4,
        3 => 6.7,
        4 => 3,
    ];

    $absence = [
        1 => 4,
        2 => 5,
        3 => 1,
        4 => 0,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Retido', $response->situacao);
    }

    $registration = $this->enrollment->registration;
    self::assertEquals(2, $registration->refresh()->aprovado);
});

test('failure for non attendance after all score and absence posted', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 9.1,
        2 => 7.8,
        3 => 6.7,
        4 => 6.9,
    ];

    $absence = [
        1 => 27,
        2 => 58,
        3 => 32,
        4 => 29,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Reprovado por faltas', $response->situacao);
    }

    $registration = $this->enrollment->registration;
    self::assertEquals(14, $registration->refresh()->aprovado);
});

test('returns to studying after remove score in last stage', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 9.7,
        2 => 10,
        3 => 7,
        4 => 9,
    ];

    $absence = [
        1 => 7,
        2 => 3,
        3 => 1,
        4 => 0,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Aprovado', $response->situacao);
    }

    $registration = $this->enrollment->registration;

    self::assertEquals(1, $registration->refresh()->aprovado);

    $randomDiscipline = $schoolClass->disciplines->random()->id;
    $response = $this->deleteScore($this->enrollment, $randomDiscipline, 4);
    self::assertEquals('Cursando', $response->situacao);

    self::assertEquals(3, $registration->refresh()->aprovado);
});

test('remove score when not last stage', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 9.7,
        2 => 10,
        3 => 7,
        4 => 9,
    ];

    $absence = [
        1 => 7,
        2 => 3,
        3 => 1,
        4 => 0,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Aprovado', $response->situacao);
    }

    $registration = $this->enrollment->registration;

    self::assertEquals(1, $registration->refresh()->aprovado);

    $randomDiscipline = $schoolClass->disciplines->random()->id;
    $response = $this->deleteScore($this->enrollment, $randomDiscipline, 2);
    self::assertTrue($response->any_error_msg);

    self::assertEquals(1, $registration->refresh()->aprovado);
});

test('returns to studying after remove absence in last stage', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 9.7,
        2 => 10,
        3 => 7,
        4 => 9,
    ];

    $absence = [
        1 => 7,
        2 => 3,
        3 => 1,
        4 => 0,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Aprovado', $response->situacao);
    }

    $registration = $this->enrollment->registration;

    self::assertEquals(1, $registration->refresh()->aprovado);

    $randomDiscipline = $schoolClass->disciplines->random()->id;
    $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 4);
    self::assertEquals('Cursando', $response->situacao);

    self::assertEquals(3, $registration->refresh()->aprovado);
});

test('remove absence when not is last stage', function () {
    $schoolClass = $this->enrollment->schoolClass;
    $school = $schoolClass->school;

    $this->createStages($school, 4);
    $this->createDisciplines($schoolClass, 2);

    $disciplines = $schoolClass->disciplines;

    $score = [
        1 => 9.7,
        2 => 10,
        3 => 7,
        4 => 9,
    ];

    $absence = [
        1 => 7,
        2 => 3,
        3 => 1,
        4 => 0,
    ];

    foreach ($disciplines as $discipline) {
        $this->postAbsenceForStages($absence, $discipline);
        $response = $this->postScoreForStages($score, $discipline);

        self::assertEquals('Aprovado', $response->situacao);
    }

    $registration = $this->enrollment->registration;

    self::assertEquals(1, $registration->refresh()->aprovado);

    $randomDiscipline = $schoolClass->disciplines->random()->id;
    $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 2);
    self::assertTrue($response->any_error_msg);

    self::assertEquals(1, $registration->refresh()->aprovado);
});
