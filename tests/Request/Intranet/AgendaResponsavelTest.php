<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('pesquisa responsáveis pela agenda', function () {
    $this->get('/intranet/agenda_responsavel.php?pesquisa=nome-que-nao-existe')->assertOk();
});
