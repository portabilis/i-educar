<?php

use Database\Factories\CityFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('pesquisa municípios pelo nome', function () {
    $city = CityFactory::new()->create(['name' => 'Município Alfa']);

    $this->get('/intranet/educar_pesquisa_municipio_lst.php?busca=S&nome=' . urlencode('Município Alfa') . '&sigla_uf=' . $city->state->abbreviation)
        ->assertOk()
        ->assertSee('Município Alfa');
});
