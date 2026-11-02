<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->other = LegacyUserFactory::new()->institutional()->create();
});

test('mostra o detalhe do usuário', function () {
    $this->get('/intranet/educar_usuario_det.php?ref_pessoa=' . $this->other->getKey())->assertOk();
});
