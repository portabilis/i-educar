<?php

namespace Tests\Feature;

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SequencialEnturmacao;
use Tests\TestCase;

class SequencialEnturmacaoTest extends TestCase
{
    use DatabaseTransactions;

    public function testSequencialEqualsOneWhenRelocationDateIsNull()
    {
        $schoolClass = factory(LegacySchoolClass::class)->create();
        $registration = factory(LegacyRegistration::class)->create([
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
        $schoolClass = factory(LegacySchoolClass::class)->create();
        $registration1 = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);
        $registration2 = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);
        $registration3 = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_curso' => $schoolClass->ref_cod_curso,
        ]);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass->cod_turma,
            'ref_cod_matricula' => $registration1->cod_matricula,
            'sequencial_fechamento' => 1
        ]);

        factory(LegacyEnrollment::class)->create([
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
