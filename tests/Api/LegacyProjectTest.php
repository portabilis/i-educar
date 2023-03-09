<?php

namespace Tests\Api;

use Database\Factories\LegacyProjectFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyProjectTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testSaveSuccess(): void
    {
        $this->loginWithFirstUser();

        $project = LegacyProjectFactory::new()->make();

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'nome' => $project->nome,
            'observacao' => $project->observacao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_projeto_cad.php', $payload)
            ->assertRedirectContains('educar_projeto_lst.php');

        $this->assertDatabaseHas($project, [
            'nome' => $project->nome,
            'observacao' => $project->observacao,
        ]);
    }

    public function testEditSuccess(): void
    {
        $this->loginWithFirstUser();

        $projectOrignal = LegacyProjectFactory::new()->create();

        $editProject = LegacyProjectFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
        ];

        $data = [
            'cod_projeto' => $projectOrignal->getKey(),
            'nome' => $editProject->nome,
            'observacao' => $editProject->observacao,
        ];

        $payload = array_merge($request, $data);
        $this->post('/intranet/educar_projeto_cad.php', $payload)
            ->assertRedirectContains('educar_projeto_lst.php');

        $this->assertDatabaseHas($editProject, [
            'cod_projeto' => $projectOrignal->getKey(),
            'nome' => $editProject->nome,
            'observacao' => $editProject->observacao,
        ]);
    }
}
