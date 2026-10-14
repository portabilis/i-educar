<?php

use Database\Factories\CountryFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $this->country = CountryFactory::new()->create();
    $this->xml = fn (array $query) => $this->get('/intranet/public_uf_xml.php?' . http_build_query($query))->assertOk();
});

test('lista os estados do país pelo id', function () {
    $state = StateFactory::new()->create(['country_id' => $this->country, 'name' => 'Estado Alfa']);

    ($this->xml)(['pais' => $this->country->getKey()])
        ->assertSee('<estado id="' . $state->getKey() . '">Estado Alfa</estado>', false);
});

test('lista os estados do país pela sigla', function () {
    StateFactory::new()->create(['country_id' => $this->country, 'name' => 'Estado Beta', 'abbreviation' => 'EB']);

    ($this->xml)(['pais' => $this->country->getKey(), 'abbreviation' => 1])
        ->assertSee('<estado id="EB">Estado Beta</estado>', false);
});

test('escapa o nome do estado para não quebrar o xml', function () {
    StateFactory::new()->create(['country_id' => $this->country, 'name' => 'Estado <b>&</b>']);

    ($this->xml)(['pais' => $this->country->getKey()])
        ->assertSee('Estado &lt;b&gt;&amp;&lt;/b&gt;', false)
        ->assertDontSee('<b>', false);
});

test('país que não é número devolve a consulta vazia', function () {
    StateFactory::new()->create(['country_id' => $this->country]);

    ($this->xml)(['pais' => '<script>'])
        ->assertDontSee('<estado', false)
        ->assertDontSee('<script>', false);
});
