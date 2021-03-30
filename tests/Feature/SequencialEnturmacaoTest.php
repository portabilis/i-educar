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

    public function testSequencialEqualsOne()
    {
        $shollClass = factory(LegacySchoolClass::class)->create();
        $registration = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $shollClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $shollClass->ref_ref_cod_escola,
            'ref_cod_curso' => $shollClass->ref_cod_curso,
        ]);

        $sequencialEnturmacao = new SequencialEnturmacao($registration->cod_matricula, $shollClass->cod_turma, '2021-02-10');
        $sequencial = $sequencialEnturmacao->ordenaSequencialNovaMatricula();
        $this->assertEquals(1, $sequencial);
    }

    public function testSequencialEqualsThree()
    {
        $shollClass = factory(LegacySchoolClass::class)->create();
        $registration1 = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $shollClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $shollClass->ref_ref_cod_escola,
            'ref_cod_curso' => $shollClass->ref_cod_curso,
        ]);
        $registration2 = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $shollClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $shollClass->ref_ref_cod_escola,
            'ref_cod_curso' => $shollClass->ref_cod_curso,
        ]);
        $registration3 = factory(LegacyRegistration::class)->create([
            'ref_ref_cod_serie' => $shollClass->ref_ref_cod_serie,
            'ref_ref_cod_escola' => $shollClass->ref_ref_cod_escola,
            'ref_cod_curso' => $shollClass->ref_cod_curso,
        ]);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $shollClass->cod_turma,
            'ref_cod_matricula' => $registration1->cod_matricula,
            'sequencial_fechamento' => 1
        ]);

        factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $shollClass->cod_turma,
            'ref_cod_matricula' => $registration2->cod_matricula,
            'sequencial_fechamento' => 2
        ]);

        //Garante que o aluno será o último
        $registration3->student->individual->person->nome = 'zzzz';
        $registration3->student->individual->person->save();
        $sequencialEnturmacao = new SequencialEnturmacao($registration3->cod_matricula, $shollClass->cod_turma, '2021-02-10');
        $sequencial = $sequencialEnturmacao->ordenaSequencialNovaMatricula();
        $this->assertEquals(3, $sequencial);
    }
}
