<?php

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('save success', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);

    $benefit = LegacyBenefitFactory::new()->make();
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'nm_beneficio' => $benefit->name,
        'desc_beneficio' => $benefit->description,
    ];

    $payload = array_merge($request, $data);
    $this->post('/intranet/educar_aluno_beneficio_cad.php', $payload)
        ->assertRedirectContains('educar_aluno_beneficio_lst.php');

    $this->assertDatabaseHas($benefit->getTable(), [
        'nm_beneficio' => $benefit->name,
        'desc_beneficio' => $benefit->description,
        'ref_usuario_cad' => $user->getKey(),
        'ativo' => 1,
    ]);
});

test('edit success', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);

    $benefitOrignal = LegacyBenefitFactory::new()->create();
    $editBenefit = LegacyBenefitFactory::new()->make();

    $request = [
        'tipoacao' => 'Editar',
    ];

    $data = [
        'cod_aluno_beneficio' => $benefitOrignal->getKey(),
        'nm_beneficio' => $editBenefit->name,
        'desc_beneficio' => $editBenefit->description,
    ];

    $payload = array_merge($request, $data);
    $this->post('/intranet/educar_aluno_beneficio_cad.php', $payload)
        ->assertRedirectContains('educar_aluno_beneficio_lst.php');

    $this->assertDatabaseHas($editBenefit, [
        'cod_aluno_beneficio' => $benefitOrignal->getKey(),
        'nm_beneficio' => $editBenefit->name,
        'desc_beneficio' => $editBenefit->description,
        'ref_usuario_cad' => $user->getKey(),
        'ativo' => 1,
    ]);
});
