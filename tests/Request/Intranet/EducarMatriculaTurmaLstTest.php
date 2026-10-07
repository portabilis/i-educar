<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves')]);

    $this->alfa = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $this->beta = $this->scenario->schoolClass(['nm_turma' => 'Turma Beta']);
    $this->scenario->enroll($this->registration, $this->alfa);

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_matricula_turma_lst.php?' . http_build_query($query + [
        'ref_cod_matricula' => $this->registration->getKey(),
        'ano_letivo' => 2030,
    ]));

    $this->enroll = fn ($schoolClass) => route('enrollments.enroll.create', ['registration' => $this->registration->getKey(), 'schoolClass' => $schoolClass->getKey()]);
    $this->relocate = fn ($schoolClass) => route('enrollments.relocate.create', ['registration' => $this->registration->getKey(), 'schoolClass' => $schoolClass->getKey()]);

    // Um usuário institucional com acesso só aos processos dados.
    $this->as = function (int ...$processes) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

        $factory = LegacyUserFactory::new()->institutional()->withAccess(578);

        foreach ($processes as $process) {
            $factory = $factory->withAccess($process);
        }

        $this->actingAs($factory->create(['ref_cod_instituicao' => $this->scenario->institution]));
    };
});

test('lista as turmas da série no ano, dizendo em quais a matrícula está enturmada', function () {
    $otherYear = $this->scenario->schoolClass(['nm_turma' => 'Turma de 2031', 'ano' => 2031]);

    ($this->list)()
        ->assertOk()
        ->assertSee('Selecione uma turma para enturmar ou remover a enturmação')
        ->assertSee('Escola Aurora')
        ->assertSee('Ana Alves')
        ->assertSeeInOrder(['Turma Alfa', 'Sim', 'Turma Beta', 'Não'])
        ->assertSee(($this->enroll)($this->alfa), false)
        ->assertSee(($this->enroll)($this->beta), false)
        ->assertDontSee(($this->enroll)($otherYear), false)
        ->assertSee('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey(), false);
});

test('no remanejamento só lista as turmas em que a matrícula não está, com o link de remanejar', function () {
    ($this->list)(['acao' => 'remanejar'])
        ->assertOk()
        ->assertSee('Selecione uma turma para remanejar a matrícula')
        ->assertSeeInOrder(['Alunos', 'Ana Alves', 'Matrícula', 'Remanejamento'])
        ->assertSee(($this->relocate)($this->beta), false)
        ->assertDontSee(($this->relocate)($this->alfa), false);
});

test('inclui as turmas que têm a série da matrícula como série secundária', function () {
    $second = $this->scenario->grade('Segundo Ano');
    $multi = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Multi',
        'ref_ref_cod_serie' => $second,
        'ref_ref_cod_serie_mult' => $this->scenario->grade->getKey(),
        'ref_ref_cod_escola_mult' => $this->scenario->school->getKey(),
    ]);

    ($this->list)()->assertOk()->assertSee(($this->enroll)($multi), false);
});

test('lista só a turma de série secundária quando a série não tem turma própria no ano', function () {
    $second = $this->scenario->grade('Segundo Ano');
    $multi = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Multi',
        'ref_ref_cod_serie' => $second,
        'ref_ref_cod_serie_mult' => $this->scenario->grade->getKey(),
        'ref_ref_cod_escola_mult' => $this->scenario->school->getKey(),
    ]);

    ($this->list)(['ano_letivo' => 2029])
        ->assertOk()
        ->assertSee(($this->enroll)($multi), false)
        ->assertDontSee(($this->enroll)($this->beta), false);
});

test('a quem pode enturmar mas não desenturmar, esconde a turma em que a matrícula já está', function () {
    ($this->as)(683);

    ($this->list)()
        ->assertOk()
        ->assertSee(($this->enroll)($this->beta), false)
        ->assertDontSee(($this->enroll)($this->alfa), false);
});

test('a quem só pode desenturmar mostra as duas turmas', function () {
    ($this->as)(696);

    ($this->list)()
        ->assertOk()
        ->assertSee(($this->enroll)($this->alfa), false)
        ->assertSee(($this->enroll)($this->beta), false);
});

test('manda para o início quem não tem o processo da ação', function (array $processes, array $query) {
    ($this->as)(...$processes);

    ($this->list)($query)->assertRedirect('/intranet/index.php');
})->with([
    'enturmar sem enturmação nem desenturmação' => [[695], []],
    'remanejar sem remanejamento' => [[683, 696], ['acao' => 'remanejar']],
]);

test('manda para o início o usuário de biblioteca', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->withAccess(578)->withAccess(683)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]));

    ($this->list)()->assertRedirect('/intranet/index.php');
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_matricula_turma_lst.php')->assertRedirect('educar_matricula_lst.php');
});
