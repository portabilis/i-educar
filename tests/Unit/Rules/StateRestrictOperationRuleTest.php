<?php

use App\Models\Country;
use App\Models\District;
use App\Rules\StateRestrictOperationRule;

test('permite exclusao de qualquer estado quando usuario for poli institucional', function (int $countryId) {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL);

    $state = Mockery::mock(District::class);
    $state->shouldReceive('getOriginal')->with('country_id')->andReturn($countryId);

    expect($rule->passes('state', $state))->toBeTrue();
})->with([
    'estado brasileiro com poli institucional' => Country::BRASIL,
    'estado estrangeiro' => 2,
]);

test('permite exclusao de estados estrangeiros para outros niveis de usuario', function (int $level, int $countryId) {
    $rule = new StateRestrictOperationRule($level);

    $state = Mockery::mock(District::class);
    $state->shouldReceive('getOriginal')->with('country_id')->andReturn($countryId);

    expect($rule->passes('state', $state))->toBeTrue();
})->with([
    'institucional com estado de outro pais' => [App_Model_NivelTipoUsuario::INSTITUCIONAL, 2],
    'escola com estado de outro pais' => [App_Model_NivelTipoUsuario::ESCOLA, 3],
]);

test('rejeita exclusao de estados brasileiros para usuarios nao poli institucionais', function (int $level) {
    $rule = new StateRestrictOperationRule($level);

    $state = Mockery::mock(District::class);
    $state->shouldReceive('getOriginal')->with('country_id')->andReturn(Country::BRASIL);

    expect($rule->passes('state', $state))->toBeFalse();
})->with([
    'institucional tentando excluir estado brasileiro' => App_Model_NivelTipoUsuario::INSTITUCIONAL,
    'escola tentando excluir estado brasileiro' => App_Model_NivelTipoUsuario::ESCOLA,
]);

test('retorna a mensagem de erro correta ao tentar excluir estado brasileiro', function () {
    $rule = new StateRestrictOperationRule(App_Model_NivelTipoUsuario::INSTITUCIONAL);

    expect($rule->message())->toBe('Não é permitido exclusão de estados brasileiros, pois já estão previamente cadastrados.');
});
