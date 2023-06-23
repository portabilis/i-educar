<?php

namespace Tests\Api;

use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyRegimeTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyRegimeTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_regime_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_regime_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyRegimeTypeFactory::new()->create();

        $editType = LegacyRegimeTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_tipo_regime' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_regime_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_regime_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_tipo_regime' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'ativo' => 1,
        ]);
    }
}
