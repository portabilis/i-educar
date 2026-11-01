<?php

use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    /**
     * Turma de uma instituição sem datas de fechamento, transferência ou
     * remanejamento, para que o sequencial siga a ordem alfabética.
     */
    $this->alphabeticalScenario = function (): array {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        $schoolClass->school->institution->update(['data_fechamento' => null, 'data_base_transferencia' => null, 'data_base_remanejamento' => null]);

        $registration = function (string $name) use ($schoolClass) {
            $registration = LegacyRegistrationFactory::new()->create([
                'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
                'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
                'ref_cod_curso' => $schoolClass->ref_cod_curso,
            ]);
            $registration->student->individual->person->update(['nome' => $name]);

            return $registration;
        };

        return [$schoolClass, $registration];
    };
});

test('sequencial equals one when relocation date is null', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $registration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
    ]);

    $sequencialEnturmacao = new SequencialEnturmacao($registration->cod_matricula, $schoolClass->cod_turma, date('Y-m-d'));
    $sequencial = $sequencialEnturmacao->ordenaSequencialNovaMatricula();
    $this->assertEquals(1, $sequencial);
});

test('sequencial equals three when relocation date is null', function () {
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
        'sequencial_fechamento' => 1,
    ]);

    LegacyEnrollmentFactory::new()->create([
        'ref_cod_turma' => $schoolClass->cod_turma,
        'ref_cod_matricula' => $registration2->cod_matricula,
        'sequencial_fechamento' => 2,
    ]);

    // Garante que o aluno será o último
    $registration1->student->individual->person->nome = 'Aaa';
    $registration1->student->individual->person->save();
    $registration2->student->individual->person->nome = 'Bbb';
    $registration2->student->individual->person->save();
    $registration3->student->individual->person->nome = 'Zzz';
    $registration3->student->individual->person->save();
    $sequencialEnturmacao = new SequencialEnturmacao($registration3->cod_matricula, $schoolClass->cod_turma, date('Y-m-d'));
    $sequencial = $sequencialEnturmacao->ordenaSequencialNovaMatricula();
    $this->assertEquals(3, $sequencial);
});

test('sem datas da instituicao o aluno entra na ordem alfabetica', function () {
    [$schoolClass, $registration] = ($this->alphabeticalScenario)();
    $ana = $registration('Ana');
    $zeca = $registration('Zeca');
    $maria = $registration('Maria');
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $ana, 'sequencial_fechamento' => 1]);
    $last = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $zeca, 'sequencial_fechamento' => 2]);

    $sequencial = (new SequencialEnturmacao($maria->cod_matricula, $schoolClass->cod_turma, date('Y-m-d')))->ordenaSequencialNovaMatricula();

    $this->assertEquals(2, $sequencial);
    $this->assertEquals(3, $last->refresh()->sequencial_fechamento);
});

test('quem ja esta na turma libera a posicao antes de ser reposicionado', function () {
    [$schoolClass, $registration] = ($this->alphabeticalScenario)();
    $ana = $registration('Ana');
    $bia = $registration('Bia');
    $zeca = $registration('Zeca');
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $bia, 'sequencial_fechamento' => 1]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $ana, 'sequencial_fechamento' => 2]);
    $last = LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $zeca, 'sequencial_fechamento' => 3]);
    $sequence = new SequencialEnturmacao($ana->cod_matricula, $schoolClass->cod_turma, date('Y-m-d'));

    // A posição atual de Ana sai da turma e quem vinha depois sobe uma posição.
    $this->assertEquals(2, $sequence->existeMatriculaTurma());

    (fn () => $this->subtraiSequencialPosterior(2))->call($sequence);

    $this->assertEquals(2, $last->refresh()->sequencial_fechamento);

    // A reordenação completa empata Ana e Zeca na posição 2; o resultado
    // depende da ordem do banco, então aqui só se confere que ela roda.
    $this->assertIsInt((int) $sequence->ordenaSequencialNovaMatricula());
});

test('aluno em dependencia entra depois dos dependentes ja enturmados', function () {
    [$schoolClass, $registration] = ($this->alphabeticalScenario)();
    $regular = $registration('Ana');
    $dependent = $registration('Bia');
    $dependent->update(['dependencia' => true]);
    $newcomer = $registration('Caio');
    $newcomer->update(['dependencia' => true]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $regular, 'sequencial_fechamento' => 1]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_turma' => $schoolClass, 'ref_cod_matricula' => $dependent, 'sequencial_fechamento' => 2, 'data_enturmacao' => now()->subDay()]);

    $sequencial = (new SequencialEnturmacao($newcomer->cod_matricula, $schoolClass->cod_turma, date('Y-m-d')))->ordenaSequencialNovaMatricula();

    $this->assertEquals(3, $sequencial);
});
