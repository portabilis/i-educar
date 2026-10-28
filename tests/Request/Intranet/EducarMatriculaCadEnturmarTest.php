<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

/**
 * O formulário de matrícula no modo "copiar enturmações": recebe uma turma de
 * origem e matricula e enturma os alunos dela em uma turma de destino.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->user->institution;

    $this->source = $this->scenario->schoolClass(['nm_turma' => 'Turma Origem']);
    $this->target = $this->scenario->schoolClass(['nm_turma' => 'Turma Destino', 'max_aluno' => 10]);

    $this->ana = $this->scenario->enrolled('Ana Alves', $this->source, ['sequencial' => 1]);
    $this->bruno = $this->scenario->enrolled('Bruno Barros', $this->source, ['sequencial' => 1]);

    $this->url = '/intranet/educar_matricula_cad.php?ref_cod_turma_copiar_enturmacoes=' . $this->source->getKey();

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->target->getKey(),
    ]);

    $this->enrolledInTarget = fn () => LegacyEnrollment::query()->where('ref_cod_turma', $this->target->getKey())->count();
});

test('abre no modo de copiar enturmações, sem aluno', function () {
    $response = $this->get($this->url)->assertOk()->assertSee('Gravar enturmações');
    $fields = LegacyFields::from($response->getContent());

    expect($fields['tipoacao'])->toBe('Enturmar')
        ->and($fields['ref_cod_turma_copiar_enturmacoes'])->toBe((string) $this->source->getKey())
        ->and($fields['ref_cod_aluno'])->toBe('');
});

test('matricula e enturma na turma de destino os alunos da turma de origem', function () {
    $this->post($this->url, ($this->payload)())
        ->assertRedirect(route('enrollments.batch.enroll.index', ['schoolClass' => $this->target->getKey()]));

    expect(($this->enrolledInTarget)())->toBe(2);

    $registration = LegacyRegistration::query()
        ->where('ref_cod_aluno', $this->ana->registration->ref_cod_aluno)
        ->whereKeyNot($this->ana->ref_cod_matricula)
        ->sole();

    expect($registration)
        ->ref_ref_cod_escola->toBe($this->scenario->school->getKey())
        ->ref_ref_cod_serie->toBe($this->scenario->grade->getKey())
        ->ref_cod_curso->toBe($this->scenario->course->getKey())
        ->ano->toBe(2030)
        ->aprovado->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO)
        ->ativo->toBe(1)
        ->ultima_matricula->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey());

    expect(LegacyEnrollment::query()->where('ref_cod_matricula', $registration->getKey())->sole())
        ->ref_cod_turma->toBe($this->target->getKey())
        ->sequencial->toBe(1)
        ->ativo->toBe(1);
});

test('recusa quando a turma de destino não tem vagas para todos', function () {
    $this->target->update(['max_aluno' => 1]);

    $this->post($this->url, ($this->payload)())->assertOk()->assertSee('A turma não tem saldo de vagas suficiente.');

    expect(($this->enrolledInTarget)())->toBe(0);
});

// Defeito conhecido: com os campos do Censo obrigatórios, o conflito de
// horário é conferido aluno a aluno mas a cópia segue; a tela avisa o
// conflito depois de já ter matriculado e enturmado todos.
test('defeito: avisa o conflito de horário depois de já ter copiado as enturmações', function () {
    $this->institution->update(['obrigar_campos_censo' => true]);

    $schedule = [
        'tipo_mediacao_didatico_pedagogico' => 1,
        'nao_informar_educacenso' => 0,
        'hora_inicial' => '07:30:00',
        'hora_final' => '11:30:00',
        'dias_semana' => [2, 3, 4],
    ];

    $this->source->update($schedule);
    $this->target->update($schedule);

    $this->post($this->url, ($this->payload)())
        ->assertOk()
        ->assertSee('O aluno já está matriculado em uma turma com esse horário.');

    expect(($this->enrolledInTarget)())->toBe(2);
});
