<?php

namespace Tests\Feature\App\Model;

use App_Model_IedFinder;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStageTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IedFinderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_quantidade_de_modulos_matricula_curso_ano_nao_padrao(): void
    {
        $turma = LegacySchoolClassFactory::new()->create();
        $etapa = LegacyStageTypeFactory::new()->create();

        foreach (range(1, 4) as $sequencial) {
            LegacySchoolClassStageFactory::new()->create([
                'ref_cod_turma' => $turma,
                'ref_cod_modulo' => $etapa,
                'sequencial' => $sequencial,
            ]);
        }

        $matricula = [
            'ref_cod_turma' => $turma->getKey(),
            'padrao_ano_escolar' => 0,
        ];

        $this->assertEquals(4, App_Model_IedFinder::getQuantidadeDeModulosMatricula(1, $matricula));
    }
}
