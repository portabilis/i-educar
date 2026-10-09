<?php

use App\Models\LegacyCalendarDay;
use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->class = $this->school->schoolClass(['nm_turma' => 'Turma Alfa']);
    $this->arts = $this->school->discipline('Artes');
    $this->botany = $this->school->discipline('Botânica');

    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->ana->allocation($this->school->school, ['ano' => 2030]);

    $this->timetable = DB::table('pmieducar.quadro_horario')->insertGetId([
        'ref_usuario_cad' => $this->user->getKey(),
        'ref_cod_turma' => $this->class->getKey(),
        'data_cadastro' => now(),
        'ativo' => 1,
        'ano' => 2030,
    ], 'cod_quadro_horario');

    $this->url = fn (int $weekday = 2, array $query = []) => '/intranet/educar_quadro_horario_horarios_cad.php?' . http_build_query($query + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->institution,
        'ref_cod_escola' => $this->school->school->getKey(),
        'ref_cod_curso' => $this->school->course->getKey(),
        'ref_cod_serie' => $this->school->grade->getKey(),
        'ref_cod_turma' => $this->class->getKey(),
        'ref_cod_quadro_horario' => $this->timetable,
        'dia_semana' => $weekday,
        'identificador' => '20300001',
    ]);

    // A listagem para onde a tela volta depois de gravar, com as chaves na ordem em que ela as monta.
    $this->list = "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->institution}&ref_cod_escola={$this->school->school->getKey()}&ref_cod_curso={$this->school->course->getKey()}"
        . "&ref_cod_serie={$this->school->grade->getKey()}&ref_cod_turma={$this->class->getKey()}&ano=2030&busca=S";

    $this->lessons = fn () => DB::table('pmieducar.quadro_horario_horarios')->where('ref_cod_quadro_horario', $this->timetable)->orderBy('sequencial');
    $this->drafts = fn () => DB::table('pmieducar.quadro_horario_horarios_aux')->where('identificador', '20300001')->orderBy('sequencial');

    // O que o botão "incluir horário" envia: o formulário atual, sem ação, com a aula nos campos de cima.
    $this->include = fn (string $html, $discipline, string $start, string $end, int $weekday = 2) => $this->post(($this->url)($weekday), LegacyFields::from($html, [
        'tipoacao' => '',
        'incluir_horario' => '1',
        'ref_cod_disciplina' => (string) $discipline->getKey(),
        'hora_inicial' => $start,
        'hora_final' => $end,
        'ref_cod_servidor' => (string) $this->ana->employee->getKey(),
    ]))->assertOk()->getContent();

    // O que o "x" de uma linha envia: o formulário atual, sem ação, com o número da linha.
    $this->remove = fn (string $html, int $row) => $this->post(($this->url)(), LegacyFields::from($html, ['tipoacao' => '', 'excluir_horario' => (string) $row]))
        ->assertOk()
        ->getContent();

    // As aulas que o formulário carrega, serializadas, de uma requisição para a outra.
    $this->carried = fn (string $html) => unserialize(urldecode(LegacyFields::from($html)['quadro_horario']));

    $this->viewer = function (bool $modify = false) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(641, modify: $modify, remove: false)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('volta para a listagem quando falta a turma ou o quadro', function (string $missing) {
    $this->get(($this->url)(2, [$missing => '']))->assertRedirect('educar_quadro_horario_lst.php');
})->with(['ref_cod_turma', 'ref_cod_quadro_horario']);

