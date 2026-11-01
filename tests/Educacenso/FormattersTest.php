<?php

use iEducar\Modules\Educacenso\Formatters;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->formatter = function (): object {
        return new class
        {
            use Formatters;
        };
    };
});

test('texto alfabetico fica sem acentos numeros e espacos repetidos', function () {
    $this->assertSame('JOAO DA SILVA', ($this->formatter)()->convertStringToAlpha('  João 2 da   Silva! '));
});

test('texto do censo mantem numeros ordinais e hifen', function () {
    $this->assertSame('RUA 7 - 1ª QUADRA', ($this->formatter)()->convertStringToCenso(' Rua 7 -   1ª quadra; '));
});

test('certidao no formato novo mantem so numeros e x', function () {
    $this->assertSame('1234X56', ($this->formatter)()->convertStringToCertNovoFormato('12.34-x/56'));
});

test('email mantem os caracteres validos', function () {
    $this->assertSame('ANA.ALVES-1@ESCOLA.BR', ($this->formatter)()->convertEmailToCenso('ána.alves-1@escola.br '));
});
