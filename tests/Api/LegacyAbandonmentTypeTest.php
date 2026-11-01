<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('save success', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);

    $type = LegacyAbandonmentTypeFactory::new()->make();

    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'ref_cod_instituicao' => $type->ref_cod_instituicao,
        'nome' => $type->nome,
    ];

    $payload = array_merge($request, $data);
    $this->post('/intranet/educar_abandono_tipo_cad.php', $payload)
        ->assertRedirectContains('educar_abandono_tipo_lst.php');

    $this->assertDatabaseHas($type->getTable(), [
        'ref_cod_instituicao' => $type->ref_cod_instituicao,
        'ref_usuario_cad' => $user->getKey(),
        'nome' => $type->nome,
        'ativo' => 1,
    ]);
});

test('edit success', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);

    $typeOrignal = LegacyAbandonmentTypeFactory::new()->create();

    $editType = LegacyAbandonmentTypeFactory::new()->make();

    $request = [
        'tipoacao' => 'Editar',
    ];

    $data = [
        'cod_abandono_tipo' => $typeOrignal->getKey(),
        'ref_cod_instituicao' => $editType->ref_cod_instituicao,
        'nome' => $editType->nome,
    ];

    $payload = array_merge($request, $data);
    $this->post('/intranet/educar_abandono_tipo_cad.php', $payload)
        ->assertRedirectContains('educar_abandono_tipo_lst.php');

    $this->assertDatabaseHas($editType, [
        'cod_abandono_tipo' => $typeOrignal->getKey(),
        'ref_cod_instituicao' => $editType->ref_cod_instituicao,
        'nome' => $editType->nome,
        'ref_usuario_exc' => $user->getKey(),
        'ativo' => 1,
    ]);
});
