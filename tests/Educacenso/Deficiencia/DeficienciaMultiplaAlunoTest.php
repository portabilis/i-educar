<?php

use iEducar\Modules\Educacenso\Deficiencia\CombinacaoDeficienciaMultipla;
use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaAluno;
use Tests\TestCase;

uses(TestCase::class);

test('lista os pares de deficiencias que formam deficiencia multipla', function () {
    $combinacoes = (new DeficienciaMultiplaAluno)->getCombinacoes();

    $this->assertInstanceOf(CombinacaoDeficienciaMultipla::class, new DeficienciaMultiplaAluno);
    $this->assertCount(14, $combinacoes);
    $this->assertContains(['17', '22'], $combinacoes);
    $this->assertContains(['22', '23'], $combinacoes);
    $this->assertNotContains(['17', '18'], $combinacoes);

    foreach ($combinacoes as $par) {
        $this->assertCount(2, $par);
    }
});
