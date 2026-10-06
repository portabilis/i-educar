<?php

use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->record = LegacyRoleFactory::new()->create();
});

test('mostra o detalhe de função', function () {
    $this->get('/intranet/educar_funcao_det.php?cod_funcao=' . $this->record->getKey())->assertOk();
});

test('volta para a listagem quando função não existe', function () {
    $this->get('/intranet/educar_funcao_det.php?cod_funcao=0')->assertRedirect();
});
