<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->class = $this->school->schoolClass(['nm_turma' => 'Turma Alfa']);

    $this->arts = $this->school->discipline('Artes');
    $this->arts->update(['abreviatura' => 'ART']);
    $this->botany = $this->school->discipline('Botânica');
    $this->botany->update(['abreviatura' => 'BOT']);

    $this->filters = [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->institution,
        'ref_cod_escola' => $this->school->school->getKey(),
        'ref_cod_curso' => $this->school->course->getKey(),
        'ref_cod_serie' => $this->school->grade->getKey(),
        'ref_cod_turma' => $this->class->getKey(),
    ];

    // As chaves na ordem em que a tela monta os links para o cadastro.
    $this->keys = "ref_cod_turma={$this->class->getKey()}&ref_cod_serie={$this->school->grade->getKey()}&ref_cod_curso={$this->school->course->getKey()}"
        . "&ref_cod_escola={$this->school->school->getKey()}&ref_cod_instituicao={$this->institution}&ano=2030";

    $this->timetable = fn () => DB::table('pmieducar.quadro_horario')->where('ref_cod_turma', $this->class->getKey())->value('cod_quadro_horario');

    $this->teacher = function (string $name, bool $allocated = true) {
        $teacher = EmployeeScenario::named($name, $this->institution);

        if ($allocated) {
            $teacher->allocation($this->school->school, ['ano' => 2030]);
        }

        return $teacher;
    };

    $this->show = fn () => $this->post('/intranet/educar_quadro_horario_lst.php?busca=S', $this->filters)->assertOk();
});

test('sem busca mostra só os filtros, sem quadro algum', function () {
    $this->get('/intranet/educar_quadro_horario_lst.php')
        ->assertOk()
        ->assertSee('Quadros de horários')
        ->assertDontSee("class='calendar'", false)
        ->assertDontSee('Não existe nenhum quadro de horário cadastrado para esta turma.');
});

test('avisa que a turma não tem quadro e oferece o cadastro', function () {
    ($this->show)()
        ->assertSee('Não existe nenhum quadro de horário cadastrado para esta turma.')
        ->assertSee("educar_quadro_horario_cad.php?{$this->keys}'", false)
        ->assertDontSee('Excluir Quadro de Horários');
});

test('mostra as aulas de cada dia em ordem de hora, com o componente e o primeiro nome do professor', function () {
    $ana = ($this->teacher)('Ana Alves');
    $ana->lesson($this->class, $this->botany, 2, '09:00', '10:00');
    $ana->lesson($this->class, $this->arts, 2, '08:00', '09:00');
    ($this->teacher)('Bruno Borges')->lesson($this->class, $this->arts, 4, '13:00', '14:00');

    $timetable = ($this->timetable)();

    ($this->show)()
        ->assertSee('Turma: Turma Alfa')
        ->assertSeeInOrder([
            '<div class="horario">08:00 - 09:00<br />ART<br />Ana</div>',
            '<div class="horario">09:00 - 10:00<br />BOT<br />Ana</div>',
            '<div class="horario">13:00 - 14:00<br />ART<br />Bruno</div>',
        ], false)
        ->assertSee("envia( this, {$this->class->getKey()}, {$this->school->grade->getKey()}, {$this->school->course->getKey()}, {$this->school->school->getKey()}, {$this->institution}, {$timetable}, 2, 2030 )", false)
        ->assertSee("educar_quadro_horario_cad.php?{$this->keys}&ref_cod_quadro_horario={$timetable}'", false)
        ->assertDontSee('Novo Quadro de Horários');
});

test('os filtros também chegam pela URL', function () {
    ($this->teacher)('Ana Alves')->lesson($this->class, $this->arts, 2, '08:00', '09:00');

    $this->get('/intranet/educar_quadro_horario_lst.php?busca=S&' . http_build_query($this->filters))
        ->assertOk()
        ->assertSee('<div class="horario">08:00 - 09:00<br />ART<br />Ana</div>', false);
});

test('mostra o substituto no lugar do professor da aula', function () {
    $bia = ($this->teacher)('Bia Borges');

    ($this->teacher)('Ana Alves')->lesson($this->class, $this->arts, 2, '08:00', '09:00', [
        'ref_cod_instituicao_substituto' => $this->institution,
        'ref_servidor_substituto' => $bia->employee->getKey(),
    ]);

    ($this->show)()->assertSee('<div class="horario">08:00 - 09:00<br />ART<br />Bia</div>', false);
});

test('o professor sem alocação ativa ou inativo fica sem nome na aula', function (Closure $teacher) {
    $teacher->call($this)->lesson($this->class, $this->arts, 2, '08:00', '09:00');

    ($this->show)()->assertSee('<div class="horario">08:00 - 09:00<br />ART<br /></div>', false);
})->with([
    'sem alocação' => [fn () => ($this->teacher)('Ana Alves', allocated: false)],
    'inativo' => [function () {
        $ana = ($this->teacher)('Ana Alves');
        $ana->employee->update(['ativo' => 0]);

        return $ana;
    }],
]);

test('aulas no mesmo horário da primeira do dia viram uma só, de educação infantil', function () {
    $ana = ($this->teacher)('Ana Alves');
    $ana->lesson($this->class, $this->arts, 2, '08:00', '09:00');
    $ana->lesson($this->class, $this->botany, 2, '08:00', '09:00');

    $html = ($this->show)()
        ->assertSee('<div class="horario">08:00 - 09:00<br />EDUCAÇÃO INFANTIL<br />Ana</div>', false)
        ->getContent();

    expect(substr_count($html, '<div class="horario">08:00'))->toBe(1);
});

test('quem só pode visualizar vê o quadro sem os botões de cadastrar e excluir', function () {
    ($this->teacher)('Ana Alves')->lesson($this->class, $this->arts, 2, '08:00', '09:00');
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(641, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->show)()
        ->assertSee('Turma: Turma Alfa')
        ->assertDontSee('educar_quadro_horario_cad.php', false);
});

test('quem pode cadastrar mas não excluir não vê o botão de excluir', function () {
    ($this->teacher)('Ana Alves')->lesson($this->class, $this->arts, 2, '08:00', '09:00');
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(641, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->show)()
        ->assertSee('Turma: Turma Alfa')
        ->assertDontSee('Excluir Quadro de Horários')
        ->assertDontSee('Novo Quadro de Horários');
});

test('o usuário de biblioteca não acessa a tela', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->withAccess(641)->create([
        'ref_cod_instituicao' => $this->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]));

    ($this->show)()
        ->assertSee('Usuário sem permissão para acessar esta página')
        ->assertDontSee('Turma: Turma Alfa');
});
