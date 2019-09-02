<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEnrollment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PromotionFromAverageAndAttendanceWithoutRetake extends TestCase
{
    use DiarioApiFakeDataTestTrait, DiarioApiRequestTestTrait, DatabaseTransactions;

    /**
     * @var LegacyEnrollment
     */
    private $enrollment;

    public function setUp(): void
    {
        parent::setUp();
        $this->enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    }

    public function testAproveAfterAllScoreAndAbsencePosted()
    {
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

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }

    public function testFailureAfterAllScoreAndAbsencePosted()
    {
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

            $this->assertEquals('Retido', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(2, $registration->refresh()->aprovado);
    }

    public function testFailureForNonAttendanceAfterAllScoreAndAbsencePosted()
    {
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

            $this->assertEquals('Retido', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        $this->assertEquals(14, $registration->refresh()->aprovado);
    }

    public function testReturnsToStudyingAfterRemoveScoreInLastStage()
    {
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

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        $this->assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteScore($this->enrollment, $randomDiscipline, 4);
        $this->assertEquals('Cursando', $response->situacao);
    
        $this->assertEquals(3, $registration->refresh()->aprovado);
    }

    public function testRemoveScoreWhenNotLastStage()
    {
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

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        $this->assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteScore($this->enrollment, $randomDiscipline, 2);
        $this->assertTrue($response->any_error_msg);
    
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }

    public function testReturnsToStudyingAfterRemoveAbsenceInLastStage()
    {
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

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        $this->assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 4);
        $this->assertEquals('Cursando', $response->situacao);
    
        $this->assertEquals(3, $registration->refresh()->aprovado);
    }

    public function testRemoveAbsenceWhenNotIsLastStage()
    {
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

            $this->assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        $this->assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 2);
        $this->assertTrue($response->any_error_msg);
    
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }
}
