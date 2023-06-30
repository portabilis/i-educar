<?php

namespace Tests\Api;

use Database\Factories\LegacyTransferTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyTransferTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyTransferTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
            'desc_tipo' => $type->desc_tipo,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_transferencia_tipo_cad.php', $payload)
            ->assertRedirectContains('educar_transferencia_tipo_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'desc_tipo' => $type->desc_tipo,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyTransferTypeFactory::new()->create();

        $editType = LegacyTransferTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_transferencia_tipo' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'desc_tipo' => $editType->desc_tipo,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_transferencia_tipo_cad.php', $payload)
            ->assertRedirectContains('educar_transferencia_tipo_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_transferencia_tipo' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'desc_tipo' => $editType->desc_tipo,
            'ativo' => 1,
        ]);
    }
}
