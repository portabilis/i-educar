<?php

use App\Models\LegacyRole;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_funcao_cad.php';
    $this->role = fn () => LegacyRoleFactory::new()->create([
        'nm_funcao' => 'Registro Original',
        'abreviatura' => 'RO',
        'professor' => 1,
        'ref_cod_instituicao' => $this->institution,
    ]);
    $this->edit = fn ($role) => $this->new . '?cod_funcao=' . $role->getKey();

    $this->viewer = function (bool $modify, bool $remove) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(634, modify: $modify, remove: $remove)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

// "Permitir vincular a turmas?" é um checkbox: marcado envia 1, desmarcado não envia nada.
test('cadastra uma função', function (bool $teacher, int $stored) {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_funcao' => 'Registro Alfa',
        'abreviatura' => 'RA',
        'professor' => '1',
    ]);

    if (!$teacher) {
        unset($payload['professor']);
    }

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_funcao_lst.php');

    expect(LegacyRole::query()->where('nm_funcao', 'Registro Alfa')->sole())
        ->abreviatura->toBe('RA')
        ->professor->toBe($stored)
        ->ativo->toBe(1)
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
})->with(['professor' => [true, 1], 'não professor' => [false, 0]]);

test('abre a edição com os dados gravados e edita a função', function () {
    $role = ($this->role)();

    $fields = LegacyFields::from($this->get(($this->edit)($role))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_funcao'])->toBe('Registro Original')
        ->and($fields['abreviatura'])->toBe('RO')
        ->and($fields['professor'])->toBe('1')
        ->and($fields['cod_funcao'])->toBe((string) $role->getKey());

    unset($fields['professor']);

    $this->post(($this->edit)($role), ['nm_funcao' => 'Registro Renomeado', 'abreviatura' => 'RR', 'ref_cod_instituicao' => $this->institution] + $fields)
        ->assertRedirect('educar_funcao_lst.php');

    expect($role->refresh())
        ->nm_funcao->toBe('Registro Renomeado')
        ->abreviatura->toBe('RR')
        ->professor->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey());
});

test('abre a edição de uma função que não é de professor', function () {
    $role = ($this->role)();
    $role->update(['professor' => 0]);

    expect(LegacyFields::from($this->get(($this->edit)($role))->assertOk()->getContent()))->not->toHaveKey('professor');
});

test('exclui a função', function () {
    $role = ($this->role)();

    $payload = LegacyFields::from($this->get(($this->edit)($role))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($role), $payload)->assertRedirect('educar_funcao_lst.php');

    expect(LegacyRole::query()->where('ativo', 1)->whereKey($role->getKey())->exists())->toBeFalse();
});

test('quem só pode visualizar não abre nem grava', function (string $action) {
    $role = ($this->role)();
    $payload = LegacyFields::from($this->get(($this->edit)($role))->assertOk()->getContent(), ['tipoacao' => $action, 'nm_funcao' => 'Registro Renomeado']);

    ($this->viewer)(false, false);

    $this->get($this->new)->assertRedirect('educar_funcao_lst.php');
    $this->post(($this->edit)($role), $payload)->assertRedirect('educar_funcao_lst.php');

    expect($role->refresh())->nm_funcao->toBe('Registro Original')->ativo->toBe(1);
})->with(['Novo', 'Editar', 'Excluir']);

test('quem não pode remover não vê o botão de excluir', function () {
    $role = ($this->role)();

    ($this->viewer)(true, false);

    $this->get(($this->edit)($role))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('com um código inexistente abre o formulário como cadastro', function () {
    expect(LegacyFields::from($this->get($this->new . '?cod_funcao=999999999')->assertOk()->getContent())['tipoacao'])->toBe('Novo');
});
