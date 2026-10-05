<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

/**
 * Popup de pesquisa de professores. Só entra na lista quem é servidor ativo
 * com função de professor, alocação ativa e algum componente que leciona.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    LegacyInstitution::query()->whereKey($this->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->arts = $this->school->discipline('Artes');
    $this->botany = $this->school->discipline('Botânica');

    // Um professor completo: função, alocação de 20 horas pela manhã em 2030 e um componente.
    $this->teacher = function (string $name, array $employee = [], array $allocation = [], ?array $disciplines = null) {
        $scenario = EmployeeScenario::named($name, $this->institution, $employee);
        $role = $scenario->role('Professor', '1001');
        $scenario->allocation($this->school->school, $allocation + ['ano' => 2030, 'periodo' => 1, 'carga_horaria' => '20:00:00']);

        foreach ($disciplines ?? [$this->arts] as $discipline) {
            $scenario->discipline($role, $discipline, $this->school->course);
        }

        return $scenario;
    };

    $this->search = fn (array $query = []) => $this->get('/intranet/educar_pesquisa_servidor_lst.php?' . http_build_query($query + [
        'campo1' => 'ref_cod_servidor',
        'campo2' => 'nm_servidor',
        'ref_cod_instituicao' => $this->institution,
    ]))->assertOk();
});

test('lista os professores em ordem de nome, com a matrícula de funcionário e a instituição', function () {
    $bia = ($this->teacher)('Bia Borges');
    $ana = ($this->teacher)('Ana Alves');
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $ana->employee->getKey(), 'matricula' => 4321]);

    ($this->search)()->assertSeeInOrder(['Ana Alves', '4321', 'Rede Aurora', 'Bia Borges', 'Rede Aurora']);
});

test('deixa de fora quem não é professor, não tem alocação, não leciona nada ou está inativo', function () {
    ($this->teacher)('Ana Alves');

    $coordinator = EmployeeScenario::named('Carla Coordenadora', $this->institution);
    $coordinator->role('Coordenador', '2002', teacher: false);
    $coordinator->allocation($this->school->school, ['ano' => 2030]);

    $unallocated = EmployeeScenario::named('Davi Desalocado', $this->institution);
    $unallocated->discipline($unallocated->role('Professor', '3003'), $this->arts, $this->school->course);

    $idle = EmployeeScenario::named('Eva Sem Componente', $this->institution);
    $idle->role('Professor', '4004');
    $idle->allocation($this->school->school, ['ano' => 2030]);

    ($this->teacher)('Fábio Inativo', ['ativo' => 0]);

    ($this->search)()
        ->assertSee('Ana Alves')
        ->assertDontSee('Carla Coordenadora')
        ->assertDontSee('Davi Desalocado')
        ->assertDontSee('Eva Sem Componente')
        ->assertDontSee('Fábio Inativo');
});

test('devolve o professor escolhido aos campos da tela de origem', function (array $query, Closure $script) {
    $ana = ($this->teacher)('Ana Alves');

    ($this->search)($query)->assertSee($script->call($this, $ana->employee->getKey()), false);
})->with([
    'código e nome' => [[], fn ($id) => "addVal1('ref_cod_servidor','{$id}','Ana Alves'); addVal1('nm_servidor','Ana Alves','{$id}')"],
    'só o campo do nome' => [['campo1' => null], fn ($id) => "onclick=\"addVal1('nm_servidor','Ana Alves','{$id}'); fecha();\""],
    'só o campo do código' => [['campo2' => null], fn ($id) => "onclick=\"addVal1('ref_cod_servidor','{$id}','Ana Alves'); fecha();\""],
    'com tipo, código e nome' => [['tipo' => 1], fn ($id) => "addVal1('ref_cod_servidor','{$id}', 'Ana Alves'); addVal1('nm_servidor','Ana Alves', '{$id}')"],
    'com tipo, vindo do quadro de horários' => [['tipo' => 1, 'horario' => 'N'], fn ($id) => "addVal1('ref_cod_servidor','Ana Alves','{$id}'); addVal1('nm_servidor','{$id}','Ana Alves')"],
    'com tipo, só o campo do código' => [['tipo' => 1, 'campo2' => null], fn ($id) => "onclick=\"addVal1('ref_cod_servidor','{$id}','Ana Alves'); fecha();\""],
]);

test('filtra pelo nome, pela escolaridade, pela carga horária e pela escola e ano da alocação', function (Closure $query) {
    $degree = LegacySchoolingDegreeFactory::new()->create(['descricao' => 'Mestrado']);
    $otherSchool = EnrollmentScenario::for($this->user, 2030);

    ($this->teacher)('Ana Álves', ['ref_idesco' => $degree, 'carga_horaria' => 30]);

    $bia = EmployeeScenario::named('Bia Borges', $this->institution);
    $bia->discipline($bia->role('Professor', '1002'), $this->arts, $this->school->course);
    $bia->allocation($otherSchool->school, ['ano' => 2029]);

    ($this->search)($query->call($this, $degree))
        ->assertSee('Ana Álves')
        ->assertDontSee('Bia Borges');
})->with([
    'nome' => [fn () => ['nome_servidor' => 'ana alv']],
    'escolaridade' => [fn ($degree) => ['ref_idesco' => $degree->getKey()]],
    'carga horária' => [fn () => ['carga_horaria' => 30]],
    'escola da alocação' => [fn () => ['ref_cod_escola' => $this->school->school->getKey()]],
    'ano da alocação' => [fn () => ['ano_alocacao' => 2030]],
]);

test('filtra por quem leciona o componente no curso, ou qualquer componente do curso', function () {
    ($this->teacher)('Ana Alves', disciplines: [$this->arts]);
    ($this->teacher)('Bia Borges', disciplines: [$this->botany]);

    ($this->search)(['ref_cod_disciplina' => $this->botany->getKey(), 'ref_cod_curso' => $this->school->course->getKey()])
        ->assertSee('Bia Borges')
        ->assertDontSee('Ana Alves');

    ($this->search)(['ref_cod_disciplina' => 0, 'ref_cod_curso' => $this->school->course->getKey()])
        ->assertSee('Bia Borges')
        ->assertSee('Ana Alves');
});

test('para substituir um servidor, lista só quem leciona todos os componentes dele, e não ele mesmo', function () {
    $ana = ($this->teacher)('Ana Alves', disciplines: [$this->arts, $this->botany]);
    ($this->teacher)('Bia Borges', disciplines: [$this->arts, $this->botany]);
    ($this->teacher)('Caio Costa', disciplines: [$this->arts]);

    ($this->search)(['ref_cod_servidor' => $ana->employee->getKey()])
        ->assertSee('Bia Borges')
        ->assertDontSee('Caio Costa')
        ->assertDontSee('Ana Alves');
});

test('guarda os parâmetros na sessão e os reaproveita na busca seguinte', function () {
    ($this->teacher)('Ana Alves');

    ($this->search)(['tipo' => 1])->assertSessionHas('campo1', 'ref_cod_servidor')->assertSessionHas('tipo', '1');

    $this->get('/intranet/educar_pesquisa_servidor_lst.php?nome_servidor=Ana')
        ->assertOk()
        ->assertSee("addVal1('ref_cod_servidor'", false)
        ->assertSessionHas('ref_cod_instituicao', (string) $this->institution)
        ->assertSessionHas('tipo', '');
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->teacher)(sprintf('Registro %02d', $number));
    }

    ($this->search)()->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->search)(['pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('para um horário do quadro, lista quem tem alocação com carga no período e está livre naquela aula', function (string $start, string $end, int $period, string $minutes) {
    $class = $this->school->schoolClass();

    ($this->teacher)('Ana Livre', allocation: ['periodo' => $period]);
    ($this->teacher)('Bia Ocupada', allocation: ['periodo' => $period])->lesson($class, $this->arts, 2, $start, $end);
    ($this->teacher)('Caio Outro Turno', allocation: ['periodo' => $period === 1 ? 2 : 1]);
    ($this->teacher)('Dora Multisseriada', ['multi_seriado' => true], ['periodo' => $period])->lesson($class, $this->arts, 2, $start, $end);

    ($this->search)([
        'horario' => 'S',
        'dia_semana' => 2,
        'hora_inicial' => $start,
        'hora_final' => $end,
        $minutes => 60,
        'ref_cod_escola' => $this->school->school->getKey(),
        'ano_alocacao' => 2030,
        'identificador' => 'abc',
    ])
        ->assertSee('Ana Livre')
        ->assertSee('Dora Multisseriada')
        ->assertDontSee('Bia Ocupada')
        ->assertDontSee('Caio Outro Turno');
})->with([
    'manhã' => ['08:00', '09:00', 1, 'min_mat'],
    'tarde' => ['13:00', '14:00', 2, 'min_ves'],
    'noite' => ['19:00', '20:00', 3, 'min_not'],
]);

test('quem já dá aquela aula não conta como ocupado quando vem na lista de matrículas', function () {
    $bia = ($this->teacher)('Bia Ocupada');
    $bia->lesson($this->school->schoolClass(), $this->arts, 2, '08:00', '09:00');

    $query = ['horario' => 'S', 'dia_semana' => 2, 'hora_inicial' => '08:00', 'hora_final' => '09:00', 'min_mat' => 60];

    ($this->search)($query)->assertDontSee('Bia Ocupada');
    ($this->search)($query + ['lst_matriculas' => (string) $bia->employee->getKey()])->assertSee('Bia Ocupada');
});

test('a aula que atravessa o meio-dia ou as 18 horas só exige o turno em que começa', function (string $start, string $end, int $period, string $minutes) {
    ($this->teacher)('Ana Livre', allocation: ['periodo' => $period]);

    ($this->search)(['horario' => 'S', 'dia_semana' => 2, 'hora_inicial' => $start, 'hora_final' => $end, $minutes => 120])
        ->assertSee('Ana Livre');
})->with([
    'da manhã para a tarde' => ['11:00', '13:00', 1, 'min_mat'],
    'da tarde para a noite' => ['17:00', '19:00', 2, 'min_ves'],
]);

test('a aula que começa de manhã e termina à meia-noite também exige o turno da noite', function () {
    ($this->teacher)('Ana Manhã', allocation: ['periodo' => 1]);

    ($this->search)(['horario' => 'S', 'dia_semana' => 2, 'hora_inicial' => '08:00', 'hora_final' => '00:00', 'min_mat' => 60, 'min_not' => 60])
        ->assertDontSee('Ana Manhã');
});

test('defeito: com dia e hora mas sem ser do quadro de horários, a consulta estoura', function (string $start, string $end) {
    // A subconsulta de carga usa o apelido `qhh` de uma tabela que não está no FROM.
    ($this->teacher)('Ana Alves');

    $this->get('/intranet/educar_pesquisa_servidor_lst.php?' . http_build_query([
        'campo1' => 'ref_cod_servidor',
        'ref_cod_instituicao' => $this->institution,
        'dia_semana' => 2,
        'hora_inicial' => $start,
        'hora_final' => $end,
    ]))->assertStatus(500);
})->with([['08:00', '09:00'], ['13:00', '14:00'], ['19:00', '20:00']]);
