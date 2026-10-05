<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->class = $this->school->schoolClass(['nm_turma' => 'Turma Alfa']);

    $this->filters = [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->institution,
        'ref_cod_escola' => $this->school->school->getKey(),
        'ref_cod_curso' => $this->school->course->getKey(),
        'ref_cod_serie' => $this->school->grade->getKey(),
        'ref_cod_turma' => $this->class->getKey(),
    ];

    $this->url = '/intranet/educar_quadro_horario_cad.php?' . http_build_query($this->filters);

    // A listagem para onde a tela volta, com as chaves na ordem em que ela as monta.
    $this->list = "educar_quadro_horario_lst.php?ref_cod_turma={$this->class->getKey()}&ref_cod_serie={$this->school->grade->getKey()}&ref_cod_curso={$this->school->course->getKey()}"
        . "&ref_cod_escola={$this->school->school->getKey()}&ref_cod_instituicao={$this->institution}&ano=2030&busca=S";

    // Para onde volta quem não tem permissão: a mesma listagem, sem refazer a busca.
    $this->back = substr($this->list, 0, -strlen('&busca=S'));

    $this->timetables = fn () => DB::table('pmieducar.quadro_horario')->where('ref_cod_turma', $this->class->getKey());

    // Um quadro já existente para a turma, com uma aula, cadastrado pelo usuário dado.
    $this->existing = function (?int $author = null) {
        $arts = $this->school->discipline('Artes');
        EmployeeScenario::named('Ana Alves', $this->institution)->lesson($this->class, $arts, 2, '08:00', '09:00');

        ($this->timetables)()->update(['ref_usuario_cad' => $author ?? $this->user->getKey()]);

        return ($this->timetables)()->value('cod_quadro_horario');
    };

    $this->viewer = function (bool $modify = false) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(641, modify: $modify, remove: false)->create(['ref_cod_instituicao' => $this->institution])
        );
    };
});

test('abre o cadastro com a turma escolhida', function () {
    $html = $this->get($this->url)->assertOk()->assertSee('Cadastrar')->getContent();

    expect(LegacyFields::from($html))->toMatchArray([
        'tipoacao' => 'Novo',
        'cod_quadro_horario' => '',
        'ano' => '2030',
        'ref_cod_instituicao' => (string) $this->institution,
    ])
        ->and($html)->toContain('educar_quadro_horario_lst.php?ref_cod_turma=' . $this->class->getKey())
        ->not->toContain("id='btn_excluir'");
});

test('cadastra o quadro de horários da turma no ano', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $this->filters);

    $this->post($this->url, $payload)->assertRedirect($this->list);

    expect((array) ($this->timetables)()->sole())->toMatchArray([
        'ref_usuario_cad' => $this->user->getKey(),
        'ref_usuario_exc' => null,
        'ref_cod_turma' => $this->class->getKey(),
        'ativo' => 1,
        'ano' => 2030,
    ]);
});

test('recusa um segundo quadro para a mesma turma', function () {
    ($this->existing)();

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $this->filters);

    $this->post($this->url, $payload)->assertOk()->assertSee('Quadro de Horário já cadastrado para esta turma');

    expect(($this->timetables)()->count())->toBe(1);
});

test('defeito: a recusa de quadro repetido só enxerga os quadros do próprio usuário', function () {
    // A consulta de duplicidade filtra por quem cadastrou; o quadro feito por outra pessoa não conta.
    ($this->existing)(LegacyUserFactory::new()->create()->getKey());

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $this->filters);

    $this->post($this->url, $payload)->assertRedirect($this->list);

    expect(($this->timetables)()->where('ativo', 1)->count())->toBe(2);
});

test('com o código do quadro, abre para exclusão sem alterar nada', function () {
    $timetable = ($this->existing)();

    $html = $this->get("{$this->url}&ref_cod_quadro_horario={$timetable}")->assertOk()->assertSee('Editar')->getContent();

    expect(LegacyFields::from($html))->toMatchArray(['tipoacao' => 'Editar', 'cod_quadro_horario' => (string) $timetable])
        ->and($html)->toContain("id='btn_excluir'")
        ->and(($this->timetables)()->value('ativo'))->toBe(1);
});

test('exclui o quadro e desativa as aulas dele', function () {
    $timetable = ($this->existing)();
    $url = "{$this->url}&ref_cod_quadro_horario={$timetable}";

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir'] + $this->filters);

    $this->post($url, $payload)->assertRedirect($this->back);

    expect((array) ($this->timetables)()->sole())->toMatchArray(['ativo' => 0, 'ref_usuario_exc' => $this->user->getKey()])
        ->and(DB::table('pmieducar.quadro_horario_horarios')->where('ref_cod_quadro_horario', $timetable)->value('ativo'))->toBe(0);
});

test('a exclusão sem o código do quadro não faz nada', function () {
    $timetable = ($this->existing)();

    $this->post($this->url, ['tipoacao' => 'Excluir'] + $this->filters)->assertOk()->assertSee('Exclusão não realizada.');

    expect(($this->timetables)()->value('ativo'))->toBe(1);
});

test('a ação de editar não existe', function () {
    $timetable = ($this->existing)();

    $this->post($this->url, ['tipoacao' => 'Editar', 'cod_quadro_horario' => $timetable] + $this->filters)->assertOk();

    expect(($this->timetables)()->value('ativo'))->toBe(1);
});

test('quem só pode visualizar volta para a listagem ao abrir ou gravar', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $this->filters);

    ($this->viewer)();

    $this->get($this->url)->assertRedirect($this->back);
    $this->post($this->url, $payload)->assertRedirect($this->back);

    expect(($this->timetables)()->count())->toBe(0);
});

test('quem pode cadastrar mas não excluir não vê o botão nem consegue excluir', function () {
    $timetable = ($this->existing)();
    $url = "{$this->url}&ref_cod_quadro_horario={$timetable}";

    ($this->viewer)(modify: true);

    $html = $this->get($url)->assertOk()->getContent();

    expect($html)->not->toContain("id='btn_excluir'");

    $this->post($url, LegacyFields::from($html, ['tipoacao' => 'Excluir'] + $this->filters))->assertRedirect($this->back);

    expect(($this->timetables)()->value('ativo'))->toBe(1);
});

test('não cadastra o quadro sem a turma', function () {
    $this->post($this->url, ['tipoacao' => 'Novo', 'ano' => '2030'])->assertOk()->assertSee('Cadastro não realizado.');

    expect(($this->timetables)()->count())->toBe(0);
});
