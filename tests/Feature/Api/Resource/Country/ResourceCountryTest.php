<?php

use Database\Factories\CountryFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('lista os países ativos como opções de seleção, filtrando pelo nome', function () {
    $country = CountryFactory::new()->create(['name' => 'Terra de Teste']);

    $this->getJson(route('api.resource.country', ['name' => 'Terra de Teste']))
        ->assertOk()
        ->assertJsonFragment(['value' => $country->getKey(), 'label' => 'Terra de Teste']);
});
