<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->individual = LegacyIndividualFactory::new()->create();
});

test('lista as pessoas físicas filtrando pelo nome', function () {
    $this->get('/intranet/atendidos_lst.php?nm_pessoa=' . urlencode($this->individual->person->nome))
        ->assertOk()
        ->assertSee('atendidos_det.php?cod_pessoa=' . $this->individual->getKey(), false);

    $this->get('/intranet/atendidos_lst.php?nm_pessoa=nome-que-nao-existe&id_federal=00000000191&situacao_cpf=1&motivo_ausencia_cpf=1')
        ->assertOk();
});
