<?php

namespace App\Exceptions\Registration;

class MissingEvaluationRuleForCurrentYear extends RegistrationException
{
    public function __construct()
    {
        $message = 'A série não possui regra de avaliação para este ano letivo.';

        parent::__construct($message);
    }
}
