<?php

use App\Models\WithdrawalReason;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_motivo_afastamento_cad.php';
    $this->record = fn () => WithdrawalReasonFactory::new()->create([
        'nm_motivo' => 'Registro Original',
        'descricao' => 'Descrição original',
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->edit = fn ($record) => $this->new . '?cod_motivo_afastamento=' . $record->getKey();

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(633, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('cadastra o registro', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_motivo' => 'Registro Alfa',
        'descricao' => 'Descrição alfa',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_motivo_afastamento_lst.php');

    expect(WithdrawalReason::query()->where('nm_motivo', 'Registro Alfa')->sole())
        ->descricao->toBe('Descrição alfa')
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita o registro', function () {
    $record = ($this->record)();

    $fields = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_motivo'])->toBe('Registro Original')
        ->and($fields['descricao'])->toBe('Descrição original')
        ->and($fields['cod_motivo_afastamento'])->toBe((string) $record->getKey());

    $this->post(($this->edit)($record), ['nm_motivo' => 'Registro Renomeado', 'descricao' => 'Outra descrição', 'ref_cod_instituicao' => $this->institution] + $fields)
        ->assertRedirect('educar_motivo_afastamento_lst.php');

    expect($record->refresh())
        ->nm_motivo->toBe('Registro Renomeado')
        ->descricao->toBe('Outra descrição')
        ->ref_usuario_exc->toBe($this->user->getKey());
});

test('exclui o registro', function () {
    $record = ($this->record)();

    $payload = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($record), $payload)->assertRedirect('educar_motivo_afastamento_lst.php');

    expect(WithdrawalReason::query()->whereKey($record->getKey())->exists())->toBeFalse();
});

test('quem só pode visualizar não grava nem exclui', function (string $action) {
    $record = ($this->record)();
    $payload = LegacyFields::from($this->get(($this->edit)($record))->assertOk()->getContent(), ['tipoacao' => $action, 'nm_motivo' => 'Registro Renomeado']);

    ($this->viewer)(false, false);

    $this->post(($this->edit)($record), $payload)->assertRedirect('educar_motivo_afastamento_lst.php');

    expect(WithdrawalReason::query()->whereKey($record->getKey())->value('nm_motivo'))->toBe('Registro Original');
})->with(['Novo', 'Editar', 'Excluir']);

test('quem não pode remover não vê o botão de excluir', function () {
    $record = ($this->record)();

    ($this->viewer)(true, false);

    $this->get(($this->edit)($record))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('quem só pode visualizar não abre o formulário', function () {
    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect('educar_motivo_afastamento_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    expect(LegacyFields::from($this->get($this->new . '?cod_motivo_afastamento=999999999')->assertOk()->getContent())['tipoacao'])->toBe('Novo');
});
