<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEvaluationRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;

class NaoContinuadaMediaPresencaSemRecuperacao extends TestCase
{
    use DiarioApiFakeDataTestTrait, DiarioApiRequestTestTrait;

    public function testAprovacaoAposTodasFaltasENotasLancadas()
    {
        $roundingTable = factory(LegacyRoundingTable::class, 'numeric')->create();
        factory(LegacyValueRoundingTable::class, 10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);
    
        $evaluationRule = factory(LegacyEvaluationRule::class, 'media-presenca-sem-recuperacao')->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $enrollment = $this->getCommonFakeData($evaluationRule);
        $schoolClass = $enrollment->schoolClass;
        $school = $schoolClass->school;
    
        factory(LegacyAcademicYearStage::class)->create([
            'ref_ano' => now()->year,
            'ref_ref_cod_escola' => $school->id,
            'sequencial' => 2,
        ]);

        factory(LegacyAcademicYearStage::class)->create([
            'ref_ano' => now()->year,
            'ref_ref_cod_escola' => $school->id,
            'sequencial' => 3,
        ]);

        factory(LegacyAcademicYearStage::class)->create([
            'ref_ano' => now()->year,
            'ref_ref_cod_escola' => $school->id,
            'sequencial' => 4,
        ]);

        $discipline = factory(LegacyDiscipline::class)->create();
        $schoolClass->disciplines()->attach($discipline->id, [
            'ano_escolar_id' => $schoolClass->grade_id,
            'escola_id' => $school->id
        ]);

        factory(LegacyDisciplineAcademicYear::class)->create([
            'componente_curricular_id' => $discipline->id,
            'ano_escolar_id' => $schoolClass->grade_id,
        ]);

        $disciplines = $schoolClass->disciplines;

        foreach ($disciplines as $key => $discipline) {
            for ($stage = 1; $stage <= 4; $stage++) {
                // $this->postAbsence($enrollment, $discipline->id, $stage, '2');
                $this->postGrade($enrollment, $discipline->id, $stage, '8');
            }
        }

        $this->assertEquals('Aprovado', 'Aprovado');
    }
}
