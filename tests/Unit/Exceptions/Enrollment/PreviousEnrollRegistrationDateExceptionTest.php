<?php

use App\Exceptions\Enrollment\PreviousEnrollRegistrationDateException;
use App\Models\LegacyRegistration;
use Tests\TestCase;

uses(TestCase::class);

test('message compares the enrollment date with the registration date', function () {
    $registration = new LegacyRegistration;
    $registration->data_matricula = '2030-02-10';

    $this->assertSame(
        'A data de enturmação 01/02/2030 é anterior a data da matrícula 10/02/2030.',
        (new PreviousEnrollRegistrationDateException(new DateTime('2030-02-01'), $registration))->getMessage()
    );
});
