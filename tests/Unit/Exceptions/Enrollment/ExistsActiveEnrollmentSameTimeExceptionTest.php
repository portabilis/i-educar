<?php

use App\Exceptions\Enrollment\ExistsActiveEnrollmentSameTimeException;
use App\Models\LegacyRegistration;
use Tests\TestCase;

uses(TestCase::class);

test('message names the registration', function () {
    $registration = new LegacyRegistration;
    $registration->cod_matricula = 42;

    $this->assertSame(
        'A matrícula 42 já está enturmada em uma turma com esse horário.',
        (new ExistsActiveEnrollmentSameTimeException($registration))->getMessage()
    );
});
