<?php

namespace Tests\Api;

use Database\Factories\LegacyQualificationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyQualificationTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyQualificationFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_habilitacao_cad.php', $payload)
            ->assertRedirectContains('educar_habilitacao_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
            'ativo' => 1
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyQualificationFactory::new()->create();

        $editType = LegacyQualificationFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_habilitacao' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_habilitacao_cad.php', $payload)
            ->assertRedirectContains('educar_habilitacao_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_habilitacao' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
            'ativo' => 1
        ]);
    }
}
