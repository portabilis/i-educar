<?php

use App\Models\Country;
use App\Models\District;
use App\Rules\CountryRestrictOperationRule;

test('CT01: ramo verdadeiro da primeira decisao (POLI_INSTITUCIONAL retorna true)', function () {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL);

    $country = Mockery::mock(District::class);
    $country->shouldReceive('getOriginal')->with('id')->andReturn(Country::BRASIL);

    expect($rule->passes('country', $country))->toBeTrue();
});

test('CT02: ramo falso da primeira decisao e verdadeiro da segunda (nao-poli com pais estrangeiro retorna true)', function () {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    $country = Mockery::mock(District::class);
    $country->shouldReceive('getOriginal')->with('id')->andReturn(99);

    expect($rule->passes('country', $country))->toBeTrue();
});

test('CT03: ramo falso de ambas as decisoes (nao-poli tentando excluir Brasil retorna false)', function () {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    $country = Mockery::mock(District::class);
    $country->shouldReceive('getOriginal')->with('id')->andReturn(Country::BRASIL);

    expect($rule->passes('country', $country))->toBeFalse();
});

test('CT04: fallback para propriedade direta id quando getOriginal retorna null', function () {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    $country = new District;
    $country->id = Country::BRASIL;

    expect($rule->passes('country', $country))->toBeFalse();
});

test('CT05: executa instrucao do metodo message', function () {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    expect($rule->message())->toBe('Não é permitido exclusão do Brasil, pois já está previamente cadastrado.');
});
