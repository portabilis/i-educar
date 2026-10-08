<?php

namespace Tests\Unit\Modules\Enrollments\Model;

use App\Models\RegistrationStatus;
use iEducar\Modules\Enrollments\Model\EnrollmentStatusFilter;
use Tests\TestCase;

class EnrollmentStatusFilterTest extends TestCase
{
    public function test_short_values_keep_only_the_common_statuses_plus_the_two_groups(): void
    {
        $values = EnrollmentStatusFilter::getShortValues();

        $this->assertSame('Todas', $values[10]);
        $this->assertSame('Exceto Transferidos/Deixou de Frequentar', $values[9]);
        $this->assertSame('Reprovado', $values[RegistrationStatus::REPROVED]);
        $this->assertTrue($values->has([RegistrationStatus::APPROVED, RegistrationStatus::ONGOING, RegistrationStatus::TRANSFERRED, RegistrationStatus::RECLASSIFIED, RegistrationStatus::ABANDONED]));
        $this->assertFalse($values->has(RegistrationStatus::IN_EXAM));
        $this->assertFalse($values->has(RegistrationStatus::APPROVED_WITH_DEPENDENCY));
        $this->assertFalse($values->has(RegistrationStatus::APPROVED_BY_BOARD));
        $this->assertFalse($values->has(RegistrationStatus::DECEASED));
    }

    public function test_short_values_with_dependency_and_board_add_those_two_statuses(): void
    {
        $values = EnrollmentStatusFilter::getShortWithDependecyAndBoardValues();

        $this->assertSame('Todas', $values[10]);
        $this->assertTrue($values->has([RegistrationStatus::APPROVED_WITH_DEPENDENCY, RegistrationStatus::APPROVED_BY_BOARD]));
        $this->assertFalse($values->has(RegistrationStatus::IN_EXAM));
        $this->assertFalse($values->has(RegistrationStatus::DECEASED));
    }

    public function test_short_values_with_exam_and_dependency_add_the_exam_status_but_not_the_board(): void
    {
        $values = EnrollmentStatusFilter::getShortWithExamAndDependecyValues();

        $this->assertSame('Exceto Transferidos/Deixou de Frequentar', $values[9]);
        $this->assertTrue($values->has([RegistrationStatus::IN_EXAM, RegistrationStatus::APPROVED_WITH_DEPENDENCY]));
        $this->assertFalse($values->has(RegistrationStatus::APPROVED_BY_BOARD));
        $this->assertFalse($values->has(RegistrationStatus::PRE_REGISTRATION));
    }
}
