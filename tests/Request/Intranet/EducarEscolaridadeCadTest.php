<?php

use App\Models\LegacySchoolingDegree;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->new = '/intranet/educar_escolaridade_cad.php';
    $this->degree = fn () => LegacySchoolingDegreeFactory::new()->create([
        'descricao' => 'Registro Original',
        'escolaridade' => Escolaridade::ENSINO_FUNDAMENTAL,
    ]);
    $this->edit = fn ($degree) => $this->new . '?idesco=' . $degree->getKey();
});

test('oferece as escolaridades do Educacenso', function () {
    $options = LegacyForm::from($this->get($this->new)->assertOk()->getContent())->options('escolaridade');

    expect($options)->toBe([
        '' => 'Selecione',
        '1' => 'Não concluiu o Ensino Fundamental',
        '2' => 'Ensino fundamental',
        '7' => 'Ensino médio',
        '6' => 'Educação superior',
    ]);
});

test('cadastra uma escolaridade', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'descricao' => 'Registro Alfa',
        'escolaridade' => '7',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_escolaridade_lst.php');

    expect(LegacySchoolingDegree::query()->where('descricao', 'Registro Alfa')->sole()->escolaridade)->toBe(Escolaridade::ENSINO_MEDIO);
});

test('cadastra sem a escolaridade do Educacenso', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), ['descricao' => 'Registro Alfa']);

    $this->post($this->new, $payload)->assertRedirect('educar_escolaridade_lst.php');

    expect(LegacySchoolingDegree::query()->where('descricao', 'Registro Alfa')->sole()->escolaridade)->toBeNull();
});

test('recusa descrição com mais de 60 caracteres', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'descricao' => str_repeat('a', 61),
        'escolaridade' => '7',
    ]);

    $this->post($this->new, $payload)
        ->assertOk()
        ->assertSee('A descrição deve conter no máximo 60 caracteres.');

    expect(LegacySchoolingDegree::query()->where('descricao', str_repeat('a', 61))->exists())->toBeFalse();
});

test('abre a edição com os dados gravados e edita a escolaridade', function () {
    $degree = ($this->degree)();

    $fields = LegacyFields::from($this->get(($this->edit)($degree))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['descricao'])->toBe('Registro Original')
        ->and($fields['escolaridade'])->toBe('2')
        ->and($fields['idesco'])->toBe((string) $degree->getKey());

    $this->post(($this->edit)($degree), ['descricao' => 'Registro Renomeado', 'escolaridade' => '6'] + $fields)
        ->assertRedirect('educar_escolaridade_lst.php');

    expect($degree->refresh())
        ->descricao->toBe('Registro Renomeado')
        ->escolaridade->toBe(Escolaridade::EDUCACAO_SUPERIOR);
});

test('defeito: a edição não limpa a escolaridade do Educacenso', function () {
    $degree = ($this->degree)();

    $fields = LegacyFields::from($this->get(($this->edit)($degree))->assertOk()->getContent());

    $this->post(($this->edit)($degree), ['escolaridade' => ''] + $fields)->assertRedirect('educar_escolaridade_lst.php');

    expect($degree->refresh()->escolaridade)->toBe(Escolaridade::ENSINO_FUNDAMENTAL);
});

test('defeito: a edição não valida o tamanho da descrição e estoura no banco', function () {
    $degree = ($this->degree)();

    $fields = LegacyFields::from($this->get(($this->edit)($degree))->assertOk()->getContent());

    $this->post(($this->edit)($degree), ['descricao' => str_repeat('a', 61)] + $fields)->assertStatus(500);
});

test('recusa a edição sem nenhum campo e a de escolaridade inexistente', function (array $payload) {
    $this->post($this->new, ['tipoacao' => 'Editar'] + $payload)
        ->assertOk()
        ->assertSee('Edição não realizada.');
})->with([
    'sem campos' => [['idesco' => '999999999']],
    'código inexistente' => [['idesco' => '999999999', 'descricao' => 'Registro Alfa']],
]);

test('exclui a escolaridade', function () {
    $degree = ($this->degree)();

    $response = $this->get(($this->edit)($degree))->assertOk()->assertDontSee("onclick='javascript:modalOpen();'", false);

    $this->post(($this->edit)($degree), LegacyFields::from($response->getContent(), ['tipoacao' => 'Excluir']))
        ->assertRedirect('educar_escolaridade_lst.php');

    $this->assertModelMissing($degree);
});

test('recusa excluir a escolaridade de um servidor e troca o botão por um aviso', function () {
    $degree = ($this->degree)();
    EmployeeFactory::new()->create(['ref_idesco' => $degree->getKey()]);

    $response = $this->get(($this->edit)($degree))->assertOk()->assertSee("onclick='javascript:modalOpen();'", false);

    $this->post(($this->edit)($degree), LegacyFields::from($response->getContent(), ['tipoacao' => 'Excluir']))
        ->assertOk()
        ->assertSee('Exclusão não realizada - Ainda existe vínculos.');

    $this->assertModelExists($degree);
});

test('recusa excluir sem o código e com o código de uma escolaridade que não existe', function (array $payload) {
    $this->post($this->new, ['tipoacao' => 'Excluir'] + $payload)
        ->assertOk()
        ->assertSee('Exclusão não realizada.');
})->with([
    'sem código' => [[]],
    'código inexistente' => [['idesco' => '999999999']],
]);

test('não oferece excluir a quem não pode remover', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $degree = ($this->degree)();

    $this->get(($this->edit)($degree))->assertOk()->assertSee("id='btn_excluir'", false);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(632, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get(($this->edit)($degree))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(632, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_escolaridade_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?idesco=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
