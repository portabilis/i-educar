<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->other = LegacyUserFactory::new()->institutional()->create();
});

test('lista os usuários filtrando pela matrícula', function () {
    $this->get('/intranet/educar_usuario_lst.php?busca=S&matricula=' . urlencode($this->other->employee->matricula) . '&ref_cod_instituicao=' . $this->user->ref_cod_instituicao)
        ->assertOk()
        ->assertSee('educar_usuario_det.php?ref_pessoa=' . $this->other->getKey(), false);
});
