<?php

namespace Tests\Api;

use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyCopySchoolClassTeacherTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testCopySchoolClassTeacher(): void
    {
        $this->loginWithFirstUser();

        $instituicao = LegacyInstitutionFactory::new()->unique()->make();
        $turmaAnterior = LegacySchoolClassFactory::new()->create([
            'ref_cod_instituicao' => $instituicao->getKey(),
            'ano' => now()->year - 1,
        ]);
        $vinculo = LegacySchoolClassTeacherFactory::new()->create([
            'turma_id' => $turmaAnterior->getKey(),
            'ano' => now()->year - 1,
        ]);

        $turmaAtual = LegacySchoolClassFactory::new()->create([
            'ref_cod_instituicao' => $instituicao->getKey(),
        ]);

        $request = [
            'tipoacao' => 'Novo',
        ];

        $data = [
            'cod_turma' => $turmaAtual->getKey(),
            'ref_cod_turma_origem' => $turmaAnterior->getKey(),
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/copia_vinculos_servidores_cad.php', $payload)
            ->assertRedirectContains('educar_turma_det.php?cod_turma=' . $turmaAtual->getKey());

        $this->assertDatabaseHas($vinculo, [
            'ano' => now()->year,
            'turma_id' => $turmaAtual->getKey(),
            'instituicao_id' => $instituicao->getKey(),
            'servidor_id' => $vinculo->servidor_id,
            'funcao_exercida' => $vinculo->funcao_exercida,
            'tipo_vinculo' => $vinculo->tipo_vinculo,
        ]);
    }
}
