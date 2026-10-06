<?php

use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

test('lista os tipos de usuário e aplica os filtros', function () {
    $this->get('/intranet/educar_tipo_usuario_lst.php')->assertOk();
    $this->get('/intranet/educar_tipo_usuario_lst.php?nm_tipo=nome-que-nao-existe&descricao=x&nivel=1')->assertOk();
});
