<?php

namespace Tests\Unit\Exceptions\Enrollment;

use App\Exceptions\Enrollment\PreviousEnrollRegistrationDateException;
use App\Models\LegacyRegistration;
use DateTime;
use Tests\TestCase;

class PreviousEnrollRegistrationDateExceptionTest extends TestCase
{
    public function test_message_compares_the_enrollment_date_with_the_registration_date(): void
    {
        $registration = new LegacyRegistration;
        $registration->data_matricula = '2030-02-10';

        $this->assertSame(
            'A data de enturmação 01/02/2030 é anterior a data da matrícula 10/02/2030.',
            (new PreviousEnrollRegistrationDateException(new DateTime('2030-02-01'), $registration))->getMessage()
        );
    }
}
