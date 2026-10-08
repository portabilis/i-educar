<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->record = fn (array $attributes = []) => LegacyEducationLevelFactory::new()->create($attributes + [
        'nm_nivel' => 'Registro Alfa',
        'descricao' => 'Descrição do registro',
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->url = fn ($record) => '/intranet/educar_nivel_ensino_det.php?cod_nivel_ensino=' . $record->getKey();
});

test('mostra a instituição, o nome, a descrição e os links de novo e de edição', function () {
    $record = ($this->record)();

    $this->get(($this->url)($record))
        ->assertOk()
        ->assertSeeInOrder(['Instituição', 'Rede Aurora', 'Nível Ensino', 'Registro Alfa', 'Descrição', 'Descrição do registro'])
        ->assertSee('educar_nivel_ensino_cad.php?cod_nivel_ensino=' . $record->getKey(), false)
        ->assertSee('educar_nivel_ensino_lst.php', false);
});

test('omite a descrição vazia', function () {
    $record = ($this->record)(['descricao' => null]);

    $this->get(($this->url)($record))->assertOk()->assertSee('Registro Alfa')->assertDontSee('Descrição');
});

test('fora do nível de administrador não mostra a instituição, e sem permissão não oferece novo nem editar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $record = ($this->record)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(571, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)($record))
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('Rede Aurora')
        ->assertDontSee('educar_nivel_ensino_cad.php', false);
});

test('volta para a listagem quando o registro não existe', function () {
    $this->get('/intranet/educar_nivel_ensino_det.php?cod_nivel_ensino=0')->assertRedirect('educar_nivel_ensino_lst.php');
});
