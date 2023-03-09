<?php

namespace Tests\Api;

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyBenefitTest extends TestCase
{
    use DatabaseTransactions;

    public function testSaveSuccess(): void
    {
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

        $this->assertDatabaseHas($benefit, [
            'nm_beneficio' => $benefit->name,
            'desc_beneficio' => $benefit->description,
            'ref_usuario_cad' => $user->getKey(),
            'ativo' => 1,
        ]);
    }

    public function testEditSuccess(): void
    {
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
    }
}
