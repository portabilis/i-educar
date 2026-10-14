<?php

use Database\Factories\LegacyProjectFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class);

test('save success', function () {
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

    $this->assertDatabaseHas($project->getTable(), [
        'nome' => $project->nome,
        'observacao' => $project->observacao,
    ]);
});

test('edit success', function () {
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
});
