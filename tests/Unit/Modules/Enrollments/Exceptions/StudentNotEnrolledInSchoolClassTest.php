<?php

use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;
use iEducar\Support\Exceptions\Error;
use Tests\TestCase;

uses(TestCase::class);

test('carrega a mensagem o codigo e a enturmacao', function () {
    $exception = new StudentNotEnrolledInSchoolClass(42);

    $this->assertSame('Aluno não enturmado.', $exception->getMessage());
    $this->assertSame(Error::STUDENT_NOT_ENROLLED_IN_SCHOOL_CLASS, $exception->getCode());
    $this->assertSame(['enrollment_code' => 42], $exception->getExtraInfo());
});
