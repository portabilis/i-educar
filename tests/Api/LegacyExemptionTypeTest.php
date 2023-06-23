<?php

namespace Tests\Api;

use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyExemptionTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyExemptionTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_dispensa_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_dispensa_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyExemptionTypeFactory::new()->create();

        $editType = LegacyExemptionTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_tipo_dispensa' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_dispensa_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_dispensa_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_tipo_dispensa' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
            'ativo' => 1,
        ]);
    }
}
