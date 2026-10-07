<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_transferencia_tipo_cad.php';

    $this->type = fn () => LegacyTransferTypeFactory::new()->create(['nm_tipo' => 'Registro Original', 'ref_cod_instituicao' => $this->institution]);
    $this->edit = fn ($type) => $this->new . '?cod_transferencia_tipo=' . $type->getKey();
    $this->row = fn (string $column, $value) => DB::table('pmieducar.transferencia_tipo')->where($column, $value)->first();
});

test('cadastra um tipo de transferência', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_tipo' => 'Mudança de endereço',
        'desc_tipo' => 'A família mudou de bairro',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_transferencia_tipo_lst.php');

    expect(($this->row)('nm_tipo', 'Mudança de endereço'))
        ->nm_tipo->toBe('Mudança de endereço')
        ->desc_tipo->toBe('A família mudou de bairro')
        ->ativo->toBe(1)
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita o tipo de transferência', function () {
    $type = ($this->type)();

    $fields = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_tipo'])->toBe('Registro Original')
        ->and($fields['cod_transferencia_tipo'])->toBe((string) $type->getKey());

    $this->post(($this->edit)($type), [
        'nm_tipo' => 'Mudança de município',
        'desc_tipo' => 'A família mudou de cidade',
    ] + $fields)->assertRedirect('educar_transferencia_tipo_lst.php');

    expect(($this->row)('cod_transferencia_tipo', $type->getKey()))
        ->nm_tipo->toBe('Mudança de município')
        ->desc_tipo->toBe('A família mudou de cidade')
        ->ativo->toBe(1);
});

test('exclui o tipo de transferência desativando o registro', function () {
    $type = ($this->type)();

    $payload = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($type), $payload)->assertRedirect('educar_transferencia_tipo_lst.php');

    expect(($this->row)('cod_transferencia_tipo', $type->getKey()))
        ->ativo->toBe(0)
        ->nm_tipo->toBe('Registro Original');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(575, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->new)->assertRedirect('educar_transferencia_tipo_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_transferencia_tipo=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
