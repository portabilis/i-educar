<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyProjectFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->project = LegacyProjectFactory::new()->create(['nome' => 'Registro Alfa', 'observacao' => "Primeira linha\nSegunda linha"]);
    $this->url = '/intranet/educar_projeto_det.php?cod_projeto=' . $this->project->getKey();
});

test('mostra o código, o nome e a observação do projeto e os links de novo e de edição', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Código projeto', (string) $this->project->getKey(), 'Nome do projeto', 'Registro Alfa', 'Observação'])
        ->assertSee("Primeira linha<br />\nSegunda linha", false)
        ->assertSee('educar_projeto_cad.php?cod_projeto=' . $this->project->getKey(), false)
        ->assertSee('educar_projeto_lst.php', false);
});

test('omite a observação vazia', function () {
    $this->project->update(['observacao' => '']);

    $this->get($this->url)->assertOk()->assertSee('Registro Alfa')->assertDontSee('Observação');
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(21250, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('educar_projeto_cad.php', false);
});

test('volta para a listagem quando o projeto não existe', function () {
    $this->get('/intranet/educar_projeto_det.php?cod_projeto=0')->assertRedirect('educar_projeto_lst.php');
});
