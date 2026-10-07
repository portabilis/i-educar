<?php

use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('recusa a unificação sem pessoas selecionadas', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_unifica_pessoa.php')->assertOk()->getContent());

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)->assertRedirect();
});
