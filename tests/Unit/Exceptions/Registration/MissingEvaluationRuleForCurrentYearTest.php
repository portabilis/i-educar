<?php

namespace Tests\Unit\Exceptions\Registration;

use App\Exceptions\Registration\MissingEvaluationRuleForCurrentYear;
use App\Exceptions\Registration\RegistrationException;
use Tests\TestCase;

class MissingEvaluationRuleForCurrentYearTest extends TestCase
{
    public function test_is_a_registration_exception_with_a_fixed_message(): void
    {
        $exception = new MissingEvaluationRuleForCurrentYear;

        $this->assertInstanceOf(RegistrationException::class, $exception);
        $this->assertSame('A série não possui regra de avaliação para este ano letivo.', $exception->getMessage());
    }
}
