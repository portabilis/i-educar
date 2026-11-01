<?php

use iEducar\Modules\EvaluationRules\Exceptions\EvaluationRuleNotDefinedInLevel;

test('informa a série sem regra de avaliação', function () {
    $exception = new EvaluationRuleNotDefinedInLevel(42);

    expect($exception->getMessage())->toBe('Regra de avaliação não informada na série para o ano letivo informado.')
        ->and($exception->getExtraInfo())->toBe(['level_code' => 42]);
});
