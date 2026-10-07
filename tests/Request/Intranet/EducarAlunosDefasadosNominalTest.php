<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->page = '/intranet/educar_alunos_defasados_nominal.php';
});

test('abre o relatório e, para quem escolhe a escola, liga a troca de cursos ao campo', function () {
    $this->get($this->page)
        ->assertOk()
        ->assertSee('Movimentação Mensal de Alunos')
        ->assertSee("document.getElementById('ref_cod_escola').onchange = changeCurso", false);
});

test('para o usuário de escola, carrega os cursos ao abrir', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $this->actingAs(
        LegacyUserFactory::new()->school()->withSchool(LegacySchoolFactory::new()->create())->withAccess(944)
            ->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->page)
        ->assertOk()
        ->assertSee('window.onload = function(){ changeCurso() };', false);
});

test('devolve os campos enviados no formulário', function () {
    $this->post($this->page, ['mes' => '5', 'ano' => '2030'])->assertOk();
});

test('usuário de biblioteca volta para o início', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);
    $this->actingAs(
        LegacyUserFactory::new()->withAccess(944)->create([
            'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
            'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
        ])
    );

    $this->get($this->page)->assertRedirect('index.php');
});