test('abre um dia sem aulas como cadastro, com os componentes da turma, e limpa o rascunho anterior', function () {
    DB::table('pmieducar.quadro_horario_horarios_aux')->insert([
        'ref_cod_quadro_horario' => $this->timetable,
        'sequencial' => 1,
        'ref_cod_disciplina' => $this->arts->getKey(),
        'ref_cod_escola' => $this->school->school->getKey(),
        'ref_cod_serie' => $this->school->grade->getKey(),
        'ref_cod_instituicao_servidor' => $this->institution,
        'ref_servidor' => $this->ana->employee->getKey(),
        'dia_semana' => 2,
        'hora_inicial' => '08:00',
        'hora_final' => '09:00',
        'identificador' => '20300001',
        'data_cadastro' => now(),
    ]);

    expect(($this->drafts)()->count())->toBe(1);

    $html = $this->get(($this->url)())->assertOk()->assertSee('Cadastrar horário')->getContent();
    $fields = LegacyFields::from($html);

    expect($fields)->toMatchArray([
        'tipoacao' => 'Novo',
        'identificador' => '20300001',
        'dia_semana' => '2',
        'ref_cod_turma' => (string) $this->class->getKey(),
        'ref_cod_curso_' => (string) $this->school->course->getKey(),
        'ano_alocacao' => '2030',
        'quadro_horario' => urlencode(serialize(null)),
    ])
        ->and(LegacyForm::from($html)->options('ref_cod_disciplina'))->toBe([
            '' => 'Selecione uma disciplina',
            'todas_disciplinas' => 'Todas as disciplinas',
            (string) $this->arts->getKey() => 'Artes',
            (string) $this->botany->getKey() => 'Botânica',
        ])
        ->and(($this->drafts)()->count())->toBe(0);
});

test('avisa quando a série não tem componentes na escola', function () {
    $empty = $this->school->grade('Segundo Ano');

    $html = $this->get(($this->url)(2, ['ref_cod_serie' => $empty->getKey()]))->assertOk()->getContent();

    expect(LegacyForm::from($html)->options('ref_cod_disciplina'))->toBe(['NULL' => 'A série dessa escola não possui componentes cadastrados']);
});

test('abre um dia com aulas como edição, lista cada aula e copia todas para o rascunho', function () {
    $this->ana->lesson($this->class, $this->arts, 2, '08:00', '09:00');
    $this->ana->lesson($this->class, $this->botany, 2, '09:00', '10:00');
    $this->ana->lesson($this->class, $this->botany, 3, '13:00', '14:00');

    $html = $this->get(($this->url)())
        ->assertOk()
        ->assertSee('Editar horário')
        ->assertSeeInOrder(['Segunda-Feira', '08:00', '09:00', 'Artes', 'Ana Alves', 'Segunda-Feira', '09:00', '10:00', 'Botânica', 'Ana Alves'])
        ->assertDontSee('13:00')
        ->getContent();

    expect(LegacyFields::from($html)['tipoacao'])->toBe('Editar')
        ->and(($this->carried)($html))->toHaveCount(2)
        ->and(($this->carried)($html)[1])->toMatchArray([
            'ref_cod_quadro_horario_' => $this->timetable,
            'ref_ref_cod_disciplina_' => $this->arts->getKey(),
            'sequencial_' => 1,
            'ref_servidor_' => $this->ana->employee->getKey(),
            'hora_inicial_' => '08:00',
            'hora_final_' => '09:00',
            'ativo_' => 1,
            'dia_semana_' => 2,
            'qtd_horario_' => 1,
        ])
        ->and(($this->drafts)()->get()->map(fn ($draft) => [$draft->ref_cod_disciplina, $draft->hora_inicial, $draft->hora_final])->all())->toBe([
            [$this->arts->getKey(), '08:00:00', '09:00:00'],
            [$this->botany->getKey(), '09:00:00', '10:00:00'],
        ])
        ->and($html)->not->toContain("id='btn_excluir'");
});

test('mostra o nome de cada dia da semana nas aulas', function (int $weekday, string $name) {
    $this->ana->lesson($this->class, $this->arts, $weekday, '08:00', '09:00');

    $this->get(($this->url)($weekday))->assertOk()->assertSeeInOrder([$name, '08:00', '09:00', 'Artes']);
})->with([
    [1, 'Domingo'], [2, 'Segunda-Feira'], [3, 'Terça-Feira'], [4, 'Quarta-Feira'], [5, 'Quinta-Feira'], [6, 'Sexta-Feira'], [7, 'Sábado'],
]);

