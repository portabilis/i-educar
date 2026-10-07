<?php

use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->school = LegacySchoolFactory::new()->create();
});

test('volta para a listagem de escolas sem os dados da ação', function () {
    $this->get('/intranet/educar_iniciar_ano_letivo.php')->assertRedirect('educar_escola_lst.php');
    $this->post('/intranet/educar_iniciar_ano_letivo.php', ['ref_cod_escola' => $this->school->getKey()])
        ->assertRedirect('educar_escola_lst.php');
});

test('encaminha para a edição das etapas', function () {
    $this->post('/intranet/educar_iniciar_ano_letivo.php', [
        'ref_cod_escola' => $this->school->getKey(),
        'tipo_acao' => 'editar',
        'ano' => now()->year,
    ])->assertRedirect();
});

test('inicia e finaliza o ano letivo da escola', function () {
    $year = LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->school, 'andamento' => 0]);
    $data = ['ref_cod_escola' => $this->school->getKey(), 'ano' => $year->ano];

    $this->post('/intranet/educar_iniciar_ano_letivo.php', $data + ['tipo_acao' => 'iniciar'])
        ->assertOk()
        ->assertSee('Ano letivo inicializado com sucesso!', false);

    expect($year->refresh()->andamento)->toBe(1);

    $this->post('/intranet/educar_iniciar_ano_letivo.php', $data + ['tipo_acao' => 'finalizar'])
        ->assertOk()
        ->assertSee('Ano letivo finalizado com sucesso!', false);

    expect($year->refresh()->andamento)->toBe(2);
});
