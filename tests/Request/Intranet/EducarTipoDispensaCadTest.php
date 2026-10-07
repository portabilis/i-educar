<?php

use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_tipo_dispensa_cad.php';

    $this->type = fn () => LegacyExemptionTypeFactory::new()->create(['nm_tipo' => 'Registro Original', 'ref_cod_instituicao' => $this->institution]);
    $this->edit = fn ($type) => $this->new . '?cod_tipo_dispensa=' . $type->getKey();
    $this->row = fn (string $column, $value) => DB::table('pmieducar.tipo_dispensa')->where($column, $value)->first();
});

test('cadastra um tipo de dispensa', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_tipo' => 'Atestado médico',
        'descricao' => 'Dispensa por motivo de saúde',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_tipo_dispensa_lst.php');

    expect(($this->row)('nm_tipo', 'Atestado médico'))
        ->nm_tipo->toBe('Atestado médico')
        ->descricao->toBe('Dispensa por motivo de saúde')
        ->ativo->toBe(1)
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita o tipo de dispensa', function () {
    $type = ($this->type)();

    $fields = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_tipo'])->toBe('Registro Original')
        ->and($fields['cod_tipo_dispensa'])->toBe((string) $type->getKey());

    $this->post(($this->edit)($type), [
        'nm_tipo' => 'Laudo médico',
        'descricao' => 'Dispensa permanente',
    ] + $fields)->assertRedirect('educar_tipo_dispensa_lst.php');

    expect(($this->row)('cod_tipo_dispensa', $type->getKey()))
        ->nm_tipo->toBe('Laudo médico')
        ->descricao->toBe('Dispensa permanente')
        ->ativo->toBe(1);
});

test('exclui o tipo de dispensa desativando o registro', function () {
    $type = ($this->type)();

    $payload = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($type), $payload)->assertRedirect('educar_tipo_dispensa_lst.php');

    expect(($this->row)('cod_tipo_dispensa', $type->getKey()))
        ->ativo->toBe(0)
        ->nm_tipo->toBe('Registro Original');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(577, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->new)->assertRedirect('educar_tipo_dispensa_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_tipo_dispensa=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