test('a aula com substituto não pode ser retirada da lista', function () {
    $bia = EmployeeScenario::named('Bia Borges', $this->institution);
    $this->ana->lesson($this->class, $this->arts, 2, '08:00', '09:00');

    $with = fn () => substr_count($this->get(($this->url)())->assertOk()->getContent(), "getElementById('excluir_horario').value = '1'");

    expect($with())->toBe(1);

    ($this->lessons)()->update(['ref_cod_instituicao_substituto' => $this->institution, 'ref_servidor_substituto' => $bia->employee->getKey()]);

    expect($with())->toBe(0);
});

test('incluir uma aula a acrescenta à lista e ao rascunho, sem gravar no quadro', function () {
    $html = ($this->include)($this->get(($this->url)())->getContent(), $this->arts, '08:00', '09:00');

    expect(($this->carried)($html))->toHaveCount(1)
        ->and(($this->carried)($html)[1])->toMatchArray([
            'ref_cod_quadro_horario_' => (string) $this->timetable,
            'ref_ref_cod_serie_' => (string) $this->school->grade->getKey(),
            'ref_ref_cod_escola_' => (string) $this->school->school->getKey(),
            'ref_ref_cod_disciplina_' => (string) $this->arts->getKey(),
            'ref_cod_instituicao_servidor_' => (string) $this->institution,
            'ref_servidor_' => (string) $this->ana->employee->getKey(),
            'hora_inicial_' => '08:00',
            'hora_final_' => '09:00',
            'ativo_' => 1,
            'dia_semana_' => '2',
            'qtd_horario_' => 1,
        ])
        ->and($html)->toContain('Segunda-Feira', 'Artes', 'Ana Alves')
        ->and((array) ($this->drafts)()->sole())->toMatchArray([
            'ref_cod_disciplina' => $this->arts->getKey(),
            'ref_servidor' => $this->ana->employee->getKey(),
            'dia_semana' => 2,
            'hora_inicial' => '08:00:00',
            'hora_final' => '09:00:00',
        ])
        ->and(($this->lessons)()->count())->toBe(0);
});

test('não inclui a aula sem servidor ou sem componente', function (string $field) {
    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), [
        'tipoacao' => '',
        'incluir_horario' => '1',
        'ref_cod_disciplina' => (string) $this->arts->getKey(),
        'hora_inicial' => '08:00',
        'hora_final' => '09:00',
        'ref_cod_servidor' => (string) $this->ana->employee->getKey(),
        $field => '',
    ]);

    $html = $this->post(($this->url)(), $payload)->assertOk()->getContent();

    expect(($this->carried)($html))->toBeNull()
        ->and(($this->drafts)()->count())->toBe(0);
})->with(['ref_cod_servidor', 'ref_cod_disciplina']);

test('grava as aulas incluídas num dia vazio e limpa o rascunho', function () {
    $html = ($this->include)($this->get(($this->url)())->getContent(), $this->arts, '08:00', '09:00');
    $html = ($this->include)($html, $this->botany, '09:00', '10:00');

    $this->post(($this->url)(), LegacyFields::from($html, ['ref_cod_disciplina' => '', 'hora_inicial' => '', 'hora_final' => '']))
        ->assertRedirect($this->list);

    $columns = ['sequencial', 'ref_cod_disciplina', 'ref_cod_escola', 'ref_cod_serie', 'ref_cod_instituicao_servidor', 'ref_servidor', 'ref_servidor_substituto', 'hora_inicial', 'hora_final', 'ativo', 'dia_semana'];

    expect(($this->lessons)()->get($columns)->map(fn ($lesson) => array_values((array) $lesson))->all())->toBe([
        [1, $this->arts->getKey(), $this->school->school->getKey(), $this->school->grade->getKey(), $this->institution, $this->ana->employee->getKey(), null, '08:00:00', '09:00:00', 1, 2],
        [2, $this->botany->getKey(), $this->school->school->getKey(), $this->school->grade->getKey(), $this->institution, $this->ana->employee->getKey(), null, '09:00:00', '10:00:00', 1, 2],
    ])
        ->and(($this->drafts)()->count())->toBe(0);
});

