<?php

namespace Tests\Unit\Rules;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use App\Rules\CanCreateSchoolClass;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CanCreateSchoolClassTest extends TestCase
{
    public function testCadastroComBloqueioEVagasExistentes()
    {
        // Mocks dos Models
        $schoolGrade = \Mockery::mock(LegacySchoolGrade::class);
        $schoolGrade->bloquear_cadastro_turma_para_serie_com_vagas = 1;

        $schoolClass = \Mockery::mock(LegacySchoolClass::class);
        $schoolClass->shouldReceive('getTotalEnrolled')->andReturn(20);
        $schoolClass->max_aluno = 30;
        $schoolClass->nm_turma = 'Turma A';

        // Mock para consulta de turmas
        LegacySchoolGrade::shouldReceive('query->where->where->first')->andReturn($schoolGrade);
        LegacySchoolClass::shouldReceive('query->where->where->where->where->where->get')->andReturn(new Collection([$schoolClass]));

        // Objeto simulado para passar no passes
        $value = (object) [
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'cod_turma' => null,
        ];

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertFalse($result);
        $this->assertStringContainsString('vagas em aberto', $rule->message());
    }

    public function testCadastroComTurmaPreenchida()
    {
        $schoolGrade = \Mockery::mock(LegacySchoolGrade::class);
        $schoolGrade->bloquear_cadastro_turma_para_serie_com_vagas = 1;

        $schoolClass = \Mockery::mock(LegacySchoolClass::class);
        $schoolClass->shouldReceive('getTotalEnrolled')->andReturn(30);
        $schoolClass->max_aluno = 30;
        $schoolClass->nm_turma = 'Turma B';

        LegacySchoolGrade::shouldReceive('query->where->where->first')->andReturn($schoolGrade);
        LegacySchoolClass::shouldReceive('query->where->where->where->where->where->get')->andReturn(new Collection([$schoolClass]));

        $value = (object) [
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'cod_turma' => null,
        ];

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }

    public function testCadastroComSerieSemBloqueio()
    {
        $schoolGrade = \Mockery::mock(LegacySchoolGrade::class);
        $schoolGrade->bloquear_cadastro_turma_para_serie_com_vagas = 0;

        LegacySchoolGrade::shouldReceive('query->where->where->first')->andReturn($schoolGrade);

        $value = (object) [
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'cod_turma' => null,
        ];

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }

    public function testCadastroComSerieInexistente()
    {
        LegacySchoolGrade::shouldReceive('query->where->where->first')->andReturn(null);

        $value = (object) [
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'cod_turma' => null,
        ];

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }

    public function testEdicaoDeTurma()
    {
        $value = (object) [
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'cod_turma' => 10, // já existe, não é create
        ];

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }
}