<?php

use App\Models\Religion;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->new = '/intranet/educar_religiao_cad.php';
    $this->religion = fn () => ReligionFactory::new()->create(['name' => 'Registro Original']);
    $this->edit = fn ($religion) => $this->new . '?cod_religiao=' . $religion->getKey();
});

test('cadastra uma religião', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['nm_religiao' => 'Registro Alfa']);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_religiao_lst.php');

    expect(Religion::query()->where('name', 'Registro Alfa')->exists())->toBeTrue();
});

test('abre a edição com os dados gravados e edita a religião', function () {
    $religion = ($this->religion)();

    $fields = LegacyFields::from($this->get(($this->edit)($religion))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_religiao'])->toBe('Registro Original')
        ->and($fields['cod_religiao'])->toBe((string) $religion->getKey());

    $this->post(($this->edit)($religion), ['nm_religiao' => 'Registro Renomeado'] + $fields)->assertRedirect('educar_religiao_lst.php');

    expect($religion->refresh()->name)->toBe('Registro Renomeado');
});

test('exclui a religião', function () {
    $religion = ($this->religion)();

    $payload = LegacyFields::from($this->get(($this->edit)($religion))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($religion), $payload)->assertRedirect('educar_religiao_lst.php');

    $this->assertSoftDeleted($religion);
});

test('recusa excluir a religião de uma pessoa física', function () {
    $religion = ($this->religion)();
    LegacyIndividualFactory::new()->create(['ref_cod_religiao' => $religion->getKey()]);

    $payload = LegacyFields::from($this->get(($this->edit)($religion))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($religion), $payload)
        ->assertOk()
        ->assertSee('Você não pode excluir essa Religião, pois ela possui Pessoa(s) Física(s) vinculadas.');

    expect($religion->refresh()->trashed())->toBeFalse();
});

test('defeito: as mensagens de sucesso não chegam à sessão', function () {
    // A tela usa `$this->mensagem .=`, que a classe base não intercepta.
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['nm_religiao' => 'Registro Alfa']);

    $this->post($this->new, $payload)
        ->assertRedirect('educar_religiao_lst.php')
        ->assertSessionMissing('legacy');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(579, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_religiao_lst.php');
});

test('responde 404 ao abrir a edição de uma religião que não existe', function () {
    $this->get($this->new . '?cod_religiao=999999999')->assertNotFound();
});
