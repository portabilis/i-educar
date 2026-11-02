<?php

use App\Models\Educacenso\Registro10;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use Tests\TestCase;

uses(TestCase::class);

test('rede local converte cada opcao para o codigo do inep', function () {
    $registro = new Registro10;

    $registro->redeLocal = [RedeLocal::NENHUMA];
    $this->assertSame(0, $registro->redeLocal());

    $registro->redeLocal = [RedeLocal::A_CABO];
    $this->assertSame(1, $registro->redeLocal());

    $registro->redeLocal = [RedeLocal::WIRELESS];
    $this->assertSame(2, $registro->redeLocal());

    $registro->redeLocal = [RedeLocal::A_CABO_E_WIRELESS];
    $this->assertSame(3, $registro->redeLocal());
});

test('rede local sem selecao retorna null', function () {
    $registro = new Registro10;
    $registro->redeLocal = [];

    $this->assertNull($registro->redeLocal());
});
