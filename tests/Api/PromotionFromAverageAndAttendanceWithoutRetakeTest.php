<?php

namespace Tests\Api;

use App\Models\LegacyEnrollment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PromotionFromAverageAndAttendanceWithoutRetakeTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiFakeDataTestTrait;
    use DiarioApiRequestTestTrait;

    /**
     * @var LegacyEnrollment
     */
    private $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    }

    public function test_aprove_after_all_score_and_absence_posted()
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

            self::assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        self::assertEquals(1, $registration->refresh()->aprovado);
    }

    public function test_failure_after_all_score_and_absence_posted()
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

            self::assertEquals('Retido', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        self::assertEquals(2, $registration->refresh()->aprovado);
    }

    public function test_failure_for_non_attendance_after_all_score_and_absence_posted()
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

            self::assertEquals('Reprovado por faltas', $response->situacao);
        }

        $registration = $this->enrollment->registration;
        self::assertEquals(14, $registration->refresh()->aprovado);
    }

    public function test_returns_to_studying_after_remove_score_in_last_stage()
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

            self::assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        self::assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteScore($this->enrollment, $randomDiscipline, 4);
        self::assertEquals('Cursando', $response->situacao);

        self::assertEquals(3, $registration->refresh()->aprovado);
    }

    public function test_remove_score_when_not_last_stage()
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

            self::assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        self::assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteScore($this->enrollment, $randomDiscipline, 2);
        self::assertTrue($response->any_error_msg);

        self::assertEquals(1, $registration->refresh()->aprovado);
    }

    public function test_returns_to_studying_after_remove_absence_in_last_stage()
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

            self::assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        self::assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 4);
        self::assertEquals('Cursando', $response->situacao);

        self::assertEquals(3, $registration->refresh()->aprovado);
    }

    public function test_remove_absence_when_not_is_last_stage()
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

            self::assertEquals('Aprovado', $response->situacao);
        }

        $registration = $this->enrollment->registration;

        self::assertEquals(1, $registration->refresh()->aprovado);

        $randomDiscipline = $schoolClass->disciplines->random()->id;
        $response = $this->deleteAbsence($this->enrollment, $randomDiscipline, 2);
        self::assertTrue($response->any_error_msg);

        self::assertEquals(1, $registration->refresh()->aprovado);
    }
}
