<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->individual = LegacyIndividualFactory::new()->create();
});

test('pesquisa pessoas pelo nome', function () {
    $this->get('/intranet/pesquisa_pessoa_lst.php?busca=S&campos=cod_pessoa_fj&chave_campo=&campo_busca=' . urlencode($this->individual->person->nome))
        ->assertOk();
});

test('pesquisa pessoas pelo CPF', function () {
    $this->get('/intranet/pesquisa_pessoa_lst.php?busca=S&campos=cod_pessoa_fj&chave_campo=&cpf=00000000191')
        ->assertOk();
});
