<?php

namespace Tests\Api;

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyDisciplinaryOccurrenceTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $type = LegacyDisciplinaryOccurrenceTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
            'max_ocorrencias' => $type->max_ocorrencias,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_ocorrencia_disciplinar_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_ocorrencia_disciplinar_lst.php');

        $this->assertDatabaseHas($type, [
            'ref_cod_instituicao' => $type->ref_cod_instituicao,
            'ref_usuario_cad' => $user->getKey(),
            'nm_tipo' => $type->nm_tipo,
            'descricao' => $type->descricao,
            'max_ocorrencias' => $type->max_ocorrencias,
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $typeOrignal = LegacyDisciplinaryOccurrenceTypeFactory::new()->create();

        $editType = LegacyDisciplinaryOccurrenceTypeFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_tipo_ocorrencia_disciplinar' => $typeOrignal->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
            'max_ocorrencias' => $editType->max_ocorrencias,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_tipo_ocorrencia_disciplinar_cad.php', $payload)
            ->assertRedirectContains('educar_tipo_ocorrencia_disciplinar_lst.php');

        $this->assertDatabaseHas($editType, [
            'cod_tipo_ocorrencia_disciplinar' => $typeOrignal->getKey(),
            'ref_usuario_exc' => $user->getKey(),
            'ref_cod_instituicao' => $editType->ref_cod_instituicao,
            'nm_tipo' => $editType->nm_tipo,
            'descricao' => $editType->descricao,
            'max_ocorrencias' => $editType->max_ocorrencias,
            'ativo' => 1,
        ]);
    }
}
