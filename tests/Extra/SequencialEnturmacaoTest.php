<?php

namespace Tests\Extra;

use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SequencialEnturmacao;
use Tests\TestCase;

class SequencialEnturmacaoTest extends TestCase
{
    use DatabaseTransactions;

    public function testSequencialEqualsOneWhenRelocationDateIsNull()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        $registration = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);

        $sequencialEnturmacao = new SequencialEnturmacao($registration->cod_matricula, $schoolClass->cod_turma, date('Y-m-d'));
        $sequencial = $sequencialEnturmacao->ordenaSequencialNovaMatricula();
        $this->assertEquals(1, $sequencial);
    }

    public function testSequencialEqualsThreeWhenRelocationDateIsNull()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        $registration1 = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);
        $registration2 = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);
        $registration3 = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass->cod_turma,
            'ref_cod_matricula' => $registration1->cod_matricula,
            'sequencial_fechamento' => 1
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass->cod_turma,
            'ref_cod_matricula' => $registration2->cod_matricula,
            'sequencial_fechamento' => 2
        ]);

        //Garante que o aluno será o último
        $registration1->student->individual->person->nome = 'Aaa';
        $registration1->student->individual->person->save();
        $registration2->student->individual->person->nome = 'Bbb';
        $registration2->student->individual->person->save();
        $registration3->student->individual->person->nome = 'Zzz';
        $registration3->student->individual->person->save();
        $sequencialEnturmacao = new SequencialEnturmacao($registration3->cod_matricula, $schoolClass->cod_turma, date('Y-m-d'));
        $sequencial = $sequencialEnturmacao->ordenaSequencialNovaMatricula();
        $this->assertEquals(3, $sequencial);
    }
}
