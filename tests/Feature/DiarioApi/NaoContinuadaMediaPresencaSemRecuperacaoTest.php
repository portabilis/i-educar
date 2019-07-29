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
    use DiarioApiFakeDataTestTrait, DiarioApiRequestTestTrait, DatabaseTransactions;
    
    private $enrollment;
    
    public function setUp()
    {
        $roundingTable = factory(LegacyRoundingTable::class, 'numeric')->create();
        factory(LegacyValueRoundingTable::class, 10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = factory(LegacyEvaluationRule::class, 'media-presenca-sem-recuperacao')->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $this->enrollment = $this->getCommonFakeData($evaluationRule);
    }

    public function testAprovacaoAposTodasFaltasENotasLancadas()
    {
        $schoolClass = $this->enrollment->schoolClass;
        $school = $schoolClass->school;

        $this->addAcademicYearStage($schoolClass, 2);
        $this->addAcademicYearStage($schoolClass, 3);
        $this->addAcademicYearStage($schoolClass, 4);

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

        foreach ($disciplines as $discipline) {
            for ($stage = 1; $stage <= 4; $stage++) {
                $this->postAbsence($this->enrollment, $discipline->id, $stage, '3');
                $response = $this->postScore($this->enrollment, $discipline->id, $stage, '8');
            }
            $this->assertEquals('Aprovado', $response->situacao);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }
}
