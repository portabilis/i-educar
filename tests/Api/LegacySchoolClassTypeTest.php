<?php

namespace Tests\Api;

use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacySchoolClassTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacySchoolClassTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
            'sgl_tipo' => $type->sgl_tipo,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_turma_tipo_cad.php', $payload)
            ->assertRedirectContains('educar_turma_tipo_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'sgl_tipo' => $type->sgl_tipo,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacySchoolClassTypeFactory::new()->create();

        $editType = LegacySchoolClassTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_turma_tipo' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'sgl_tipo' => $editType->sgl_tipo,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_turma_tipo_cad.php', $payload)
            ->assertRedirectContains('educar_turma_tipo_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_turma_tipo' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $editType->nm_tipo,
            'sgl_tipo' => $editType->sgl_tipo,
            'ativo' => 1,
        ]);
    }
}
