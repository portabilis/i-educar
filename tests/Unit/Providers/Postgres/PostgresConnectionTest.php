<?php

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

test('em modo de simulação a consulta do legado não roda', function () {
    $result = null;

    DB::pretend(function ($connection) use (&$result) {
        $result = $connection->publicRun('select 1');
    });

    expect($result)->toBe([]);
});
