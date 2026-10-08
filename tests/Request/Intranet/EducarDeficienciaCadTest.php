<?php

use App\Models\DeficiencyType;
use App\Models\LegacyDeficiency;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\Transtornos;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->new = '/intranet/educar_deficiencia_cad.php';
    $this->deficiency = fn () => LegacyDeficiencyFactory::new()->create([
        'nm_deficiencia' => 'Registro Original',
        'deficiency_type_id' => DeficiencyType::DEFICIENCY,
        'deficiencia_educacenso' => Deficiencias::CEGUEIRA,
        'transtorno_educacenso' => null,
        'exigir_laudo_medico' => true,
        'desconsidera_regra_diferenciada' => true,
    ]);
    $this->edit = fn ($deficiency) => $this->new . '?cod_deficiencia=' . $deficiency->getKey();
});

test('oferece os tipos e os valores do Educacenso', function () {
    $form = LegacyForm::from($this->get($this->new)->assertOk()->getContent());

    expect($form->options('deficiency_type_id'))->toBe(['' => 'Selecione', '1' => 'Deficiência', '2' => 'Transtorno'])
        ->and($form->options('deficiencia_educacenso'))->toHaveKeys(['1', '25', '999'])
        ->and($form->options('transtorno_educacenso'))->toHaveKeys(['50', '55', '999']);
});

test('cadastra uma deficiência com as duas marcações', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'nm_deficiencia' => 'Registro Alfa',
        'deficiency_type_id' => '1',
        'deficiencia_educacenso' => '3',
        'exigir_laudo_medico' => 'on',
        'desconsidera_regra_diferenciada' => 'on',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_deficiencia_lst.php');

    expect(LegacyDeficiency::query()->where('nm_deficiencia', 'Registro Alfa')->sole())
        ->deficiency_type_id->toBe(DeficiencyType::DEFICIENCY)
        ->deficiencia_educacenso->toBe(Deficiencias::SURDEZ)
        ->transtorno_educacenso->toBeNull()
        ->exigir_laudo_medico->toBeTrue()
        ->desconsidera_regra_diferenciada->toBeTrue();
});

test('cadastra um transtorno sem as marcações', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'nm_deficiencia' => 'Registro Alfa',
        'deficiency_type_id' => '2',
        'transtorno_educacenso' => '53',
    ]);

    $this->post($this->new, $payload)->assertRedirect('educar_deficiencia_lst.php');

    expect(LegacyDeficiency::query()->where('nm_deficiencia', 'Registro Alfa')->sole())
        ->deficiency_type_id->toBe(DeficiencyType::DISORDER)
        ->deficiencia_educacenso->toBeNull()
        ->transtorno_educacenso->toBe(Transtornos::DISLEXIA)
        ->exigir_laudo_medico->toBeFalse()
        ->desconsidera_regra_diferenciada->toBeFalse();
});

test('recusa o cadastro sem o campo do nome', function () {
    $this->post($this->new, ['tipoacao' => 'Novo', 'deficiency_type_id' => '1'])
        ->assertOk()
        ->assertSee('Cadastro não realizado.');
});

test('abre a edição com os dados gravados e edita a deficiência', function () {
    $deficiency = ($this->deficiency)();

    $fields = LegacyFields::from($this->get(($this->edit)($deficiency))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_deficiencia'])->toBe('Registro Original')
        ->and($fields['deficiency_type_id'])->toBe('1')
        ->and($fields['deficiencia_educacenso'])->toBe('1')
        ->and($fields)->toHaveKeys(['exigir_laudo_medico', 'desconsidera_regra_diferenciada'])
        ->and($fields['cod_deficiencia'])->toBe((string) $deficiency->getKey());

    unset($fields['exigir_laudo_medico'], $fields['desconsidera_regra_diferenciada']);

    $this->post(($this->edit)($deficiency), [
        'nm_deficiencia' => 'Registro Renomeado',
        'deficiency_type_id' => '2',
        'deficiencia_educacenso' => '',
        'transtorno_educacenso' => '50',
    ] + $fields)->assertRedirect('educar_deficiencia_lst.php');

    expect($deficiency->refresh())
        ->nm_deficiencia->toBe('Registro Renomeado')
        ->deficiency_type_id->toBe(DeficiencyType::DISORDER)
        ->deficiencia_educacenso->toBeNull()
        ->transtorno_educacenso->toBe(Transtornos::DISCALCULIA)
        ->exigir_laudo_medico->toBeFalse()
        ->desconsidera_regra_diferenciada->toBeFalse();
});

test('recusa a edição sem o código da deficiência', function () {
    $this->post($this->new, ['tipoacao' => 'Editar', 'nm_deficiencia' => 'Registro Alfa'])
        ->assertOk()
        ->assertSee('Edição não realizada.');
});

test('exclui a deficiência', function () {
    $deficiency = ($this->deficiency)();

    $payload = LegacyFields::from($this->get(($this->edit)($deficiency))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($deficiency), $payload)->assertRedirect('educar_deficiencia_lst.php');

    $this->assertModelMissing($deficiency);
});

test('recusa excluir a deficiência de uma pessoa física', function () {
    $deficiency = ($this->deficiency)();
    $deficiency->individuals()->attach(LegacyIndividualFactory::new()->create()->getKey());

    $payload = LegacyFields::from($this->get(($this->edit)($deficiency))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($deficiency), $payload)
        ->assertOk()
        ->assertSee('Exclusão não realizada, pois existem pessoas físicas com a deficiência.');

    $this->assertModelExists($deficiency);
});

test('não oferece excluir a quem não pode remover', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $deficiency = ($this->deficiency)();

    $this->get(($this->edit)($deficiency))->assertOk()->assertSee("id='btn_excluir'", false);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(631, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get(($this->edit)($deficiency))->assertOk()->assertDontSee("id='btn_excluir'", false);
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(631, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_deficiencia_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_deficiencia=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
