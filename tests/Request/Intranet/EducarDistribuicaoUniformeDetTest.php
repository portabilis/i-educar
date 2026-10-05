<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->distribution = UniformDistributionFactory::new()->create();
    $this->keys = 'ref_cod_aluno=' . $this->distribution->student_id . '&cod_distribuicao_uniforme=' . $this->distribution->getKey();
});

test('mostra o detalhe da distribuição de uniforme', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_det.php?' . $this->keys)->assertOk();
});

test('volta para a listagem quando a distribuição não existe', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_det.php?ref_cod_aluno=' . $this->distribution->student_id . '&cod_distribuicao_uniforme=0')
        ->assertRedirect();
});
