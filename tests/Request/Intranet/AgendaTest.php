<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre a agenda do usuário em uma data', function () {
    $this->get('/intranet/agenda.php?time=' . now()->timestamp)->assertOk();
});

test('grava um compromisso na agenda', function () {
    $this->post('/intranet/agenda.php', [
        'novo_titulo' => 'Reunião Alfa',
        'novo_descricao' => 'Pauta da reunião',
        'novo_data' => now()->format('d/m/Y'),
        'novo_hora_inicio' => '10:00',
        'novo_hora_fim' => '11:00',
    ])->assertOk();
});
