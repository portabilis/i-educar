<?php

namespace Tests\Unit\Rules;

use App\Rules\ExistsEnrollmentsInSchoolClassGrades;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExistsEnrollmentsInSchoolClassGradesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_refuses_to_remove_a_grade_that_still_has_enrollments_in_the_class(): void
    {
        $schoolClass = LegacySchoolClassFactory::new()->create(['ano' => 2030]);
        $grade = LegacyGradeFactory::new()->create(['nm_serie' => 'Segundo Ano']);
        $otherGrade = LegacyGradeFactory::new()->create(['nm_serie' => 'Terceiro Ano']);
        LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => LegacyRegistrationFactory::new()->create(['ano' => 2030, 'ref_ref_cod_serie' => $grade]),
            'ativo' => 1,
        ]);

        $rule = new ExistsEnrollmentsInSchoolClassGrades;

        $this->assertFalse($rule->passes('grades', ['turma' => $schoolClass, 'grades_delete' => [$grade->getKey(), $otherGrade->getKey()]]));
        $this->assertSame('Não é possível remover a(s) série(s): Segundo Ano, pois existem matrículas vinculadas.', $rule->message());

        $this->assertTrue($rule->passes('grades', ['turma' => $schoolClass, 'grades_delete' => [$otherGrade->getKey()]]));
    }
}
