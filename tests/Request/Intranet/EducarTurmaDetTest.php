<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);

    $this->detail = fn ($schoolClass) => $this->get('/intranet/educar_turma_det.php?cod_turma=' . $schoolClass->getKey());
});

test('mostra os dados da turma e as ações', function () {
    $schoolClass = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Alfa',
        'sgl_turma' => 'TAL',
        'max_aluno' => 32,
        'ref_cod_turma_tipo' => LegacySchoolClassTypeFactory::new()->create(['nm_tipo' => 'Tempo Integral']),
        'ref_cod_regente' => LegacyPersonFactory::new()->create(['nome' => 'Regina Regente']),
    ]);

    ($this->detail)($schoolClass)
        ->assertOk()
        ->assertSeeInOrder([
            'Ano', '2030',
            'Instituição', $this->user->institution->nm_instituicao,
            'Escola', 'Escola Aurora',
            'Multisseriada', 'Não',
            'Curso', 'Ensino Fundamental',
            'Série', 'Primeiro Ano',
            'Professor/Regente', 'Regina Regente',
            'Tipo de Turma', 'Tempo Integral',
            'Turma', 'Turma Alfa',
            'TAL',
            'Máximo de Alunos', '32',
            'Situação', 'Ativo',
        ], false)
        ->assertSee('educar_turma_cad.php?cod_turma=' . $schoolClass->getKey(), false)
        ->assertSee('Reclassificar alunos alfabeticamente')
        ->assertSee('Reclassificar alunos por data base')
        ->assertSee("educar_ordenar_alunos_turma.php?cod_turma={$schoolClass->getKey()}", false)
        ->assertSee("educar_parecer_turma_cad.php?cod_turma={$schoolClass->getKey()}", false)
        ->assertSee("copia_vinculos_servidores_cad.php?cod_turma={$schoolClass->getKey()}", false)
        ->assertSee("/new/quadro-de-horarios/{$schoolClass->getKey()}", false);
});

test('mostra a turma inativa como desativada', function () {
    ($this->detail)($this->scenario->schoolClass(['visivel' => false]))
        ->assertOk()
        ->assertSeeInOrder(['Situação', 'Desativo']);
});

test('não oferece copiar vínculos quando a turma já tem servidor vinculado no ano', function () {
    $schoolClass = $this->scenario->schoolClass();

    LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $schoolClass, 'ano' => 2030]);

    ($this->detail)($schoolClass)->assertOk()->assertDontSee('copia_vinculos_servidores_cad.php', false);
});

test('em curso com padrão de ano escolar mostra os horários e os dias da semana', function () {
    $this->scenario->course->update(['padrao_ano_escolar' => 1]);

    $schoolClass = $this->scenario->schoolClass([
        'hora_inicial' => '07:30:00',
        'hora_final' => '11:45:00',
        'hora_inicio_intervalo' => '09:30:00',
        'hora_fim_intervalo' => '09:50:00',
        'dias_semana' => [2, 4, 6],
    ]);

    ($this->detail)($schoolClass)
        ->assertOk()
        ->assertSeeInOrder([
            'Hora Inicial', '07:30',
            'Hora Final', '11:45',
            'Hora Início Intervalo', '09:30',
            'Hora Fim Intervalo', '09:50',
            'Dia da Semana', 'Segunda', 'Quarta', 'Sexta',
        ])
        ->assertDontSee('Módulo');
});

