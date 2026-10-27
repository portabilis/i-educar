<?php

use App\Events\RegistrationEvent;
use App\Exceptions\Registration\RegistrationException;
use App\Exceptions\Transfer\TransferException;
use App\Jobs\UpdateBlockedSituationRegistration;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolGrade;
use App\Setting;
use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PersonHasPlaceFactory;
use Database\Factories\PlaceFactory;
use Database\Factories\StudentInepFactory;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

/**
 * As regras que o formulário de matrícula confere antes de gravar, na ordem
 * em que a tela as aplica, cada uma com a recusa e, quando há, a condição que
 * a libera.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->user->institution;

    // A factory põe o fechamento e a data-base no dia de hoje; sem isto as
    // regras de data dependeriam do dia em que a suíte roda.
    $this->institution->update(['data_fechamento' => null, 'data_base_matricula' => null]);

    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'max_aluno' => 30]);
    $this->student = $this->scenario->student('Ana Alves');
    $this->url = '/intranet/educar_matricula_cad.php?ref_cod_aluno=' . $this->student->getKey();

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'data_matricula' => '01/03/2030',
    ]);

    $this->count = fn () => LegacyRegistration::query()->where('ref_cod_aluno', $this->student->getKey())->count();

    // Envia a matrícula e confere que foi recusada com a mensagem, sem gravar nada.
    $this->refused = function (string $message, array $overrides = []) {
        $payload = ($this->payload)($overrides);
        $before = ($this->count)();

        $this->post($this->url, $payload)->assertOk()->assertSee($message, false);

        expect(($this->count)())->toBe($before);
    };

    // Envia a matrícula e confere que foi aceita.
    $this->accepted = function (array $overrides = []) {
        $payload = ($this->payload)($overrides);
        $before = ($this->count)();

        $this->post($this->url, $payload)->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey());

        expect(($this->count)())->toBe($before + 1);
    };

    $this->registration = fn (array $attributes = []) => $this->scenario->registration($attributes + ['ref_cod_aluno' => $this->student]);
});

// A tela abre uma transação ao gravar e, nas recusas, devolve o formulário
// sem desfazê-la; sem isto o teste seguinte ficaria esperando um bloqueio.
afterEach(function () {
    while (DB::transactionLevel() > 1) {
        DB::rollBack();
    }
});

test('recusa quando o curso não permite novas matrículas', function () {
    $this->scenario->course->update(['bloquear_novas_matriculas' => true]);

    ($this->refused)('Não foi possível realizar a matrícula, o curso selecionado está configurado para não permitir a realização de novas matrículas.');
});

test('recusa o aluno inativo', function () {
    $payload = ($this->payload)();

    $this->student->update(['ativo' => 0]);

    $this->post($this->url, $payload)->assertOk()->assertSee('Não é possível matricular alunos inativos ou inexistentes.');

    expect(($this->count)())->toBe(0);
});

test('recusa a matrícula depois da data de fechamento da instituição', function () {
    $this->institution->update(['data_fechamento' => '1990-06-30']);

    ($this->refused)('Não é possível matricular alunos após a data de fechamento.', ['data_matricula' => '01/08/2030']);
    ($this->accepted)(['data_matricula' => '30/06/2030']);
});

test('recusa a dependência além do limite da regra de avaliação da série', function () {
    $this->scenario->evaluationRule(['qtd_matriculas_dependencia' => 1]);

    ($this->registration)(['dependencia' => true, 'ref_ref_cod_serie' => $this->scenario->grade('Segundo Ano')]);

    ($this->refused)('A regra desta série limita a quantidade de matrículas de dependência para 1.', ['dependencia' => 'on']);
});

test('recusa a segunda dependência na mesma escola e série', function () {
    $this->scenario->evaluationRule(['qtd_matriculas_dependencia' => 2]);

    ($this->registration)(['dependencia' => true]);

    ($this->refused)('Esse aluno já tem uma matrícula de dependência nesta escola e série.', ['dependencia' => 'on']);
});

// Defeito conhecido: para o aluno falecido a tela só guarda a mensagem e segue
// adiante, sem interromper; a matrícula é gravada.
test('defeito: matricula o aluno falecido', function () {
    DB::table('cadastro.fisica')->where('idpes', $this->student->ref_idpes)->update(['falecido' => true]);

    ($this->accepted)();
});

test('com o bloqueio de série fora da sequência, só aceita a série seguinte à da última matrícula aprovada', function () {
    $this->institution->update(['bloqueia_matricula_serie_nao_seguinte' => true]);

    $second = $this->scenario->grade('Segundo Ano');
    $third = $this->scenario->grade('Terceiro Ano');

    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $this->scenario->grade, 'ref_serie_destino' => $second]);

    ($this->registration)(['ano' => 2029, 'aprovado' => App_Model_MatriculaSituacao::APROVADO]);

    ($this->refused)('Não é possível matricular alunos em séries fora da sequência de enturmação.', ['ref_cod_serie' => $third->getKey()]);
    ($this->accepted)(['ref_cod_serie' => $second->getKey()]);
});

test('com o bloqueio de série fora da sequência, o reprovado só pode repetir a mesma série', function () {
    $this->institution->update(['bloqueia_matricula_serie_nao_seguinte' => true]);

    $second = $this->scenario->grade('Segundo Ano');

    ($this->registration)(['ano' => 2029, 'aprovado' => App_Model_MatriculaSituacao::REPROVADO]);

    ($this->refused)('Não é possível matricular alunos em séries fora da sequência de enturmação.', ['ref_cod_serie' => $second->getKey()]);
    ($this->accepted)();
});

test('com o bloqueio de série fora da sequência, aceita qualquer série depois de uma transferência ou em andamento', function (int $status) {
    $this->institution->update(['bloqueia_matricula_serie_nao_seguinte' => true]);

    ($this->registration)(['ano' => 2029, 'aprovado' => $status]);

    $refused = $status === App_Model_MatriculaSituacao::ABANDONO;

    $refused
        ? ($this->refused)('Não é possível matricular alunos em séries fora da sequência de enturmação.', ['ref_cod_serie' => $this->scenario->grade('Segundo Ano')->getKey()])
        : ($this->accepted)(['ref_cod_serie' => $this->scenario->grade('Segundo Ano')->getKey()]);
})->with([
    'transferido' => App_Model_MatriculaSituacao::TRANSFERIDO,
    // Sem regra para a situação, nenhuma série é a esperada.
    'abandono' => App_Model_MatriculaSituacao::ABANDONO,
]);

test('quando a instituição exige o mesmo bairro, recusa o aluno de outro bairro', function () {
    $this->institution->update(['matricula_apenas_bairro_escola' => true]);

    $address = fn (int $person, string $neighborhood) => PersonHasPlaceFactory::new()->create([
        'person_id' => $person,
        'place_id' => PlaceFactory::new()->create(['neighborhood' => $neighborhood]),
    ]);

    $address($this->scenario->school->ref_idpes, 'Centro');
    $place = $address($this->student->ref_idpes, 'Vila Nova');

    ($this->refused)('O aluno deve morar no mesmo bairro da escola');

    // A comparação não diferencia maiúsculas.
    $place->place->update(['neighborhood' => 'CENTRO']);

    ($this->accepted)();
});

test('com o bloqueio de enturmação sem vagas, recusa a turma lotada', function () {
    LegacySchoolGrade::query()->where('ref_cod_serie', $this->scenario->grade->getKey())->update(['bloquear_enturmacao_sem_vagas' => 1]);

    $this->schoolClass->update(['max_aluno' => 1]);
    $this->scenario->enrolled('Bruno Barros', $this->schoolClass);

    ($this->refused)('Não existem vagas disponíveis para essa turma!');
});

test('com o bloqueio de enturmação sem vagas, recusa quando a série já lotou o conjunto das turmas', function () {
    LegacySchoolGrade::query()->where('ref_cod_serie', $this->scenario->grade->getKey())->update(['bloquear_enturmacao_sem_vagas' => 1]);

    // A turma escolhida tem vaga, mas a outra turma da série está acima da capacidade somada das duas.
    $this->schoolClass->update(['max_aluno' => 2]);
    $crowded = $this->scenario->schoolClass(['nm_turma' => 'Turma Beta', 'max_aluno' => 1, 'turma_turno_id' => $this->schoolClass->turma_turno_id]);

    foreach (['Bruno Barros', 'Carla Campos', 'Davi Dias'] as $name) {
        $this->scenario->enrolled($name, $crowded);
    }

    ($this->refused)('Não existem vagas disponíveis para essa série/turno!');
});

test('a falta de vaga não impede a dependência nem a série sem o bloqueio', function (array $overrides, int $block) {
    $this->scenario->evaluationRule(['qtd_matriculas_dependencia' => 1]);

    LegacySchoolGrade::query()->where('ref_cod_serie', $this->scenario->grade->getKey())->update(['bloquear_enturmacao_sem_vagas' => $block]);

    $this->schoolClass->update(['max_aluno' => 1]);
    $this->scenario->enrolled('Bruno Barros', $this->schoolClass);

    ($this->accepted)($overrides);
})->with([
    'dependência' => [['dependencia' => 'on'], 1],
    'série sem o bloqueio' => [[], 0],
]);

test('recusa quando o ano letivo não está em andamento na escola', function () {
    LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->scenario->school->getKey())->update(['andamento' => 0]);

    ($this->refused)('O ano (letivo) selecionado não está em andamento na escola selecionada.');
});

test('recusa a segunda matrícula na mesma série', function () {
    ($this->registration)();

    ($this->refused)('Este aluno já está matriculado nesta série e curso, não é possivel matricular um aluno mais de uma vez na mesma série.');
});

test('recusa a matrícula em outra série do mesmo curso quando o curso não é multisseriado', function () {
    ($this->registration)();

    ($this->refused)("Este aluno já está matriculado no(a) 'Primeiro Ano' deste curso e escola.", ['ref_cod_serie' => $this->scenario->grade('Segundo Ano')->getKey()]);
});

test('aceita a matrícula em outra série do mesmo curso quando o curso é multisseriado', function () {
    $this->scenario->course->update(['multi_seriado' => 1]);

    ($this->registration)();

    ($this->accepted)(['ref_cod_serie' => $this->scenario->grade('Segundo Ano')->getKey()]);
});

test('em curso de atividade complementar aceita a segunda matrícula na mesma série se o curso for multisseriado', function () {
    $this->scenario->course->educationType->update(['atividade_complementar' => true]);

    ($this->registration)();

    ($this->refused)("Este aluno já está matriculado no(a) 'Primeiro Ano' deste curso e escola.");

    $this->scenario->course->update(['multi_seriado' => 1]);

    ($this->accepted)();
});

test('em curso de atividade complementar a matrícula em outra escola não impede a nova', function () {
    $this->scenario->course->educationType->update(['atividade_complementar' => true]);

    ($this->registration)(['ref_ref_cod_escola' => EnrollmentScenario::for($this->user, 2030)->school]);

    ($this->accepted)();
});

test('recusa quando o aluno tem matrícula em andamento em outra escola', function () {
    $other = EnrollmentScenario::for($this->user, 2030);
    $other->school->organization->update(['fantasia' => 'Escola Horizonte']);

    // Mesmo curso e ano, em outra escola.
    $registration = ($this->registration)(['ref_ref_cod_escola' => $other->school]);

    ($this->refused)("Este aluno já está matriculado no(a) 'Primeiro Ano' do curso 'Ensino Fundamental' na escola 'Escola Horizonte', para matricular este aluno na sua escola solicite transferência ao secretário(a) da escola citada.");

    // Com a transferência dessa matrícula já solicitada, a nova é aceita.
    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $registration, 'ref_cod_matricula_entrada' => null]);

    ($this->accepted)();
});

test('quando a série da outra matrícula não é do curso dela, a recusa sai sem o nome da série', function () {
    $other = EnrollmentScenario::for($this->user, 2030);
    $other->school->organization->update(['fantasia' => 'Escola Horizonte']);

    // Matrícula inconsistente: o curso é o daqui, a série é de outro curso.
    ($this->registration)(['ref_ref_cod_escola' => $other->school, 'ref_ref_cod_serie' => $other->grade]);

    ($this->refused)("Este aluno já está matriculado no(a) '' do curso 'Ensino Fundamental' na escola 'Escola Horizonte'");
});

test('a matrícula em outro curso de outra escola só impede a nova quando as múltiplas matrículas estão desligadas', function () {
    $other = EnrollmentScenario::for($this->user, 2030);
    $other->school->organization->update(['fantasia' => 'Escola Horizonte']);
    $other->course->update(['nm_curso' => 'Educação Infantil']);
    $other->grade->update(['nm_serie' => 'Pré-escola']);
    $other->registration(['ref_cod_aluno' => $this->student]);

    Setting::query()->updateOrCreate(['key' => 'legacy.app.matricula.multiplas_matriculas'], ['value' => '0']);

    ($this->refused)("Este aluno já está matriculado no(a) 'Pré-escola' do curso 'Educação Infantil' na escola 'Escola Horizonte'");

    Setting::query()->updateOrCreate(['key' => 'legacy.app.matricula.multiplas_matriculas'], ['value' => '1']);
    Cache::flush();

    ($this->accepted)();
});

test('quando a série bloqueia por faixa etária, recusa o aluno fora da idade', function () {
    $this->institution->update(['data_base_matricula' => '1990-03-31']);
    $this->scenario->grade->update(['idade_inicial' => 6, 'idade_final' => 7, 'bloquear_matricula_faixa_etaria' => true]);

    DB::table('cadastro.fisica')->where('idpes', $this->student->ref_idpes)->update(['data_nasc' => '2020-01-10']);

    ($this->refused)('Não foi possível realizar a matrícula, pois a idade do aluno está fora da faixa etária da série');

    DB::table('cadastro.fisica')->where('idpes', $this->student->ref_idpes)->update(['data_nasc' => '2024-01-10']);

    ($this->accepted)();
});

test('quando a série só alerta a faixa etária, pede confirmação e grava no segundo envio', function () {
    $this->institution->update(['data_base_matricula' => '1990-03-31']);
    $this->scenario->grade->update(['idade_inicial' => 6, 'idade_final' => 7, 'alerta_faixa_etaria' => true]);

    DB::table('cadastro.fisica')->where('idpes', $this->student->ref_idpes)->update(['data_nasc' => '2020-01-10']);

    $payload = ($this->payload)();

    $this->post($this->url, $payload)
        ->assertSee('A idade do aluno encontra-se fora da faixa etária pré-definida na série, deseja continuar com a matrícula?', false)
        ->assertSessionHas('reload_faixa_etaria', 1);

    expect(($this->count)())->toBe(0);

    $this->post($this->url, $payload)->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey());

    expect(($this->count)())->toBe(1);
});

test('quando a série exige INEP, recusa o aluno sem o código', function () {
    $this->scenario->grade->update(['exigir_inep' => true]);

    ($this->refused)('Não foi possível realizar matrícula, necessário inserir o INEP no cadastro do aluno.');

    StudentInepFactory::new()->create(['student_id' => $this->student]);

    ($this->accepted)();
});

test('recusa quando o ano letivo da escola não tem etapas', function () {
    LegacyAcademicYearStage::query()->where('ref_ref_cod_escola', $this->scenario->school->getKey())->delete();

    ($this->refused)('Não está definida a data de término do ano letivo.');
});

test('recusa a data anterior à saída por transferência do mesmo ano', function () {
    $transferred = ($this->registration)(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO, 'data_cancel' => '2030-03-10', 'ref_ref_cod_serie' => $this->scenario->grade('Segundo Ano')]);

    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $transferred, 'ref_cod_matricula_entrada' => null]);

    ($this->refused)(
        'O mesmo possui enturmação com data de saída anterior à data informada. Favor alterar a data de matrícula para ser superior a 10/03/2030.',
        ['data_matricula' => '05/03/2030']
    );
});

test('recusa a data anterior ao início do ano letivo, a não ser que a instituição permita', function () {
    ($this->refused)(
        'A data de matrícula precisa ser igual ou maior que a data de início do ano letivo da escola ou turma (01/02/2030).',
        ['data_matricula' => '15/01/2030']
    );

    $this->institution->update(['permitir_matricula_fora_periodo_letivo' => true]);

    ($this->accepted)(['data_matricula' => '15/01/2030']);
});

test('recusa a data posterior ao fim do ano letivo', function () {
    ($this->refused)(
        'A data de matrícula precisa ser igual ou menor que a data fim do ano letivo da escola ou turma (15/12/2030).',
        ['data_matricula' => '20/12/2030']
    );
});

test('com os campos do Censo obrigatórios, recusa a turma que conflita com o horário de outra enturmação do aluno', function () {
    $this->institution->update(['obrigar_campos_censo' => true]);
    $this->scenario->evaluationRule(['qtd_matriculas_dependencia' => 1]);

    $schedule = [
        'tipo_mediacao_didatico_pedagogico' => 1,
        'nao_informar_educacenso' => 0,
        'hora_inicial' => '07:30:00',
        'hora_final' => '11:30:00',
        'dias_semana' => [2, 3, 4],
    ];

    $this->schoolClass->update($schedule);

    // O aluno já cursa outra série da escola no mesmo horário; a nova matrícula é de dependência.
    $second = $this->scenario->grade('Segundo Ano');

    $this->scenario->enroll(
        ($this->registration)(['ref_ref_cod_serie' => $second]),
        $this->scenario->schoolClass($schedule + ['ref_ref_cod_serie' => $second])
    );

    ($this->refused)('O aluno já está matriculado em uma turma com esse horário.', ['dependencia' => 'on']);

    // Em dias diferentes não há conflito.
    $this->schoolClass->update(['dias_semana' => [5, 6]]);

    ($this->accepted)(['dependencia' => 'on']);
});

test('em curso com padrão de ano escolar ignora o semestre informado', function () {
    ($this->accepted)(['is_padrao' => 1, 'semestre' => 2]);

    expect(LegacyRegistration::query()->where('ref_cod_aluno', $this->student->getKey())->sole()->semestre)->toBeNull();
});

test('ao rematricular depois de uma transferência na mesma série e ano, bloqueia a troca de situação da nova matrícula', function () {
    Bus::fake([UpdateBlockedSituationRegistration::class]);

    ($this->registration)(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);

    ($this->accepted)();

    $new = LegacyRegistration::query()->where('ref_cod_aluno', $this->student->getKey())->orderByDesc('cod_matricula')->first();

    // O bloqueio fica até o serviço agendado, um minuto depois, liberá-lo.
    expect((bool) $new->bloquear_troca_de_situacao)->toBeTrue();

    Bus::assertDispatched(UpdateBlockedSituationRegistration::class);
});

test('avisa quando os dados da matrícula antiga não puderam ser copiados, sem desfazer a nova', function () {
    ($this->registration)(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);

    Event::listen(RegistrationEvent::class, fn () => throw new TransferException('Regras de avaliação diferentes.'));

    $this->post($this->url, ($this->payload)())
        ->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey())
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br />Não foi possível copiar os dados da matrícula antiga. Regras de avaliação diferentes.');

    expect(($this->count)())->toBe(2);
});

test('desfaz a matrícula quando o evento de matrícula a recusa', function () {
    ($this->registration)(['aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO]);

    Event::listen(RegistrationEvent::class, fn () => throw new RegistrationException('Aluno sem documentos.'));

    $this->post($this->url, ($this->payload)())
        ->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey())
        ->assertSessionHas('legacy', 'Não é possível concluir a matrícula. Aluno sem documentos.');

    expect(($this->count)())->toBe(1);
});
