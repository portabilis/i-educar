<?php

use App\Models\Educacenso\Registro60;
use iEducar\Modules\Educacenso\ExportRule\PoderPublicoResponsavelTransporte;
use Tests\TestCase;

uses(TestCase::class);

test('limpa o poder publico quando nao usa transporte publico', function () {
    $registro = new Registro60;
    $registro->transportePublico = 0;
    $registro->poderPublicoResponsavelTransporte = 1;

    $this->assertNull(PoderPublicoResponsavelTransporte::handle($registro)->poderPublicoResponsavelTransporte);
});

test('mantem o poder publico quando usa transporte publico', function () {
    $registro = new Registro60;
    $registro->transportePublico = 1;
    $registro->poderPublicoResponsavelTransporte = 2;

    $this->assertSame(2, PoderPublicoResponsavelTransporte::handle($registro)->poderPublicoResponsavelTransporte);
});
