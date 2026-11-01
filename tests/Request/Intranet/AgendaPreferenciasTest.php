<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('abre e grava as preferências da agenda', function () {
    $payload = LegacyFields::from($this->get('/intranet/agenda_preferencias.php')->assertOk()->getContent());

    $this->post('/intranet/agenda_preferencias.php', $payload)->assertRedirect('agenda.php');
});
