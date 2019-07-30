<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyEvaluationRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;

class NaoContinuadaMediaPresencaSemRecuperacao extends TestCase
{
    use DiarioApiFakeDataTestTrait, DiarioApiRequestTestTrait, DatabaseTransactions;

    private $enrollment;

    public function setUp(): void
    {
        parent::setUp();
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

    public function testReprovacaoPorMediaAposTodasFaltasENotasLancadas()
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
                $response = $this->postScore($this->enrollment, $discipline->id, $stage, '5');
            }
            $this->assertEquals('Retido', $response->situacao);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(2, $registration->refresh()->aprovado);
    }

    public function testReprovacaoPorFaltaAposTodasFaltasENotasLancadas()
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
                $this->postAbsence($this->enrollment, $discipline->id, $stage, '50');
                $response = $this->postScore($this->enrollment, $discipline->id, $stage, '9');
            }
            $this->assertEquals('Retido', $response->situacao);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(14, $registration->refresh()->aprovado);
    }

    public function testCursandoAposRemocaoDeNotaEmEtapaConcluinte()
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
                $response = $this->postScore($this->enrollment, $discipline->id, $stage, '7');
            }
            $this->assertEquals('Aprovado', $response->situacao);
            $response = $this->deleteScore($this->enrollment, $discipline->id, 4);
            $this->assertEquals('Cursando', $response->situacao);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(3, $registration->refresh()->aprovado);
    }

    public function testRemocaoDeNotaEmEtapaNaoConcluinte()
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
                $response = $this->postScore($this->enrollment, $discipline->id, $stage, '7');
            }
            $this->assertEquals('Aprovado', $response->situacao);
            $response = $this->deleteScore($this->enrollment, $discipline->id, 2);
            $this->assertTrue($response->any_error_msg);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }

    public function testCursandoAposRemocaoDeFaltaEmEtapaConcluinte()
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
                $response = $this->postScore($this->enrollment, $discipline->id, $stage, '7');
            }
            $this->assertEquals('Aprovado', $response->situacao);
            $response = $this->deleteAbsence($this->enrollment, $discipline->id, 4);
            $this->assertEquals('Cursando', $response->situacao);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(3, $registration->refresh()->aprovado);
    }

    public function testRemocaoDeFaltaEmEtapaNaoConcluinte()
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
            $response = $this->deleteAbsence($this->enrollment, $discipline->id, 2);
            $this->assertTrue($response->any_error_msg);
        }
        $registration = $this->enrollment->registration;
        $this->assertEquals(1, $registration->refresh()->aprovado);
    }
}
