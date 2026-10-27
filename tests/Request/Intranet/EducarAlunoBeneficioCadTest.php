<?php

use App\Models\LegacyBenefit;
use App\Models\LegacyStudentBenefit;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->new = '/intranet/educar_aluno_beneficio_cad.php';
    $this->benefit = fn (array $attributes = []) => LegacyBenefitFactory::new()->create($attributes + [
        'nm_beneficio' => 'Registro Original',
        'desc_beneficio' => 'Descrição original',
        'bolsa_familia' => true,
    ]);
    $this->edit = fn ($benefit) => $this->new . '?cod_aluno_beneficio=' . $benefit->getKey();
});

test('cadastra um benefício marcado como Bolsa Família', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'nm_beneficio' => 'Registro Alfa',
        'desc_beneficio' => 'Auxílio mensal',
        'bolsa_familia' => 'on',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_aluno_beneficio_lst.php');

    expect(LegacyBenefit::query()->where('nm_beneficio', 'Registro Alfa')->sole())
        ->desc_beneficio->toBe('Auxílio mensal')
        ->bolsa_familia->toBeTrue()
        ->ativo->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('cadastra um benefício sem a marcação de Bolsa Família', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['nm_beneficio' => 'Registro Alfa']);

    $this->post($this->new, $payload)->assertRedirect('educar_aluno_beneficio_lst.php');

    expect(LegacyBenefit::query()->where('nm_beneficio', 'Registro Alfa')->sole()->bolsa_familia)->toBeFalse();
});

test('abre a edição com os dados gravados e edita o benefício', function () {
    $benefit = ($this->benefit)();

    $fields = LegacyFields::from($this->get(($this->edit)($benefit))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_beneficio'])->toBe('Registro Original')
        ->and($fields['desc_beneficio'])->toBe('Descrição original')
        ->and($fields)->toHaveKey('bolsa_familia')
        ->and($fields['cod_aluno_beneficio'])->toBe((string) $benefit->getKey());

    unset($fields['bolsa_familia']);

    $this->post(($this->edit)($benefit), ['nm_beneficio' => 'Registro Renomeado', 'desc_beneficio' => 'Outra descrição'] + $fields)
        ->assertRedirect('educar_aluno_beneficio_lst.php');

    expect($benefit->refresh())
        ->nm_beneficio->toBe('Registro Renomeado')
        ->desc_beneficio->toBe('Outra descrição')
        ->bolsa_familia->toBeFalse()
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('editar reativa um benefício excluído', function () {
    $benefit = ($this->benefit)();
    $benefit->update(['ativo' => 0]);

    expect($benefit->refresh()->ativo)->toBe(0);

    $fields = LegacyFields::from($this->get(($this->edit)($benefit))->assertOk()->getContent());

    $this->post(($this->edit)($benefit), $fields)->assertRedirect('educar_aluno_beneficio_lst.php');

    expect($benefit->refresh()->ativo)->toBe(1);
});

test('exclui o benefício desativando o registro', function () {
    $benefit = ($this->benefit)();

    $payload = LegacyFields::from($this->get(($this->edit)($benefit))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($benefit), $payload)->assertRedirect('educar_aluno_beneficio_lst.php');

    expect($benefit->refresh())
        ->ativo->toBe(0)
        ->nm_beneficio->toBe('Registro Original');
});

test('recusa excluir o benefício vinculado a um aluno', function () {
    $benefit = ($this->benefit)();
    LegacyStudentBenefit::query()->create([
        'aluno_id' => LegacyStudentFactory::new()->create()->getKey(),
        'aluno_beneficio_id' => $benefit->getKey(),
    ]);

    $payload = LegacyFields::from($this->get(($this->edit)($benefit))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($benefit), $payload)
        ->assertOk()
        ->assertSee('Você não pode excluir esse benefício, pois ele possui vínculo com aluno(s).');

    expect($benefit->refresh()->ativo)->toBe(1);
});

test('não oferece excluir a quem não pode remover', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $benefit = ($this->benefit)();

    $this->get(($this->edit)($benefit))->assertOk()->assertSee("id='btn_excluir'", false);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(581, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get(($this->edit)($benefit))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(581, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_aluno_beneficio_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_aluno_beneficio=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
