<?php

use App\Models\LegacyBondType;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $this->new = '/intranet/funcionario_vinculo_cad.php';
    $this->bond = fn (string $name = 'Registro Original', string $short = 'RO') => LegacyBondType::query()->create(['nm_vinculo' => $name, 'abreviatura' => $short]);
    $this->edit = fn ($bond) => $this->new . '?cod_funcionario_vinculo=' . $bond->getKey();

    // A tela não redireciona por cabeçalho: responde 200 com um script.
    $this->redirected = "document.location='funcionario_vinculo_lst.php'";
});

test('cadastra um vínculo', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['nm_vinculo' => 'Registro Alfa', 'abreviatura' => 'RA']);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertOk()->assertSee($this->redirected, false);

    expect(LegacyBondType::query()->where('nm_vinculo', 'Registro Alfa')->sole()->abreviatura)->toBe('RA');
});

test('recusa nome ou abreviatura já usados, sem diferenciar maiúsculas', function (array $overrides) {
    ($this->bond)();

    $this->post($this->new, $overrides + ['tipoacao' => 'Novo', 'nm_vinculo' => 'Registro Alfa', 'abreviatura' => 'RA'])
        ->assertOk()
        ->assertSee('Já existe um registro com este nome ou abreviatura.');

    expect(LegacyBondType::query()->where('abreviatura', 'RA')->exists())->toBeFalse();
})->with([
    'mesmo nome' => [['nm_vinculo' => 'registro original']],
    'mesma abreviatura' => [['abreviatura' => 'ro', 'nm_vinculo' => 'Outro Nome']],
]);

test('abre a edição com os dados gravados e edita o vínculo', function () {
    $bond = ($this->bond)();

    $fields = LegacyFields::from($this->get(($this->edit)($bond))->assertOk()->assertSee("id='btn_excluir'", false)->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_vinculo'])->toBe('Registro Original')
        ->and($fields['abreviatura'])->toBe('RO')
        ->and($fields['cod_vinculo'])->toBe((string) $bond->getKey());

    $this->post(($this->edit)($bond), ['nm_vinculo' => 'Registro Renomeado'] + $fields)->assertOk()->assertSee($this->redirected, false);

    expect($bond->refresh())->nm_vinculo->toBe('Registro Renomeado')->abreviatura->toBe('RO');
});

test('defeito: a edição que mantém o nome é recusada como duplicada', function () {
    // Na consulta de duplicidade o "diferente deste código" só se aplica à
    // abreviatura; o próprio nome conta como repetido.
    $bond = ($this->bond)();

    $fields = LegacyFields::from($this->get(($this->edit)($bond))->assertOk()->getContent());

    $this->post(($this->edit)($bond), ['abreviatura' => 'RX'] + $fields)
        ->assertOk()
        ->assertSee('Já existe um registro com este nome ou abreviatura.');

    expect($bond->refresh()->abreviatura)->toBe('RO');
});

test('exclui o vínculo', function () {
    $bond = ($this->bond)();

    $payload = LegacyFields::from($this->get(($this->edit)($bond))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($bond), $payload)->assertOk()->assertSee($this->redirected, false);

    $this->assertModelMissing($bond);
});

test('recusa excluir o vínculo de um funcionário', function () {
    $bond = ($this->bond)();
    LegacyEmployeeFactory::new()->create(['ref_cod_funcionario_vinculo' => $bond->getKey()]);

    $payload = LegacyFields::from($this->get(($this->edit)($bond))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($bond), $payload)
        ->assertOk()
        ->assertSee('Não é possível remover. Já existem funcionários cadastrados e alocados com este vínculo.');

    $this->assertModelExists($bond);
});

test('com um código inexistente abre o formulário como cadastro', function () {
    expect(LegacyFields::from($this->get($this->new . '?cod_funcionario_vinculo=999999999')->assertOk()->getContent())['tipoacao'])->toBe('Novo');
});
