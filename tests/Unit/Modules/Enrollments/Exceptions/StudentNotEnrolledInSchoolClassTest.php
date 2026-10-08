<?php

namespace Tests\Unit\Modules\Enrollments\Exceptions;

use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;
use iEducar\Support\Exceptions\Error;
use Tests\TestCase;

class StudentNotEnrolledInSchoolClassTest extends TestCase
{
    public function test_carrega_a_mensagem_o_codigo_e_a_enturmacao(): void
    {
        $exception = new StudentNotEnrolledInSchoolClass(42);

        $this->assertSame('Aluno não enturmado.', $exception->getMessage());
        $this->assertSame(Error::STUDENT_NOT_ENROLLED_IN_SCHOOL_CLASS, $exception->getCode());
        $this->assertSame(['enrollment_code' => 42], $exception->getExtraInfo());
    }
}
