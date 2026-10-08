<?php

use App\Models\LegacyEducationLevel;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_nivel_ensino_cad.php';
    $this->record = fn () => LegacyEducationLevelFactory::new()->create([
        'nm_nivel' => 'Registro Original',
        'descricao' => 'Descrição original',
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->edit = fn ($record) => $this->new . '?cod_nivel_ensino=' . $record->getKey();

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(571, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('cadastra o registro', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_nivel' => 'Registro Alfa',
        'descricao' => 'Descrição alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_nivel_ensino_lst.php');

    expect(LegacyEducationLevel::query()->where('nm_nivel', 'Registro Alfa')->sole())
        ->descricao->toBe('Descrição alfa')
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita o registro', function () {
    $record = ($this->record)();

    $fields = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_nivel'])->toBe('Registro Original')
        ->and($fields['descricao'])->toBe('Descrição original')
        ->and($fields['cod_nivel_ensino'])->toBe((string) $record->getKey());

    $this->post(($this->edit)($record), ['nm_nivel' => 'Registro Renomeado', 'descricao' => 'Outra descrição', 'ref_cod_instituicao' => $this->institution] + $fields)
        ->assertRedirect('educar_nivel_ensino_lst.php');

    expect($record->refresh())
        ->nm_nivel->toBe('Registro Renomeado')
        ->descricao->toBe('Outra descrição')
        ->ref_usuario_exc->toBe($this->user->getKey());
});

test('exclui o registro', function () {
    $record = ($this->record)();

    $payload = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($record), $payload)->assertRedirect('educar_nivel_ensino_lst.php');

    expect($record->refresh())->ativo->toBe(0)->nm_nivel->toBe('Registro Original')->ref_usuario_exc->toBe($this->user->getKey());
});

test('editar reativa um nível excluído', function () {
    $record = ($this->record)();
    $record->update(['ativo' => 0]);

    $fields = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->getContent());

    $this->post(($this->edit)($record), ['ref_cod_instituicao' => $this->institution] + $fields)->assertRedirect('educar_nivel_ensino_lst.php');

    expect($record->refresh()->ativo)->toBe(1);
});

test('recusa excluir o nível de ensino de um curso', function () {
    $record = ($this->record)();
    LegacyCourseFactory::new()->create(['ref_cod_nivel_ensino' => $record->getKey(), 'ref_cod_instituicao' => $this->institution]);

    $payload = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($record), $payload)
        ->assertOk()
        ->assertSee('Você não pode excluir esse Nível de Ensino, pois ele possui vínculo com Curso(s).');

    expect($record->refresh()->ativo)->toBe(1);
});

test('defeito: quem só pode visualizar grava e exclui pelo envio direto', function () {
    // As ações não conferem a permissão; só a abertura do formulário confere.
    $record = ($this->record)();
    $payload = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->getContent(), ['nm_nivel' => 'Registro Renomeado', 'ref_cod_instituicao' => $this->institution]);

    ($this->viewer)(false, false);

    $this->post(($this->edit)($record), $payload)->assertRedirect('educar_nivel_ensino_lst.php');

    expect($record->refresh()->nm_nivel)->toBe('Registro Renomeado');
});

test('quem não pode remover não vê o botão de excluir', function () {
    $record = ($this->record)();

    ($this->viewer)(true, false);

    $this->get(($this->edit)($record))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('quem só pode visualizar não abre o formulário', function () {
    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect('educar_nivel_ensino_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    expect(LegacyFields::from($this->get($this->new . '?cod_nivel_ensino=999999999')->assertOk()->getContent())['tipoacao'])->toBe('Novo');
});