test('retirar da lista uma aula recém-incluída a descarta', function () {
    $html = ($this->include)($this->get(($this->url)())->getContent(), $this->arts, '08:00', '09:00');
    $html = ($this->include)($html, $this->botany, '09:00', '10:00');

    $html = ($this->remove)($html, 1);

    expect(($this->carried)($html))->toHaveCount(1)
        ->and(($this->carried)($html)[1])->toMatchArray(['ref_ref_cod_disciplina_' => (string) $this->botany->getKey(), 'ativo_' => 1, 'qtd_horario_' => 1])
        ->and(LegacyFields::from($html)['lst_matriculas'])->toBe((string) $this->ana->employee->getKey())
        ->and(($this->drafts)()->pluck('ref_cod_disciplina')->all())->toBe([$this->botany->getKey()]);
});

test('na edição, mantém as aulas que ficaram, acrescenta as novas e desativa as retiradas', function () {
    $this->ana->lesson($this->class, $this->arts, 2, '08:00', '09:00');
    $this->ana->lesson($this->class, $this->botany, 2, '09:00', '10:00');

    $html = $this->get(($this->url)())->getContent();
    // A tela lista as aulas sem ordenação; a de Artes pode vir em qualquer posição.
    $row = collect(($this->carried)($html))->search(fn ($lesson) => $lesson['ref_ref_cod_disciplina_'] == $this->arts->getKey());
    $html = ($this->remove)($html, $row);

    expect(($this->carried)($html)[$row])->toMatchArray(['ref_ref_cod_disciplina_' => $this->arts->getKey(), 'ativo_' => 0])
        ->and($html)->not->toContain('08:00');

    $html = ($this->include)($html, $this->arts, '10:00', '11:00');

    $this->post(($this->url)(), LegacyFields::from($html, ['ref_cod_disciplina' => '', 'hora_inicial' => '', 'hora_final' => '']))
        ->assertRedirect($this->list);

    expect(($this->lessons)()->get()->map(fn ($lesson) => [$lesson->sequencial, $lesson->ref_cod_disciplina, $lesson->hora_inicial, $lesson->ativo])->all())->toBe([
        [1, $this->arts->getKey(), '08:00:00', 0],
        [2, $this->botany->getKey(), '09:00:00', 1],
        [3, $this->arts->getKey(), '10:00:00', 1],
    ])
        ->and(($this->drafts)()->count())->toBe(0);
});

test('com "todas as disciplinas", grava uma aula por componente da turma no mesmo horário', function (bool $existing) {
    if ($existing) {
        $this->ana->lesson($this->class, $this->arts, 2, '07:00', '08:00');
    }

    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), [
        'ref_cod_disciplina' => 'todas_disciplinas',
        'hora_inicial' => '08:00',
        'hora_final' => '09:00',
        'ref_cod_servidor' => (string) $this->ana->employee->getKey(),
    ]);

    expect($payload['tipoacao'])->toBe($existing ? 'Editar' : 'Novo');

    $this->post(($this->url)(), $payload)->assertRedirect($this->list);

    expect(($this->lessons)()->where('hora_inicial', '08:00')->get()->map(fn ($lesson) => [$lesson->ref_cod_disciplina, $lesson->ref_servidor, $lesson->hora_final, $lesson->dia_semana, $lesson->ativo])->all())->toBe([
        [$this->arts->getKey(), $this->ana->employee->getKey(), '09:00:00', 2, 1],
        [$this->botany->getKey(), $this->ana->employee->getKey(), '09:00:00', 2, 1],
    ]);
})->with(['num dia vazio' => [false], 'num dia com aulas' => [true]]);

