<?php

use App\Exceptions\Registration\MissingEvaluationRuleForCurrentYear;
use App\Exceptions\Registration\RegistrationException;
use Tests\TestCase;

uses(TestCase::class);

test('is a registration exception with a fixed message', function () {
    $exception = new MissingEvaluationRuleForCurrentYear;

    $this->assertInstanceOf(RegistrationException::class, $exception);
    $this->assertSame('A série não possui regra de avaliação para este ano letivo.', $exception->getMessage());
});
