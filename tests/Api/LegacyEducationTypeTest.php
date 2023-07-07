<?php

namespace Tests\Api;

use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyEducationTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyEducationTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
            'atividade_complementar' => $type->atividade_complementar ? true : null,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_ensino_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_ensino_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'atividade_complementar' => $type->atividade_complementar,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyEducationTypeFactory::new()->create();

        $editType = LegacyEducationTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_tipo_ensino' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'atividade_complementar' => $editType->atividade_complementar == false ? null : 1,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_ensino_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_ensino_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_tipo_ensino' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'atividade_complementar' => $editType->atividade_complementar,
            'ativo' => 1,
        ]);
    }
}
