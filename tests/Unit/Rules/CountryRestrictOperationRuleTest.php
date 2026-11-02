<?php

use App\Models\Country;
use App\Models\District;
use App\Rules\CountryRestrictOperationRule;

test('permite exclusao de qualquer pais quando usuario for poli institucional', function (int $countryId) {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL);

    $country = Mockery::mock(District::class);
    $country->shouldReceive('getOriginal')->with('id')->andReturn($countryId);

    expect($rule->passes('country', $country))->toBeTrue();
})->with([
    'brasil com poli institucional' => Country::BRASIL,
    'outro pais (argentina)' => 2,
    'outro pais (chile)' => 3,
]);

test('permite exclusao de paises estrangeiros para outros niveis de usuario', function (int $level, int $countryId) {
    $rule = new CountryRestrictOperationRule($level);

    $country = Mockery::mock(District::class);
    $country->shouldReceive('getOriginal')->with('id')->andReturn($countryId);

    expect($rule->passes('country', $country))->toBeTrue();
})->with([
    'institucional com pais estrangeiro' => [App_Model_NivelTipoUsuario::INSTITUCIONAL, 2],
    'escola com pais estrangeiro' => [App_Model_NivelTipoUsuario::ESCOLA, 3],
    'biblioteca com pais estrangeiro' => [App_Model_NivelTipoUsuario::BIBLIOTECA, 4],
]);

test('rejeita exclusao do brasil para usuarios nao poli institucionais', function (int $level) {
    $rule = new CountryRestrictOperationRule($level);

    $country = Mockery::mock(District::class);
    $country->shouldReceive('getOriginal')->with('id')->andReturn(Country::BRASIL);

    expect($rule->passes('country', $country))->toBeFalse();
})->with([
    'institucional tentando excluir brasil' => App_Model_NivelTipoUsuario::INSTITUCIONAL,
    'escola tentando excluir brasil' => App_Model_NivelTipoUsuario::ESCOLA,
    'biblioteca tentando excluir brasil' => App_Model_NivelTipoUsuario::BIBLIOTECA,
]);

test('retorna a mensagem de erro correta ao tentar excluir o brasil', function () {
    $rule = new CountryRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    expect($rule->message())->toBe('Não é permitido exclusão do Brasil, pois já está previamente cadastrado.');
});
