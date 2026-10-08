<?php

namespace Tests\Educacenso\Migrations;

use Database\Factories\LegacySchoolClassFactory;
use iEducar\Modules\Educacenso\Migrations\UpdateEducacensoInstitutionToLayout2020;
use iEducar\Modules\Educacenso\Migrations\UpdateSchoolClassToLayout2020;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LayoutMigrationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_o_layout_2020_tira_a_atividade_complementar_extinta_das_turmas(): void
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        DB::table('pmieducar.turma')->where('cod_turma', $schoolClass->getKey())->update(['atividades_complementares' => '{13108,11002}']);

        UpdateSchoolClassToLayout2020::execute();

        $this->assertSame('{11002}', DB::table('pmieducar.turma')->where('cod_turma', $schoolClass->getKey())->value('atividades_complementares'));
    }

    public function test_o_layout_2020_atualiza_as_instituicoes_de_ensino_superior(): void
    {
        UpdateEducacensoInstitutionToLayout2020::execute();

        $this->assertTrue(DB::table('modules.educacenso_ies')->exists());
    }
}
