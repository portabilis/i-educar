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

    // Cria uma LegacySchoolGrade com bloqueio configurado
    private function createSchoolGrade(int $bloquearCadastro)
    {
        return LegacySchoolGrade::create([
            'ref_cod_serie' => 1,
            'ref_cod_escola' => 1,
            'bloquear_cadastro_turma_para_serie_com_vagas' => $bloquearCadastro,
        ]);
    }

    // Cria uma LegacySchoolClass com max alunos e nome da turma
    private function createSchoolClass(int $maxAlunos, string $nomeTurma)
    {
        return LegacySchoolClass::create([
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'max_aluno' => $maxAlunos,
            'nm_turma' => $nomeTurma,
        ]);
    }

    // Retorna objeto valor para teste, com cod_turma opcional
    private function getValue($codTurma = null)
    {
        return (object) [
            'ref_ref_cod_escola' => 1,
            'ref_ref_cod_serie' => 1,
            'turma_turno_id' => 1,
            'ano' => 2024,
            'cod_turma' => $codTurma,
        ];
    }

    public function testCadastroComBloqueioEVagasExistentes()
    {
        $schoolGrade = $this->createSchoolGrade(1);
        $schoolClass = $this->createSchoolClass(30, 'Turma A');

        // Insere 20 alunos para simular ocupação
        for ($i = 0; $i < 20; $i++) {
            $schoolClass->students()->create(['name' => 'Aluno ' . $i]);
        }

        $value = $this->getValue();

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertFalse($result);
        $this->assertStringContainsString('vagas em aberto', $rule->message());
    }

    public function testCadastroComTurmaPreenchida()
    {
        $schoolGrade = $this->createSchoolGrade(1);
        $schoolClass = $this->createSchoolClass(30, 'Turma B');

        // Insere 30 alunos para turma cheia
        for ($i = 0; $i < 30; $i++) {
            $schoolClass->students()->create(['name' => 'Aluno ' . $i]);
        }

        $value = $this->getValue();

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }

    public function testCadastroComSerieSemBloqueio()
    {
        $schoolGrade = $this->createSchoolGrade(0);
        $value = $this->getValue();

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }

    public function testCadastroComSerieInexistente()
    {
        // Não cria LegacySchoolGrade para simular inexistente
        $value = $this->getValue();

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }

    public function testEdicaoDeTurma()
    {
        $value = $this->getValue(10); // cod_turma = 10 simula edição

        $rule = new CanCreateSchoolClass();
        $result = $rule->passes('turma', $value);

        $this->assertTrue($result);
    }
}