<?php

use App\Models\LegacyEnrollment;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

/**
 * A tela que executa a enturmação: recebe por POST a matrícula, a turma de
 * destino, a data e, na origem, vazio (nova enturmação), uma turma
 * (transferência) ou a palavra que pede a remoção.
 *
 * Só os caminhos que dão certo são testáveis: nas recusas a tela emite um
 * alerta e segue para o `Gerar()`, que chama `exit` e encerraria a suíte.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->user->institution;
    $this->institution->update(['data_base_remanejamento' => null]);

    $this->registration = $this->scenario->registration();
    $this->alfa = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $this->beta = $this->scenario->schoolClass(['nm_turma' => 'Turma Beta']);

    $this->send = fn (array $payload) => $this->post('/intranet/educar_matricula_turma_cad.php', $payload + [
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_turma_origem' => '',
        'ref_cod_turma_destino' => $this->beta->getKey(),
        'data_enturmacao' => '10/03/2030',
    ]);

    $this->detail = 'educar_matricula_det.php?cod_matricula=' . $this->registration->getKey();

    $this->in = fn ($schoolClass) => LegacyEnrollment::query()
        ->where('ref_cod_matricula', $this->registration->getKey())
        ->where('ref_cod_turma', $schoolClass->getKey())
        ->orderByDesc('sequencial')
        ->first();
});

test('enturma a matrícula na turma escolhida, na data informada', function () {
    ($this->send)([])->assertRedirect($this->detail);

    $enrollment = ($this->in)($this->beta);

    expect($enrollment)
        ->ativo->toBe(1)
        ->sequencial->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->and($enrollment->data_enturmacao->format('Y-m-d'))->toBe('2030-03-10');
});

test('a segunda enturmação não mexe na anterior que continua ativa', function () {
    $first = $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-02-05']);

    ($this->send)([])->assertRedirect($this->detail);

    expect($first->refresh())
        ->ativo->toBe(1)
        ->and((bool) $first->remanejado)->toBeFalse()
        ->and(($this->in)($this->beta)->sequencial)->toBe(2);
});

test('ao enturmar de novo, marca como remanejada a última enturmação, já encerrada', function () {
    $closed = $this->scenario->enroll($this->registration, $this->alfa, [
        'data_enturmacao' => '2030-02-05',
        'data_exclusao' => '2030-03-01',
        'ativo' => 0,
    ]);

    ($this->send)([])->assertRedirect($this->detail);

    expect((bool) $closed->refresh()->remanejado)->toBeTrue()
        ->and($closed->ativo)->toBe(0)
        ->and(($this->in)($this->beta)->ativo)->toBe(1);
});

test('não marca a enturmação encerrada como remanejada antes da data-base de remanejamento', function () {
    $this->institution->update(['data_base_remanejamento' => now()->addYear()->format('Y-m-d')]);

    $closed = $this->scenario->enroll($this->registration, $this->alfa, [
        'data_enturmacao' => '2030-02-05',
        'data_exclusao' => '2030-03-01',
        'ativo' => 0,
    ]);

    ($this->send)([])->assertRedirect($this->detail);

    expect((bool) $closed->refresh()->remanejado)->toBeFalse()
        ->and(($this->in)($this->beta))->not->toBeNull();
});

test('transfere a enturmação: encerra a da turma de origem como remanejada e cria a da turma de destino', function () {
    $origin = $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-02-05']);

    ($this->send)(['ref_cod_turma_origem' => $this->alfa->getKey()])->assertRedirect($this->detail);

    expect($origin->refresh())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and((bool) $origin->remanejado)->toBeTrue()
        ->and($origin->data_exclusao->format('Y-m-d'))->toBe('2030-03-10')
        ->and($origin->data_enturmacao->format('Y-m-d'))->toBe('2030-02-05');

    $destination = ($this->in)($this->beta);

    expect($destination)
        ->ativo->toBe(1)
        ->sequencial->toBe(2)
        ->turno_id->toBeNull()
        ->and($destination->data_enturmacao->format('Y-m-d'))->toBe('2030-03-10');
});

test('ao transferir para uma turma integral leva o turno da enturmação de origem', function () {
    $this->beta->update(['turma_turno_id' => $this->scenario->period(4)->getKey()]);
    $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-02-05', 'turno_id' => 2]);

    ($this->send)(['ref_cod_turma_origem' => $this->alfa->getKey()])->assertRedirect($this->detail);

    expect(($this->in)($this->beta)->turno_id)->toBe(2);
});

test('remove a enturmação da turma, com a data de hoje como saída', function () {
    $enrollment = $this->scenario->enroll($this->registration, $this->beta, ['data_enturmacao' => '2030-02-05']);

    ($this->send)(['ref_cod_turma_origem' => 'remover-enturmacao-destino', 'data_enturmacao' => ''])->assertRedirect($this->detail);

    expect($enrollment->refresh())
        ->ativo->toBe(0)
        ->and($enrollment->data_exclusao->format('Y-m-d'))->toBe(now()->format('Y-m-d'))
        ->and((bool) $enrollment->remanejado)->toBeFalse();
});

test('volta para a listagem de matrículas quando aberta sem dados', function () {
    $this->get('/intranet/educar_matricula_turma_cad.php')->assertRedirect('educar_matricula_lst.php');
});

test('volta para a listagem de alunos quando a matrícula não é informada', function () {
    $this->post('/intranet/educar_matricula_turma_cad.php', ['data_enturmacao' => '10/03/2030'])
        ->assertRedirect('/intranet/educar_aluno_lst.php');
});

test('não enturma quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->send)([])->assertRedirect('educar_matricula_lst.php');

    expect(($this->in)($this->beta))->toBeNull();
});
