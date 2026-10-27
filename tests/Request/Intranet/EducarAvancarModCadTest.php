<?php

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolAcademicYear;
use App\Setting;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StudentInepFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

/**
 * A rematrícula automática: pega os alunos de uma turma do ano anterior e os
 * matricula no ano de destino, os aprovados na série seguinte da sequência e
 * os reprovados na mesma série.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    // O ano de destino (2030) está em andamento na escola; a turma é do ano anterior.
    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->user->institution;
    $this->second = $this->scenario->grade('Segundo Ano');

    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $this->scenario->grade, 'ref_serie_destino' => $this->second]);

    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma de 2029', 'ano' => 2029]);

    $this->student = fn (string $name, int $status, array $registration = []) => $this->scenario->enrolled(
        $name,
        $this->schoolClass,
        [],
        $registration + ['ano' => 2029, 'aprovado' => $status]
    )->registration;

    $this->url = '/intranet/educar_avancar_mod_cad.php';

    $this->send = fn (array $overrides = []) => $this->post($this->url, LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'ano' => 2029,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'ano_letivo' => 2030,
        'data_matricula' => '10/02/2030',
    ]));

    $this->new = fn ($old) => LegacyRegistration::query()->where('ref_cod_aluno', $old->ref_cod_aluno)->where('ano', 2030)->first();
});

test('rematricula os aprovados na série seguinte e os reprovados na mesma série', function () {
    $approved = ($this->student)('Ana Alves', App_Model_MatriculaSituacao::APROVADO);
    $withDependency = ($this->student)('Bruno Barros', App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA);
    $byBoard = ($this->student)('Carla Campos', App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO);
    $reproved = ($this->student)('Davi Dias', App_Model_MatriculaSituacao::REPROVADO);
    $byAbsence = ($this->student)('Elisa Esteves', App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS);
    $ongoing = ($this->student)('Fábio Farias', App_Model_MatriculaSituacao::EM_ANDAMENTO);

    ($this->send)()
        ->assertOk()
        ->assertSee('foram rematriculados com sucesso em 2030')
        // As listas de nomes vão em campos ocultos, codificadas para a janela que as mostra.
        ->assertSee("name='alunos_rematriculados' id='alunos_rematriculados' type='hidden' value='" . urlencode('ANA ALVES,BRUNO BARROS,CARLA CAMPOS,DAVI DIAS,ELISA ESTEVES') . "'", false);

    foreach ([$approved, $withDependency, $byBoard] as $old) {
        expect(($this->new)($old))
            ->ref_ref_cod_serie->toBe($this->second->getKey())
            ->ref_cod_curso->toBe($this->scenario->course->getKey())
            ->ref_ref_cod_escola->toBe($this->scenario->school->getKey())
            ->aprovado->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO)
            ->ultima_matricula->toBe(1)
            ->ref_usuario_cad->toBe($this->user->getKey())
            ->and($old->refresh()->ultima_matricula)->toBe(0);
    }

    foreach ([$reproved, $byAbsence] as $old) {
        expect(($this->new)($old)->ref_ref_cod_serie)->toBe($this->scenario->grade->getKey());
    }

    expect(($this->new)($approved)->data_matricula->format('Y-m-d'))->toBe('2030-02-10')
        // Quem ainda está cursando fica de fora.
        ->and(($this->new)($ongoing))->toBeNull()
        ->and($ongoing->refresh()->ultima_matricula)->toBe(1);
});

test('com a configuração ligada, rematricula também quem ainda está cursando, na série seguinte', function () {
    Setting::query()->updateOrCreate(['key' => 'legacy.app.rematricula.permitir_cursando'], ['value' => '1']);

    $ongoing = ($this->student)('Fábio Farias', App_Model_MatriculaSituacao::EM_ANDAMENTO);

    ($this->send)()->assertOk()->assertSee('foram rematriculados com sucesso em 2030');

    expect(($this->new)($ongoing)->ref_ref_cod_serie)->toBe($this->second->getKey());
});

test('sem turma escolhida, rematricula os alunos de todas as turmas da série', function () {
    $here = ($this->student)('Ana Alves', App_Model_MatriculaSituacao::REPROVADO);

    $other = $this->scenario->enrolled(
        'Bruno Barros',
        $this->scenario->schoolClass(['nm_turma' => 'Outra turma de 2029', 'ano' => 2029]),
        [],
        ['ano' => 2029, 'aprovado' => App_Model_MatriculaSituacao::REPROVADO]
    )->registration;

    ($this->send)(['ref_cod_turma' => ''])->assertOk()->assertSee('foram rematriculados com sucesso em 2030');

    expect(($this->new)($here))->not->toBeNull()
        ->and(($this->new)($other))->not->toBeNull();
});

test('não rematricula quem saiu da escola, e avisa', function () {
    ($this->student)('Ana Alves', App_Model_MatriculaSituacao::APROVADO);
    $left = ($this->student)('Bruno Barros', App_Model_MatriculaSituacao::APROVADO, ['saida_escola' => true]);

    ($this->send)()
        ->assertOk()
        ->assertSee('Alguns alunos não foram rematriculados, pois possuem saída na escola.')
        ->assertSee("name='alunos_com_saida' id='alunos_com_saida' type='hidden' value='" . urlencode('BRUNO BARROS') . "'", false);

    expect(($this->new)($left))->toBeNull();
});

test('não rematricula de novo quem já tem matrícula no ano de destino', function () {
    $approved = ($this->student)('Ana Alves', App_Model_MatriculaSituacao::APROVADO);

    $this->scenario->registration(['ref_cod_aluno' => $approved->ref_cod_aluno, 'ref_ref_cod_serie' => $this->second]);

    ($this->send)()->assertOk()->assertSee('Os alunos desta série já encontram-se rematriculados');

    expect(LegacyRegistration::query()->where('ref_cod_aluno', $approved->ref_cod_aluno)->where('ano', 2030)->count())->toBe(1);
});

test('avisa quando a turma não tem alunos aprovados nem reprovados no ano anterior', function () {
    ($this->student)('Ana Alves', App_Model_MatriculaSituacao::EM_ANDAMENTO);

    ($this->send)()
        ->assertOk()
        ->assertSee('Nenhum aluno rematriculado. Certifique-se que a turma possui alunos aprovados ou reprovados em 2029.');
});

test('com a rematrícula de cursando ligada, avisa quando a turma só tem alunos transferidos', function () {
    Setting::query()->updateOrCreate(['key' => 'legacy.app.rematricula.permitir_cursando'], ['value' => '1']);

    ($this->student)('Ana Alves', App_Model_MatriculaSituacao::TRANSFERIDO);

    ($this->send)()
        ->assertOk()
        ->assertSee('Nenhum aluno rematriculado. Certifique-se que a turma possui alunos aprovados ou reprovados em 2029.');
});

test('a ação de editar não faz nada', function () {
    $approved = ($this->student)('Ana Alves', App_Model_MatriculaSituacao::APROVADO);

    ($this->send)(['tipoacao' => 'Editar']);

    expect(($this->new)($approved))->toBeNull();
});

test('recusa tudo quando a série exige INEP e algum aluno não tem', function () {
    $this->scenario->grade->update(['exigir_inep' => true]);

    $with = ($this->student)('Ana Alves', App_Model_MatriculaSituacao::APROVADO);
    $without = ($this->student)('Bruno Barros', App_Model_MatriculaSituacao::APROVADO);

    StudentInepFactory::new()->create(['student_id' => $with->ref_cod_aluno]);

    ($this->send)()
        ->assertOk()
        ->assertSee('Não foi possível realizar a rematrícula, pois alguns alunos não possuem o INEP cadastrado.')
        ->assertSee("name='alunos_sem_inep' id='alunos_sem_inep' type='hidden' value='" . urlencode('BRUNO BARROS') . "'", false);

    expect(($this->new)($with))->toBeNull()
        ->and(($this->new)($without))->toBeNull();
});

test('avisa quando a série não tem série seguinte na sequência', function () {
    $third = $this->scenario->grade('Terceiro Ano');
    $schoolClass = $this->scenario->schoolClass(['ano' => 2029, 'ref_ref_cod_serie' => $third]);

    $this->scenario->enrolled('Ana Alves', $schoolClass, [], ['ano' => 2029, 'aprovado' => App_Model_MatriculaSituacao::APROVADO, 'ref_ref_cod_serie' => $third]);

    ($this->send)(['ref_cod_serie' => $third->getKey(), 'ref_cod_turma' => $schoolClass->getKey()])
        ->assertOk()
        ->assertSee('Não foi possível obter a próxima série da sequência de enturmação');
});

test('avisa quando a série seguinte não está configurada na escola', function () {
    $third = $this->scenario->grade('Terceiro Ano');
    $loose = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->scenario->course, 'nm_serie' => 'Quarto Ano']);

    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $third, 'ref_serie_destino' => $loose]);

    $schoolClass = $this->scenario->schoolClass(['ano' => 2029, 'ref_ref_cod_serie' => $third]);

    $this->scenario->enrolled('Ana Alves', $schoolClass, [], ['ano' => 2029, 'aprovado' => App_Model_MatriculaSituacao::APROVADO, 'ref_ref_cod_serie' => $third]);

    ($this->send)(['ref_cod_serie' => $third->getKey(), 'ref_cod_turma' => $schoolClass->getKey()])
        ->assertOk()
        ->assertSee('A série de destino não está configurada na escola.');
});

test('recusa o ano de destino anterior ao atual', function () {
    ($this->send)(['ano' => 2031])->assertOk()->assertSee('O ano de destino (2030) deve ser maior ou igual que o atual (2031).');
});

test('recusa quando o ano de destino não está em andamento na escola', function () {
    LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->scenario->school->getKey())->update(['andamento' => 0]);

    ($this->send)()->assertOk()->assertSee('letivo aberto para 2030.');
});

test('recusa a data anterior ao início do ano letivo, a não ser que a instituição permita', function () {
    $approved = ($this->student)('Ana Alves', App_Model_MatriculaSituacao::APROVADO);

    ($this->send)(['data_matricula' => '15/01/2030'])->assertOk()->assertSee('A data da matrícula deve ser posterior ao dia 01/02/2030.');

    expect(($this->new)($approved))->toBeNull();

    $this->institution->update(['permitir_matricula_fora_periodo_letivo' => true]);

    ($this->send)(['data_matricula' => '15/01/2030'])->assertOk()->assertSee('foram rematriculados com sucesso em 2030');

    expect(($this->new)($approved)->data_matricula->format('Y-m-d'))->toBe('2030-01-15');
});

test('volta para o início da escola quando o usuário só pode visualizar a rematrícula', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(845, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_index.php');
});
