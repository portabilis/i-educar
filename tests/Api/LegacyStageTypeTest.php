<?php

namespace Tests\Api;

use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyStageTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyStageTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'num_etapas' => $type->num_etapas,
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
            'num_meses' => $type->num_meses,
            'num_semanas' => $type->num_semanas,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_modulo_cad.php', $payload)
            ->assertRedirectContains('educar_modulo_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'num_etapas' => $type->num_etapas,
            'descricao' => $type->descricao,
            'num_meses' => $type->num_meses,
            'num_semanas' => $type->num_semanas,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyStageTypeFactory::new()->create();

        $editType = LegacyStageTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_modulo' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'num_etapas' => $typeOrignal->num_etapas,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
            'num_meses' => $editType->num_meses,
            'num_semanas' => $editType->num_semanas,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_modulo_cad.php', $payload)
            ->assertRedirectContains('educar_modulo_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_modulo' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'ref_usuario_cad' => $typeOrignal->ref_usuario_cad,
            'nm_tipo' => $editType->nm_tipo,
            'num_etapas' => $typeOrignal->num_etapas,
            'descricao' => $editType->descricao,
            'num_meses' => $editType->num_meses,
            'num_semanas' => $editType->num_semanas,
            'ativo' => 1,
        ]);
    }
}
