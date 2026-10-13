<?php

use App\Models\RegistrationStatus;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;
use Tests\TestCase;

uses(TestCase::class);

test('short values keep only the common statuses plus the two groups', function () {
    $values = EnrollmentStatusFilter::getShortValues();

    $this->assertSame('Todas', $values[10]);
    $this->assertSame('Exceto Transferidos/Deixou de Frequentar', $values[9]);
    $this->assertSame('Reprovado', $values[RegistrationStatus::REPROVED]);
    $this->assertTrue($values->has([RegistrationStatus::APPROVED, RegistrationStatus::ONGOING, RegistrationStatus::TRANSFERRED, RegistrationStatus::RECLASSIFIED, RegistrationStatus::ABANDONED]));
    $this->assertFalse($values->has(RegistrationStatus::IN_EXAM));
    $this->assertFalse($values->has(RegistrationStatus::APPROVED_WITH_DEPENDENCY));
    $this->assertFalse($values->has(RegistrationStatus::APPROVED_BY_BOARD));
    $this->assertFalse($values->has(RegistrationStatus::DECEASED));
});

test('short values with dependency and board add those two statuses', function () {
    $values = EnrollmentStatusFilter::getShortWithDependecyAndBoardValues();

    $this->assertSame('Todas', $values[10]);
    $this->assertTrue($values->has([RegistrationStatus::APPROVED_WITH_DEPENDENCY, RegistrationStatus::APPROVED_BY_BOARD]));
    $this->assertFalse($values->has(RegistrationStatus::IN_EXAM));
    $this->assertFalse($values->has(RegistrationStatus::DECEASED));
});

test('short values with exam and dependency add the exam status but not the board', function () {
    $values = EnrollmentStatusFilter::getShortWithExamAndDependecyValues();

    $this->assertSame('Exceto Transferidos/Deixou de Frequentar', $values[9]);
    $this->assertTrue($values->has([RegistrationStatus::IN_EXAM, RegistrationStatus::APPROVED_WITH_DEPENDENCY]));
    $this->assertFalse($values->has(RegistrationStatus::APPROVED_BY_BOARD));
    $this->assertFalse($values->has(RegistrationStatus::PRE_REGISTRATION));
});
