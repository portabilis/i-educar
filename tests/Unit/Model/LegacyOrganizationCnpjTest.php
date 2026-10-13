<?php

use App\Models\LegacyOrganization;
use App\Models\LegacySchool;
use Tests\TestCase;

uses(TestCase::class);

test('mutator normaliza cnpj ao atribuir', function () {
    $organizacao = new LegacyOrganization;
    $organizacao->cnpj = '12.abc.345/01de-35';

    $this->assertSame('12ABC34501DE35', $organizacao->cnpj);
});

test('mutator converte vazio em null', function () {
    $organizacao = new LegacyOrganization;
    $organizacao->cnpj = '';

    $this->assertNull($organizacao->cnpj);
});

test('mutator da mantenedora normaliza cnpj', function () {
    $escola = new LegacySchool;
    $escola->cnpj_mantenedora_principal = '00.222.333/0001-81';

    $this->assertSame('00222333000181', $escola->cnpj_mantenedora_principal);
});

test('where cnpj filtra por busca parcial normalizada', function () {
    $sql = LegacyOrganization::query()->whereCnpj('12.abc')->toRawSql();

    $this->assertStringContainsString("cnpj LIKE '%12ABC%'", $sql);
});

test('when com valor vazio nao aplica filtro', function () {
    $sql = LegacyOrganization::query()
        ->when(limpaCnpj('./-'), fn ($query, $cnpj) => $query->whereCnpj($cnpj))
        ->toRawSql();

    $this->assertStringNotContainsString('cnpj', $sql);
});