test('em curso sem padrão de ano escolar mostra as etapas da turma e os dias da semana', function () {
    $this->scenario->course->update(['padrao_ano_escolar' => 0]);

    $schoolClass = $this->scenario->schoolClass(['hora_inicial' => '07:30:00', 'dias_semana' => [3, 5]]);
    $stageType = LegacyStageTypeFactory::new()->create(['nm_tipo' => 'Semestre']);

    foreach ([1 => ['2030-02-01', '2030-06-30', 100], 2 => ['2030-08-01', '2030-12-15', 95]] as $sequence => [$start, $end, $days]) {
        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_modulo' => $stageType,
            'sequencial' => $sequence,
            'data_inicio' => $start,
            'data_fim' => $end,
            'dias_letivos' => $days,
        ]);
    }

    ($this->detail)($schoolClass)
        ->assertOk()
        ->assertSeeInOrder([
            'Módulo',
            'Semestre', '01/02/2030', '30/06/2030', '100',
            'Semestre', '01/08/2030', '15/12/2030', '95',
            'Dia da Semana', 'Terça', 'Quinta',
        ])
        ->assertDontSee('Hora Inicial');
});

test('lista os componentes curriculares da série com a carga horária', function () {
    $this->scenario->discipline('Artes Visuais', 120);
    $this->scenario->discipline('Botânica', 80);

    ($this->detail)($this->scenario->schoolClass())
        ->assertOk()
        ->assertSee('Componentes curriculares')
        ->assertSeeInOrder(['Artes Visuais', '120'])
        ->assertSeeInOrder(['Botânica', '80'])
        ->assertDontSee('não possui componentes curriculares cadastrados');
});

test('avisa quando a série não tem componentes', function () {
    ($this->detail)($this->scenario->schoolClass())
        ->assertOk()
        ->assertSee('A série/ano escolar não possui componentes curriculares cadastrados.');
});

test('na turma multisseriada mostra as séries e os componentes com a série de cada um', function () {
    $second = $this->scenario->grade('Segundo Ano');

    $this->scenario->discipline('Artes Visuais', 120);
    $this->scenario->discipline('Botânica', 80, $second);

    $schoolClass = LegacySchoolClassFactory::new()->inGrades([$this->scenario->grade, $second])->create([
        'ref_ref_cod_escola' => $this->scenario->school,
        'ref_ref_cod_serie' => $this->scenario->grade,
        'ref_cod_curso' => $this->scenario->course,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ano' => 2030,
    ]);

    ($this->detail)($schoolClass)
        ->assertOk()
        ->assertSeeInOrder([
            'Multisseriada', 'Sim',
            'Curso principal', 'Ensino Fundamental',
            'Série principal', 'Primeiro Ano',
            'Séries da turma', 'Primeiro Ano', 'Segundo Ano',
        ])
        ->assertSeeInOrder(['Artes Visuais', 'Primeiro Ano', '120', 'Botânica', 'Segundo Ano', '80']);
});

test('avisa quando a turma multisseriada não tem componentes', function () {
    $schoolClass = LegacySchoolClassFactory::new()->inGrades([$this->scenario->grade])->create([
        'ref_ref_cod_escola' => $this->scenario->school,
        'ref_ref_cod_serie' => $this->scenario->grade,
        'ref_cod_curso' => $this->scenario->course,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ano' => 2030,
    ]);

    ($this->detail)($schoolClass)
        ->assertOk()
        ->assertSee('A série/ano escolar não possui componentes curriculares cadastrados.');
});

test('não oferece as ações a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(586, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->detail)($schoolClass)
        ->assertOk()
        ->assertSee('Turma Alfa')
        ->assertDontSee('educar_turma_cad.php', false)
        ->assertDontSee('Reclassificar alunos alfabeticamente');
});

test('usuário de nível escola não abre a turma de uma escola a que não está vinculado', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $mine = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $other = EnrollmentScenario::for($this->user, 2030)->schoolClass();

    $this->actingAs(
        LegacyUserFactory::new()->school()->withSchool($this->scenario->school)->withAccess(586, modify: false, remove: false)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->detail)($mine)->assertOk()->assertSee('Turma Alfa');
    ($this->detail)($other)->assertRedirect('educar_turma_lst.php');
});

test('volta para a listagem quando a turma não é informada ou não existe', function () {
    $this->get('/intranet/educar_turma_det.php')->assertRedirect('educar_turma_lst.php');
    $this->get('/intranet/educar_turma_det.php?cod_turma=999999999')->assertRedirect('educar_turma_lst.php');
});
