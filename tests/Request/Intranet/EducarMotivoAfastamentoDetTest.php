<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->record = fn (array $attributes = []) => WithdrawalReasonFactory::new()->create($attributes + [
        'nm_motivo' => 'Registro Alfa',
        'descricao' => 'Descrição do registro',
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->url = fn ($record) => '/intranet/educar_motivo_afastamento_det.php?cod_motivo_afastamento=' . $record->getKey();
});

test('mostra a instituição, o nome, a descrição e os links de novo e de edição', function () {
    $record = ($this->record)();

    $this->get(($this->url)($record))
        ->assertOk()
        ->assertSeeInOrder(['Instituição', 'Rede Aurora', 'Motivo de Afastamento', 'Registro Alfa', 'Descrição', 'Descrição do registro'])
        ->assertSee('educar_motivo_afastamento_cad.php?cod_motivo_afastamento=' . $record->getKey(), false)
        ->assertSee('educar_motivo_afastamento_lst.php', false);
});

test('omite a descrição vazia', function () {
    $record = ($this->record)(['descricao' => null]);

    $this->get(($this->url)($record))->assertOk()->assertSee('Registro Alfa')->assertDontSee('Descrição');
});

test('fora do nível de administrador não mostra a instituição, e sem permissão não oferece novo nem editar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $record = ($this->record)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(633, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get(($this->url)($record))
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('Rede Aurora')
        ->assertDontSee('educar_motivo_afastamento_cad.php', false);
});

test('volta para a listagem quando o registro não existe', function () {
    $this->get('/intranet/educar_motivo_afastamento_det.php?cod_motivo_afastamento=0')->assertRedirect('educar_motivo_afastamento_lst.php');
});