test('quem só pode visualizar volta para a listagem ao abrir ou gravar', function () {
    $html = ($this->include)($this->get(($this->url)())->getContent(), $this->arts, '08:00', '09:00');

    ($this->viewer)();

    $this->get(($this->url)())->assertRedirect(substr($this->list, 0, -strlen('&busca=S')));
    $this->post(($this->url)(), LegacyFields::from($html))->assertRedirect('educar_quadro_horario_lst.php');
    $this->post(($this->url)(), LegacyFields::from($html, ['tipoacao' => 'Editar']))->assertRedirect('educar_quadro_horario_lst.php');

    expect(($this->lessons)()->count())->toBe(0);
});

test('com "todas as disciplinas" e sem servidor, nada é gravado', function (bool $existing, string $message) {
    if ($existing) {
        $this->ana->lesson($this->class, $this->arts, 2, '07:00', '08:00');
    }

    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), [
        'ref_cod_disciplina' => 'todas_disciplinas',
        'hora_inicial' => '08:00',
        'hora_final' => '09:00',
    ]);

    $this->post(($this->url)(), $payload)->assertOk()->assertSee($message);

    expect(($this->lessons)()->where('hora_inicial', '08:00')->count())->toBe(0);
})->with([
    'num dia vazio' => [false, 'Cadastro não realizado. 1'],
    'num dia com aulas' => [true, 'Cadastro não editado.'],
]);

test('gravar sem nenhuma aula, ou "todas as disciplinas" numa série sem componentes, volta à listagem sem gravar nada', function (string $action, Closure $request) {
    [$url, $overrides] = $request->call($this);

    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), $overrides + ['tipoacao' => $action]);

    $this->post($url, $payload)->assertRedirect('educar_quadro_horario_lst.php');

    expect(($this->lessons)()->count())->toBe(0);
})->with(['Novo', 'Editar'])->with([
    'sem aulas' => [fn () => [($this->url)(), []]],
    'série sem componentes' => [fn () => [
        ($this->url)(2, ['ref_cod_serie' => $this->school->grade('Segundo Ano')->getKey()]),
        ['ref_cod_disciplina' => 'todas_disciplinas', 'hora_inicial' => '08:00', 'hora_final' => '09:00', 'ref_cod_servidor' => (string) $this->ana->employee->getKey()],
    ]],
]);

test('defeito: regravar o dia apaga o substituto das aulas', function () {
    // A edição regrava cada aula do dia sem informar o substituto, e a classe grava NULL no lugar.
    $bia = EmployeeScenario::named('Bia Borges', $this->institution);
    $this->ana->lesson($this->class, $this->arts, 2, '08:00', '09:00', [
        'ref_cod_instituicao_substituto' => $this->institution,
        'ref_servidor_substituto' => $bia->employee->getKey(),
    ]);

    $this->post(($this->url)(), LegacyFields::from($this->get(($this->url)())->getContent()))->assertRedirect($this->list);

    expect((array) ($this->lessons)()->sole(['ref_servidor_substituto', 'ref_cod_instituicao_substituto', 'ativo']))->toBe([
        'ref_servidor_substituto' => null,
        'ref_cod_instituicao_substituto' => null,
        'ativo' => 1,
    ]);
});

