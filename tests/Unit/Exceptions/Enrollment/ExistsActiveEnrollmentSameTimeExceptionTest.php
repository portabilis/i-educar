<?php

namespace Tests\Unit\Exceptions\Enrollment;

use App\Exceptions\Enrollment\ExistsActiveEnrollmentSameTimeException;
use App\Models\LegacyRegistration;
use Tests\TestCase;

class ExistsActiveEnrollmentSameTimeExceptionTest extends TestCase
{
    public function test_message_names_the_registration(): void
    {
        $registration = new LegacyRegistration;
        $registration->cod_matricula = 42;

        $this->assertSame(
            'A matrícula 42 já está enturmada em uma turma com esse horário.',
            (new ExistsActiveEnrollmentSameTimeException($registration))->getMessage()
        );
    }
}
