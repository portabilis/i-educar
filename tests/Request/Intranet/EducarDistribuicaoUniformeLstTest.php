<?php

use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->distribution = UniformDistributionFactory::new()->create();
    $this->keys = 'ref_cod_aluno=' . $this->distribution->student_id . '&cod_distribuicao_uniforme=' . $this->distribution->getKey();
});

test('lista as distribuições de uniforme do aluno', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_lst.php?ref_cod_aluno=' . $this->distribution->student_id)
        ->assertOk()
        ->assertSee('educar_distribuicao_uniforme_det.php');
});

test('volta para a listagem de alunos quando o aluno não é informado', function () {
    $this->get('/intranet/educar_distribuicao_uniforme_lst.php')->assertRedirect();
});
