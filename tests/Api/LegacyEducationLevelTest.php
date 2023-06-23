<?php

namespace Tests\Api;

use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyEducationLevelTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyEducationLevelFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_nivel' => $type->nm_nivel,
            'descricao' => $type->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_nivel_ensino_cad.php', $payload)
            ->assertRedirectContains('educar_nivel_ensino_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_nivel' => $type->nm_nivel,
            'descricao' => $type->descricao,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyEducationLevelFactory::new()->create();

        $editType = LegacyEducationLevelFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_nivel_ensino' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_nivel' => $editType->nm_nivel,
            'descricao' => $editType->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_nivel_ensino_cad.php', $payload)
            ->assertRedirectContains('educar_nivel_ensino_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_nivel_ensino' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_nivel' => $editType->nm_nivel,
            'descricao' => $editType->descricao,
            'ativo' => 1,
        ]);
    }
}
