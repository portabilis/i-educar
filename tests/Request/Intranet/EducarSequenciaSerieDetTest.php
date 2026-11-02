<?php

use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->sequence = LegacyGradeSequenceFactory::new()->create(['ref_usuario_cad' => $this->user->getKey()]);
});

test('mostra o detalhe da sequência de enturmação', function () {
    $this->get('/intranet/educar_sequencia_serie_det.php?id=' . $this->sequence->getKey())->assertOk();
});

test('volta para a listagem quando a sequência não existe', function () {
    $this->get('/intranet/educar_sequencia_serie_det.php?id=0')->assertRedirect();
});
