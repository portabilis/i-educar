<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;
    $this->new = '/intranet/educar_abandono_tipo_cad.php';

    $this->type = fn () => LegacyAbandonmentTypeFactory::new()->create(['nome' => 'Registro Original', 'ref_cod_instituicao' => $this->institution]);
    $this->edit = fn ($type) => $this->new . '?cod_abandono_tipo=' . $type->getKey();
    $this->row = fn (string $column, $value) => DB::table('pmieducar.abandono_tipo')->where($column, $value)->first();
});

test('cadastra um motivo de abandono', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->institution,
        'nome' => 'Mudança de cidade',
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($this->new, $payload)->assertRedirect('educar_abandono_tipo_lst.php');

    expect(($this->row)('nome', 'Mudança de cidade'))
        ->nome->toBe('Mudança de cidade')
        ->ativo->toBe(1)
        ->ref_cod_instituicao->toBe($this->institution)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('abre a edição com os dados gravados e edita o motivo de abandono', function () {
    $type = ($this->type)();

    $fields = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nome'])->toBe('Registro Original')
        ->and($fields['cod_abandono_tipo'])->toBe((string) $type->getKey());

    $this->post(($this->edit)($type), [
        'nome' => 'Mudança de estado',
    ] + $fields)->assertRedirect('educar_abandono_tipo_lst.php');

    expect(($this->row)('cod_abandono_tipo', $type->getKey()))
        ->nome->toBe('Mudança de estado')
        ->ativo->toBe(1);
});

test('exclui o motivo de abandono desativando o registro', function () {
    $type = ($this->type)();

    $payload = LegacyFields::from($this->get(($this->edit)($type))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($type), $payload)->assertRedirect('educar_abandono_tipo_lst.php');

    expect(($this->row)('cod_abandono_tipo', $type->getKey()))
        ->ativo->toBe(0)
        ->nome->toBe('Registro Original');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(950, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    $this->get($this->new)->assertRedirect('educar_abandono_tipo_lst.php');
});

test('com um código inexistente abre o formulário como cadastro', function () {
    $fields = LegacyFields::from($this->get($this->new . '?cod_abandono_tipo=999999999')->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Novo');
});
