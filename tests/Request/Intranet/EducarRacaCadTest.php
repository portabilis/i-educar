<?php

use App\Models\LegacyRace;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->new = '/intranet/educar_raca_cad.php';
    $this->race = fn () => LegacyRaceFactory::new()->create(['nm_raca' => 'Registro Original', 'raca_educacenso' => 2]);
    $this->edit = fn ($race) => $this->new . '?cod_raca=' . $race->getKey();
});

test('oferece as raças do Educacenso', function () {
    $options = LegacyForm::from($this->get($this->new)->assertOk()->getContent())->options('raca_educacenso');

    expect(array_filter($options))->toBe([
        '0' => 'Não declarada',
        '1' => 'Branca',
        '2' => 'Preta',
        '3' => 'Parda',
        '4' => 'Amarela',
        '5' => 'Indígena',
    ]);
});

test('cadastra uma raça', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'nm_raca' => 'Registro Alfa',
        'raca_educacenso' => '3',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)
        ->assertRedirect('educar_raca_lst.php')
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br>');

    expect(LegacyRace::query()->where('nm_raca', 'Registro Alfa')->sole())
        ->raca_educacenso->toBe(3)
        ->ativo->toBeTrue()
        ->idpes_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita a raça', function () {
    $race = ($this->race)();

    $fields = LegacyFields::from($this->get(($this->edit)($race))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_raca'])->toBe('Registro Original')
        ->and($fields['raca_educacenso'])->toBe('2')
        ->and($fields['cod_raca'])->toBe((string) $race->getKey());

    $this->post(($this->edit)($race), ['nm_raca' => 'Registro Renomeado', 'raca_educacenso' => '5'] + $fields)
        ->assertRedirect('educar_raca_lst.php')
        ->assertSessionHas('legacy', 'Edição efetuada com sucesso.<br>');

    expect($race->refresh())
        ->nm_raca->toBe('Registro Renomeado')
        ->raca_educacenso->toBe(5)
        ->ativo->toBeTrue()
        ->idpes_cad->toBe($this->user->getKey());
});

test('editar reativa uma raça excluída', function () {
    $race = ($this->race)();
    $race->forceFill(['ativo' => false])->save();

    $fields = LegacyFields::from($this->get(($this->edit)($race))->assertOk()->getContent());

    $this->post(($this->edit)($race), $fields)->assertRedirect('educar_raca_lst.php');

    expect($race->refresh()->ativo)->toBeTrue();
});

test('exclui a raça desativando o registro', function () {
    $race = ($this->race)();

    $payload = LegacyFields::from($this->get(($this->edit)($race))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($race), $payload)
        ->assertRedirect('educar_raca_lst.php')
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br>');

    expect($race->refresh())
        ->ativo->toBeFalse()
        ->nm_raca->toBe('Registro Original');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(678, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_raca_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_raca=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
