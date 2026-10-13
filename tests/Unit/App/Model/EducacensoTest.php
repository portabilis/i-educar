<?php

use Tests\TestCase;

uses(TestCase::class);

test('a etapa unificada e a 3', function () {
    $this->assertSame([3], App_Model_Educacenso::etapasEnsinoUnificadas());
});

test('lista as etapas que a turma multisseriada atende', function (int $etapa, array $etapas) {
    $this->assertSame($etapas, App_Model_Educacenso::etapasDaTurma($etapa));
})->with([
    'educação infantil unificada' => [3, [1, 2]],
    'multietapa do fundamental' => [22, [14, 15, 16, 17, 18, 19, 20, 21, 41]],
    'multietapa infantil e fundamental' => [56, [1, 2, 14, 15, 16, 17, 18, 19, 20, 21, 41]],
    'curso técnico concomitante e subsequente' => [64, [30, 40]],
    'EJA fundamental' => [72, [69, 70]],
    'etapa única' => [14, []],
]);
