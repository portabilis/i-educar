<?php

use App\Models\Educacenso\Registro30;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use Tests\TestCase;

uses(TestCase::class);

test('duas deficiencias da lista formam deficiencia multipla', function () {
    $pessoa = new Registro30;
    $pessoa->arrayDeficiencias = '{' . Deficiencias::CEGUEIRA . ',' . Deficiencias::SURDEZ . '}';
    $pessoa->deficienciaCegueira = 1;
    $pessoa->deficienciaSurdez = 1;

    $this->assertSame(1, $pessoa->deficienciaMultipla());

    $pessoa->deficienciaSurdez = 0;

    $this->assertSame(0, $pessoa->deficienciaMultipla());
});
