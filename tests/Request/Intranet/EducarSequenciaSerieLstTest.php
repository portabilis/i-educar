<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->sequence = LegacyGradeSequenceFactory::new()->create(['ref_usuario_cad' => $this->user->getKey()]);
});

test('lista as sequências de enturmação', function () {
    $this->get('/intranet/educar_sequencia_serie_lst.php')
        ->assertOk()
        ->assertSee('educar_sequencia_serie_det.php?id=' . $this->sequence->getKey(), false);
});

test('filtra pelos cursos de origem e destino da instituição', function () {
    $course = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'nm_curso' => 'Fundamental', 'descricao' => 'Anos iniciais']);
    LegacyGradeFactory::new()->create(['ref_cod_curso' => $course, 'nm_serie' => '1º ano', 'descricao' => 'Turno integral']);

    $this->get('/intranet/educar_sequencia_serie_lst.php?' . http_build_query([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_curso_origem' => $course->getKey(),
        'ref_curso_destino' => $course->getKey(),
    ]))
        ->assertOk()
        ->assertSee('Fundamental - Anos iniciais')
        ->assertSee('1º ano - Turno integral');
});

test('para o usuário institucional a listagem usa a instituição dele', function () {
    $user = LegacyUserFactory::new()->institutional()->withAccess(587)->create();
    // Usuários que não são administradores só entram com o sistema ativo na instituição.
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $this->actingAs($user);

    $this->get('/intranet/educar_sequencia_serie_lst.php')->assertOk();
});
