<?php

use App\Exceptions\Enrollment\PreviousEnrollCancellationDateException;
use App\Models\LegacyRegistration;
use Tests\TestCase;

uses(TestCase::class);

test('message compares the exit date with the registration date', function () {
    $registration = new LegacyRegistration;
    $registration->data_matricula = '2030-02-10';

    $this->assertSame(
        'A data de saída 01/02/2030 deve ser maior que a data de matrícula 10/02/2030.',
        (new PreviousEnrollCancellationDateException($registration, new DateTime('2030-02-01')))->getMessage()
    );
});
