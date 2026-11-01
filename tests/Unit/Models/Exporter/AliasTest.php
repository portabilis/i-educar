<?php

use App\Models\Exporter\SocialAssistance;
use App\Models\Exporter\Stage;

test('os exportadores traduzem a coluna para o rótulo, ou a devolvem como veio', function () {
    expect((new Stage)->alias('school_name'))->toBe('Escola')
        ->and((new Stage)->alias('coluna_inexistente'))->toBe('coluna_inexistente')
        ->and((new SocialAssistance)->alias('coluna_inexistente'))->toBe('coluna_inexistente');
});
