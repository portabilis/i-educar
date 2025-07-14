<?php

namespace Tests\Unit\Rules;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use App\Rules\CanCreateSchoolClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanCreateSchoolClassTest extends TestCase
{
    use RefreshDatabase;

    public function testCadastroComBloqueioEVagasExistentes()
    {
        $schoolGrade = LegacySchoolGrade::create([
            'ref_cod_serie' => 1,
            'ref_cod_escola' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ]);

        $schoolClass = LegacySchoolClass::create([
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'max_aluno' => 30,
            'nm_turma' => 'Turma A',
        ]);

        // Insere 20 alunos para simular ocupação
        for ($i = 0; $i < 20; $i++) {
            $schoolClass->students()->create([
                'name' => 'Aluno ' . $i,
            ]);
        }

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
        $schoolGrade = LegacySchoolGrade::create([
            'ref_cod_serie' => 1,
            'ref_cod_escola' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 1,
        ]);

        $schoolClass = LegacySchoolClass::create([
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'max_aluno' => 30,
            'nm_turma' => 'Turma B',
        ]);

        // Insere 30 alunos para simular turma cheia
        for ($i = 0; $i < 30; $i++) {
            $schoolClass->students()->create([
                'name' => 'Aluno ' . $i,
            ]);
        }

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
        $schoolGrade = LegacySchoolGrade::create([
            'ref_cod_serie' => 1,
            'ref_cod_escola' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => 0,
        ]);

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
        // Não cria nenhum LegacySchoolGrade — para simular inexistente

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
            'cod_turma' => 10,
        ];

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }