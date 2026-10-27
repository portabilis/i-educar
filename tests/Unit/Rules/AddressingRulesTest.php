<?php

use App\Rules\Addressing\AddressingCityDistrictRule;
use App\Rules\Addressing\AddressingCityPlaceRule;
use App\Rules\Addressing\AddressingCountryRule;
use App\Rules\Addressing\AddressingStateRule;

test('as regras de endereço explicam por que não dá para excluir', function (string $rule, string $message) {
    expect((new $rule)->message())->toBe($message);
})->with([
    [AddressingCityDistrictRule::class, 'Não é possível excluir a cidade, pois possui bairros vinculados!'],
    [AddressingCityPlaceRule::class, 'Não é possível excluir a cidade, pois possui endereços vinculados!'],
    [AddressingCountryRule::class, 'Não é possível excluir o país, pois possui estados vinculados!'],
    [AddressingStateRule::class, 'Não é possível excluir o estado, pois possui cidades vinculadas!'],
]);
