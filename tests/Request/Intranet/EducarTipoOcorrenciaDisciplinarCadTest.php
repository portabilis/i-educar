<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_tipo_ocorrencia_disciplinar_cad.php';

    $this->type = fn () => LegacyDisciplinaryOccurrenceTypeFactory::new()->create(['name' => 'Registro Original', 'institution_id' => $this->institution]);
    $this->edit = fn ($type) => $this->new . '?cod_tipo_ocorrencia_disciplinar=' . $type->getKey();
    $this->row = fn (string $column, $value) => DB::table('pmieducar.tipo_ocorrencia_disciplinar')->where($column, $value)->first();
});

test('cadastra um tipo de ocorrência disciplinar', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_tipo' => 'Atraso recorrente',
        'descricao' => 'Chegou depois do sinal',
        'max_ocorrencias' => '7',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');

    expect(($this->row)('nm_tipo', 'Atraso recorrente'))
        ->nm_tipo->toBe('Atraso recorrente')
        ->descricao->toBe('Chegou depois do sinal')
        ->max_ocorrencias->toEqual('7')
        ->ativo->toBe(1)
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita o tipo de ocorrência disciplinar', function () {
    $type = ($this->type)();

    $fields = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_tipo'])->toBe('Registro Original')
        ->and($fields['cod_tipo_ocorrencia_disciplinar'])->toBe((string) $type->getKey());

    $this->post(($this->edit)($type), [
        'nm_tipo' => 'Atraso eventual',
        'descricao' => 'Chegou atrasado',
        'max_ocorrencias' => '3',
    ] + $fields)->assertRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');

    expect(($this->row)('cod_tipo_ocorrencia_disciplinar', $type->getKey()))
        ->nm_tipo->toBe('Atraso eventual')
        ->descricao->toBe('Chegou atrasado')
        ->max_ocorrencias->toEqual('3')
        ->ativo->toBe(1);
});

test('exclui o tipo de ocorrência disciplinar desativando o registro', function () {
    $type = ($this->type)();

    $payload = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($type), $payload)->assertRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');

    expect(($this->row)('cod_tipo_ocorrencia_disciplinar', $type->getKey()))
        ->ativo->toBe(0)
        ->nm_tipo->toBe('Registro Original');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(580, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->new)->assertRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_tipo_ocorrencia_disciplinar=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});

test('grava o máximo de ocorrências em branco como nulo', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nm_tipo' => 'Sem limite',
        'max_ocorrencias' => '',
    ]);

    $this->post($this->new, $payload)->assertRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');

    expect(($this->row)('nm_tipo', 'Sem limite')->max_ocorrencias)->toBeNull();
});
