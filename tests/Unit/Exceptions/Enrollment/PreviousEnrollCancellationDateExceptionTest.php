<?php

namespace Tests\Unit\Exceptions\Enrollment;

use App\Exceptions\Enrollment\PreviousEnrollCancellationDateException;
use App\Models\LegacyRegistration;
use DateTime;
use Tests\TestCase;

class PreviousEnrollCancellationDateExceptionTest extends TestCase
{
    public function test_message_compares_the_exit_date_with_the_registration_date(): void
    {
        $registration = new LegacyRegistration;
        $registration->data_matricula = '2030-02-10';

        $this->assertSame(
            'A data de saída 01/02/2030 deve ser maior que a data de matrícula 10/02/2030.',
            (new PreviousEnrollCancellationDateException($registration, new DateTime('2030-02-01')))->getMessage()
        );
    }
}
