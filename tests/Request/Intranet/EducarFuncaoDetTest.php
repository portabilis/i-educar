<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->role = fn (array $attributes = []) => LegacyRoleFactory::new()->create($attributes + [
        'nm_funcao' => 'Registro Alfa',
        'abreviatura' => 'RA',
        'professor' => 1,
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
    ]);
    $this->url = fn ($role) => '/intranet/educar_funcao_det.php?cod_funcao=' . $role->getKey();
});

test('mostra os dados da função e os links de novo e de edição', function (int $teacher, string $shown) {
    $role = ($this->role)(['professor' => $teacher]);

    $this->get(($this->url)($role))
        ->assertOk()
        ->assertSeeInOrder(['Funcão', (string) $role->getKey(), 'Nome Funcão', 'Registro Alfa', 'Abreviatura', 'RA', 'Permitir vincular a turmas?', $shown])
        ->assertSee('educar_funcao_cad.php?cod_funcao=' . $role->getKey(), false)
        ->assertSee('educar_funcao_lst.php', false);
})->with(['professor' => [1, 'Sim'], 'não professor' => [0, 'Não']]);

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $role = ($this->role)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(634, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get(($this->url)($role))->assertOk()->assertSee('Registro Alfa')->assertDontSee('educar_funcao_cad.php', false);
});

test('volta para a listagem quando a função não existe', function () {
    $this->get('/intranet/educar_funcao_det.php?cod_funcao=0')->assertRedirect('educar_funcao_lst.php');
});