test('defeito: as aulas vêm serializadas do navegador e são gravadas sem conferência', function () {
    // O campo oculto `quadro_horario` passa por unserialize() e cada item é gravado como veio:
    // aqui uma aula de sexta-feira entra pela tela da segunda.
    $forged = [1 => [
        'ref_cod_quadro_horario_' => $this->timetable,
        'ref_ref_cod_serie_' => $this->school->grade->getKey(),
        'ref_ref_cod_escola_' => $this->school->school->getKey(),
        'ref_ref_cod_disciplina_' => $this->botany->getKey(),
        'ref_cod_instituicao_servidor_' => $this->institution,
        'ref_servidor_' => $this->ana->employee->getKey(),
        'hora_inicial_' => '15:00',
        'hora_final_' => '16:00',
        'ativo_' => 1,
        'dia_semana_' => 6,
        'qtd_horario_' => 1,
    ]];

    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), ['quadro_horario' => urlencode(serialize($forged))]);

    $this->post(($this->url)(), $payload)->assertRedirect();

    expect((array) ($this->lessons)()->sole(['dia_semana', 'hora_inicial', 'ref_cod_disciplina']))->toBe([
        'dia_semana' => 6,
        'hora_inicial' => '15:00:00',
        'ref_cod_disciplina' => $this->botany->getKey(),
    ]);
});

test('uma aula incompleta na lista impede a gravação', function (string $action, string $message) {
    $incomplete = [1 => [
        'ref_cod_quadro_horario_' => $this->timetable,
        'ref_ref_cod_serie_' => $this->school->grade->getKey(),
        'ref_ref_cod_escola_' => $this->school->school->getKey(),
        'ref_ref_cod_disciplina_' => $this->botany->getKey(),
        'ref_cod_instituicao_servidor_' => $this->institution,
        'ref_servidor_' => $this->ana->employee->getKey(),
        'hora_inicial_' => '15:00',
        'hora_final_' => '',
        'ativo_' => 1,
        'dia_semana_' => 2,
        'qtd_horario_' => 1,
    ]];

    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), ['tipoacao' => $action, 'quadro_horario' => urlencode(serialize($incomplete))]);

    $this->post(($this->url)(), $payload)->assertOk()->assertSee($message);

    expect(($this->lessons)()->count())->toBe(0);
})->with([
    ['Novo', 'Cadastro não realizado. 1'],
    ['Editar', 'Cadastro não editado.'],
]);

test('defeito: a ação de excluir é a da tela de calendário e apaga um dia do calendário letivo', function () {
    // O Excluir() foi copiado de educar_calendario_dia_cad.php: exclui o `calendario_dia` cujo código vier no POST.
    $day = LegacyCalendarDayFactory::new()->create();

    $this->post(($this->url)(), ['tipoacao' => 'Excluir', 'identificador' => '20300001', 'ref_cod_calendario_ano_letivo' => $day->getKey()])
        ->assertRedirect("educar_calendario_anotacao_lst.php?dia=&mes=&ano=&ref_cod_calendario_ano_letivo={$day->getKey()}");

    expect(LegacyCalendarDay::query()->whereKey($day->getKey())->exists())->toBeFalse();
});

test('defeito: sem um dia de calendário no envio, a ação de excluir dá erro 500', function () {
    $this->post(($this->url)(), ['tipoacao' => 'Excluir'])->assertStatus(500);
});

test('uma aula que chega na lista sem servidor é mostrada sem nome', function () {
    $nameless = [1 => [
        'ref_cod_quadro_horario_' => $this->timetable,
        'ref_ref_cod_serie_' => $this->school->grade->getKey(),
        'ref_ref_cod_escola_' => $this->school->school->getKey(),
        'ref_ref_cod_disciplina_' => $this->arts->getKey(),
        'ref_cod_instituicao_servidor_' => $this->institution,
        'ref_servidor_' => '',
        'hora_inicial_' => '15:00',
        'hora_final_' => '16:00',
        'ativo_' => 1,
        'dia_semana_' => 2,
        'qtd_horario_' => 1,
    ]];

    $payload = LegacyFields::from($this->get(($this->url)())->getContent(), ['tipoacao' => '', 'quadro_horario' => urlencode(serialize($nameless))]);

    $this->post(($this->url)(), $payload)
        ->assertOk()
        ->assertSeeInOrder(['Segunda-Feira', '15:00', '16:00', 'Artes'])
        ->assertDontSee('Ana Alves');
});
