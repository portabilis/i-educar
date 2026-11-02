<?php

use App\Models\Country;
use App\Models\District;
use App\Rules\StateRestrictOperationRule;

test('CT01: ramo verdadeiro da primeira decisao (POLI_INSTITUCIONAL retorna true)', function () {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL);

    $state = Mockery::mock(District::class);
    $state->shouldReceive('getOriginal')->with('country_id')->andReturn(Country::BRASIL);

    expect($rule->passes('state', $state))->toBeTrue();
});

test('CT02: ramo falso da primeira decisao e verdadeiro da segunda (nao-poli com estado estrangeiro retorna true)', function () {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    $state = Mockery::mock(District::class);
    $state->shouldReceive('getOriginal')->with('country_id')->andReturn(99);

    expect($rule->passes('state', $state))->toBeTrue();
});

test('CT03: ramo falso de ambas as decisoes (nao-poli tentando excluir estado do Brasil retorna false)', function () {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    $state = Mockery::mock(District::class);
    $state->shouldReceive('getOriginal')->with('country_id')->andReturn(Country::BRASIL);

    expect($rule->passes('state', $state))->toBeFalse();
});

test('CT04: fallback para propriedade direta country_id quando getOriginal retorna null', function () {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    $state = new District;
    $state->country_id = Country::BRASIL;

    expect($rule->passes('state', $state))->toBeFalse();
});

test('CT05: executa instrucao do metodo message', function () {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    expect($rule->message())->toBe('Não é permitido exclusão de estados brasileiros, pois já estão previamente cadastrados.');
});
