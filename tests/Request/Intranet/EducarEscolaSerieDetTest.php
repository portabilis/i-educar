<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Fundamental']);
    $this->grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Primeiro Ano']);
    $this->school = LegacySchoolFactory::new()->withName('Escola Aurora')->create($this->institution);

    $this->link = fn (array $attributes = []) => LegacySchoolGradeFactory::new()->create($attributes + [
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $this->grade,
    ]);

    $this->keys = "ref_cod_escola={$this->school->getKey()}&ref_cod_serie={$this->grade->getKey()}";
    $this->url = '/intranet/educar_escola_serie_det.php?' . $this->keys;
});

test('mostra instituição, escola, curso e série do vínculo', function () {
    ($this->link)();

    $this->get($this->url)
        ->assertOk()
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('Escola Aurora')
        ->assertSee('Fundamental')
        ->assertSee('Primeiro Ano')
        ->assertDontSee('Hora Inicial')
        ->assertDontSee('Componentes curriculares')
        ->assertSee('educar_escola_serie_cad.php?' . $this->keys, false);
});

test('mostra os horários quando preenchidos', function () {
    ($this->link)([
        'hora_inicial' => '07:30:00',
        'hora_final' => '11:45:00',
        'hora_inicio_intervalo' => '09:30:00',
        'hora_fim_intervalo' => '09:50:00',
    ]);

    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Hora Inicial', '07:30', 'Hora Final', '11:45', 'Hora Início Intervalo', '09:30', 'Hora Fim Intervalo', '09:50']);
});

test('lista os componentes com a carga da escola ou, na falta dela, a da série', function () {
    ($this->link)();

    $component = fn (string $name, ?float $hours) => tap(
        LegacyDisciplineFactory::new()->create(['instituicao_id' => $this->user->ref_cod_instituicao, 'nome' => $name]),
        function ($discipline) use ($hours) {
            LegacyDisciplineAcademicYearFactory::new()->create([
                'componente_curricular_id' => $discipline,
                'ano_escolar_id' => $this->grade,
                'carga_horaria' => 80,
            ]);

            LegacySchoolGradeDisciplineFactory::new()->create([
                'ref_ref_cod_escola' => $this->school,
                'ref_ref_cod_serie' => $this->grade,
                'ref_cod_disciplina' => $discipline,
                'carga_horaria' => $hours,
            ]);
        }
    );

    $component('Artes Visuais', 120);
    $component('Botânica', null);

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Componentes curriculares')
        ->assertSeeInOrder(['Artes Visuais', '120 h', 'Botânica', '80 h']);
});

test('não oferece editar a quem só pode visualizar', function () {
    ($this->link)();

    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(585, modify: false, remove: false)->create($this->institution));

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Primeiro Ano')
        ->assertDontSee('educar_escola_serie_cad.php', false);
});

test('volta para a listagem quando o vínculo não existe ou está inativo', function () {
    $this->get($this->url)->assertRedirect('educar_escola_serie_lst.php');

    ($this->link)(['ativo' => 0]);

    $this->get($this->url)->assertRedirect('educar_escola_serie_lst.php');
});
