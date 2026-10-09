<?php

use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\SchoolClassInepFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * A classe legada de turma. O formulário de turma não entra na suíte (ele
 * depende do pacote de relatórios), então a gravação e a edição de cada
 * campo são testadas direto na classe, junto com os filtros das duas
 * listagens e as consultas avulsas.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->row = function (int $id): object {
        return DB::table('pmieducar.turma')->where('cod_turma', $id)->first();
    };

    /**
     * Uma turma com todos os campos que a classe grava.
     */
    $this->full = function (?int $id = null): clsPmieducarTurma {
        $second = $this->scenario->grade('Série secundária');
        $regent = EmployeeFactory::new()->create(['institution_id' => $this->institution]);

        $object = new clsPmieducarTurma(
            cod_turma: $id,
            ref_usuario_exc: $this->user,
            ref_usuario_cad: $this->user,
            ref_ref_cod_serie: $this->scenario->grade->getKey(),
            ref_ref_cod_escola: $this->scenario->school->getKey(),
            nm_turma: 'Turma Completa',
            sgl_turma: 'TCO',
            max_aluno: 32,
            multiseriada: 1,
            data_cadastro: '2030-01-15 08:00:00',
            ativo: 1,
            ref_cod_turma_tipo: LegacySchoolClassTypeFactory::new()->create()->getKey(),
            hora_inicial: '07:30:00',
            hora_final: '11:45:00',
            hora_inicio_intervalo: '09:30:00',
            hora_fim_intervalo: '09:50:00',
            ref_cod_regente: $regent->getKey(),
            ref_cod_instituicao_regente: $this->institution,
            ref_cod_instituicao: $this->institution,
            ref_cod_curso: $this->scenario->course->getKey(),
            ref_ref_cod_serie_mult: $second->getKey(),
            ref_ref_cod_escola_mult: $this->scenario->school->getKey(),
            visivel: true,
            turma_turno_id: $this->scenario->period(4)->getKey(),
            tipo_boletim: 1,
            ano: 2030,
            ref_cod_disciplina_dispensada: $this->scenario->discipline('Ensino Religioso')->getKey(),
        );

        $object->tipo_boletim_diferenciado = 2;
        $object->tipo_atendimento = [0, 5];
        $object->cod_curso_profissional = 1001;
        $object->etapa_educacenso = 14;
        $object->etapa_agregada = 302;
        $object->nao_informar_educacenso = 1;
        $object->tipo_mediacao_didatico_pedagogico = 1;
        $object->local_funcionamento_diferenciado = 0;
        $object->classe_especial = 0;
        $object->formacao_alternancia = 1;
        $object->dias_semana = '{2,3,4}';
        $object->atividades_complementares = '{11002}';
        $object->atividades_aee = '{1}';
        $object->hora_inicial_matutino = '07:30:00';
        $object->hora_inicio_intervalo_matutino = '09:30:00';
        $object->hora_fim_intervalo_matutino = '09:50:00';
        $object->hora_final_matutino = '11:45:00';
        $object->hora_inicial_vespertino = '13:15:00';
        $object->hora_inicio_intervalo_vespertino = '15:00:00';
        $object->hora_fim_intervalo_vespertino = '15:20:00';
        $object->hora_final_vespertino = '17:15:00';

        // A edição lê esta propriedade, que a classe não declara; fora de uma
        // requisição o aviso de propriedade indefinida vira exceção.
        $object->cod_curso = null;

        return $object;
    };

    $this->assertFull = function (object $row, bool $withStartTime = true) {
        $this->assertSame('Turma Completa', $row->nm_turma);
        $this->assertSame('TCO', $row->sgl_turma);
        $this->assertSame(32, $row->max_aluno);
        $this->assertSame(1, $row->multiseriada);
        $this->assertSame($this->scenario->grade->getKey(), $row->ref_ref_cod_serie);
        $this->assertSame($this->scenario->school->getKey(), $row->ref_ref_cod_escola);
        $this->assertSame($this->scenario->course->getKey(), $row->ref_cod_curso);
        $this->assertSame($this->scenario->school->getKey(), $row->ref_ref_cod_escola_mult);
        $this->assertNotNull($row->ref_ref_cod_serie_mult);
        $this->assertNotNull($row->ref_cod_regente);
        $this->assertSame($this->institution, $row->ref_cod_instituicao_regente);
        $this->assertSame($withStartTime ? '07:30:00' : null, $row->hora_inicial);
        $this->assertSame('11:45:00', $row->hora_final);
        $this->assertSame('09:30:00', $row->hora_inicio_intervalo);
        $this->assertSame('09:50:00', $row->hora_fim_intervalo);
        $this->assertTrue($row->visivel);
        $this->assertSame(1, $row->tipo_boletim);
        $this->assertSame(2, $row->tipo_boletim_diferenciado);
        $this->assertSame(2030, $row->ano);
        $this->assertSame('{0,5}', $row->tipo_atendimento);
        $this->assertSame(1001, $row->cod_curso_profissional);
        $this->assertSame(14, $row->etapa_educacenso);
        $this->assertSame(302, $row->etapa_agregada);
        $this->assertNotNull($row->ref_cod_disciplina_dispensada);
        $this->assertSame(1, $row->nao_informar_educacenso);
        $this->assertSame(1, $row->tipo_mediacao_didatico_pedagogico);
        $this->assertSame(0, $row->local_funcionamento_diferenciado);
        $this->assertSame(0, $row->classe_especial);
        $this->assertSame(1, $row->formacao_alternancia);
        $this->assertSame('{2,3,4}', $row->dias_semana);
        $this->assertSame('{11002}', $row->atividades_complementares);
        $this->assertSame('{1}', $row->atividades_aee);
        $this->assertSame('07:30:00', $row->hora_inicial_matutino);
        $this->assertSame('09:30:00', $row->hora_inicio_intervalo_matutino);
        $this->assertSame('09:50:00', $row->hora_fim_intervalo_matutino);
        $this->assertSame('11:45:00', $row->hora_final_matutino);
        $this->assertSame('13:15:00', $row->hora_inicial_vespertino);
        $this->assertSame('15:00:00', $row->hora_inicio_intervalo_vespertino);
        $this->assertSame('15:20:00', $row->hora_fim_intervalo_vespertino);
        $this->assertSame('17:15:00', $row->hora_final_vespertino);
    };

    /**
     * Duas turmas que diferem em tudo o que as listagens filtram: a primeira
     * é a que cada filtro deve devolver sozinha.
     *
     * @return array{0: LegacySchoolClass, 1: LegacySchoolClass}
     */
    $this->pair = function (): array {
        $second = LegacyUserFactory::new()->admin()->create();
        $other = EnrollmentScenario::for($second, 2029);
        $regent = EmployeeFactory::new()->create(['institution_id' => $this->institution]);

        $wanted = $this->scenario->schoolClass([
            'nm_turma' => 'Turma Alfa',
            'sgl_turma' => 'ALF',
            'max_aluno' => 31,
            'multiseriada' => 1,
            'ref_usuario_cad' => $this->user,
            'ref_usuario_exc' => $this->user,
            'data_cadastro' => '2030-03-10 08:00:00',
            'data_exclusao' => '2030-04-10 08:00:00',
            'ref_cod_turma_tipo' => LegacySchoolClassTypeFactory::new()->create(),
            'hora_inicial' => '07:30:00',
            'hora_final' => '11:45:00',
            'hora_inicio_intervalo' => '09:30:00',
            'hora_fim_intervalo' => '09:50:00',
            'ref_cod_regente' => $regent->getKey(),
            'ref_cod_instituicao_regente' => $this->institution,
            'turma_turno_id' => $this->scenario->period(1),
            'tipo_boletim' => 7,
        ]);

        // Na turma multisseriada, o filtro por série olha as séries da turma.
        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $this->scenario->school,
            'serie_id' => $this->scenario->grade,
            'turma_id' => $wanted,
        ]);

        $this->scenario->enrolled('Ana Alves', $wanted);

        $unwanted = $other->schoolClass([
            'nm_turma' => 'Turma Beta',
            'sgl_turma' => 'BET',
            'max_aluno' => 20,
            'ref_usuario_cad' => $second->getKey(),
            'data_cadastro' => '2030-06-10 08:00:00',
            'ref_cod_turma_tipo' => LegacySchoolClassTypeFactory::new()->create(),
            'hora_inicial' => '13:15:00',
            'hora_final' => '17:15:00',
            'hora_inicio_intervalo' => '15:00:00',
            'hora_fim_intervalo' => '15:20:00',
            'turma_turno_id' => $other->period(2),
            'tipo_boletim' => 8,
        ]);

        return [$wanted, $unwanted];
    };

    /**
     * @return array<int, array<string, mixed>>
     */
    $this->filters = function (LegacySchoolClass $wanted): array {
        $school = ['int_ref_ref_cod_escola' => $wanted->ref_ref_cod_escola];

        return [
            ['int_cod_turma' => $wanted->getKey()],
            ['int_ref_usuario_exc' => $this->user],
            ['int_ref_usuario_cad' => $this->user],
            ['int_ref_ref_cod_serie' => $wanted->ref_ref_cod_serie],
            $school,
            ['str_sgl_turma' => 'ALF'],
            $school + ['int_max_aluno' => 31],
            $school + ['int_multiseriada' => 1],
            ['date_data_cadastro_ini' => '2030-03-01', 'date_data_cadastro_fim' => '2030-04-01'],
            ['date_data_exclusao_ini' => '2030-04-01', 'date_data_exclusao_fim' => '2030-05-01'],
            ['int_ref_cod_turma_tipo' => $wanted->ref_cod_turma_tipo],
            $school + ['time_hora_inicial_ini' => '07:00', 'time_hora_inicial_fim' => '08:00'],
            $school + ['time_hora_final_ini' => '11:00', 'time_hora_final_fim' => '12:00'],
            $school + ['time_hora_inicio_intervalo_ini' => '09:00', 'time_hora_inicio_intervalo_fim' => '09:40'],
            $school + ['time_hora_fim_intervalo_ini' => '09:45', 'time_hora_fim_intervalo_fim' => '10:00'],
            ['int_ref_cod_regente' => $wanted->ref_cod_regente, 'int_ref_cod_instituicao_regente' => $this->institution],
            $school + ['int_ref_cod_instituicao' => $this->institution],
            ['int_ref_cod_curso' => $wanted->ref_cod_curso],
            $school + ['int_qtd_min_alunos_matriculados' => 1],
            $school + ['visivel' => true],
            $school + ['visivel' => ['true', 'false']],
            $school + ['turma_turno_id' => 1],
            ['tipo_boletim' => 7],
            $school + ['ano' => 2030],
        ];
    };

    $user = LegacyUserFactory::new()->admin()->create();

    $this->user = $user->getKey();
    $this->institution = $user->ref_cod_instituicao;
    $this->scenario = EnrollmentScenario::for($user, 2030);
});

test('cadastra com todos os campos', function () {
    $id = ($this->full)()->cadastra();
    $row = ($this->row)($id);

    ($this->assertFull)($row);
    $this->assertSame(1, $row->ativo);
    $this->assertSame($this->user, $row->ref_usuario_cad);
});

test('cadastra so com os campos obrigatorios', function () {
    $id = (new clsPmieducarTurma(
        ref_usuario_cad: $this->user,
        nm_turma: 'Turma Mínima',
        max_aluno: 10,
        multiseriada: 0,
        ref_cod_turma_tipo: LegacySchoolClassTypeFactory::new()->create()->getKey(),
    ))->cadastra();

    $row = ($this->row)($id);

    $this->assertSame('Turma Mínima', $row->nm_turma);
    $this->assertNull($row->ref_ref_cod_serie);
    $this->assertNull($row->ref_cod_disciplina_dispensada);
    $this->assertNull($row->hora_inicial);
});

test('nao grava nem consulta sem os campos obrigatorios', function () {
    $empty = new clsPmieducarTurma;

    $this->assertFalse($empty->cadastra());
    $this->assertFalse($empty->edita());
    $this->assertFalse($empty->detalhe());
    $this->assertFalse($empty->checaAnoLetivoEmAndamento());
    $this->assertNull($empty->getInep());
    $this->assertNull($empty->getInepTurno(1));
    $this->assertSame('2030-12-20', (new clsPmieducarTurma(data_exclusao: '2030-12-20'))->data_exclusao);
});

test('edita preenchendo todos os campos', function () {
    $id = $this->scenario->schoolClass(['nm_turma' => 'Turma Antiga'])->getKey();

    $object = ($this->full)($id);
    // A hora inicial fica de fora: com ela a edição estoura (ver o defeito abaixo).
    $object->hora_inicial = false;
    $object->parecer_1_etapa = 'Parecer um';
    $object->parecer_2_etapa = 'Parecer dois';
    $object->parecer_3_etapa = 'Parecer três';
    $object->parecer_4_etapa = 'Parecer quatro';

    $this->assertTrue($object->edita());

    $row = ($this->row)($id);

    ($this->assertFull)($row, withStartTime: false);
    $this->assertSame($this->user, $row->ref_usuario_exc);
    $this->assertStringStartsWith('2030-01-15 08:00:00', $row->data_cadastro);
    $this->assertSame('Parecer um', $row->parecer_1_etapa);
    $this->assertSame('Parecer dois', $row->parecer_2_etapa);
    $this->assertSame('Parecer três', $row->parecer_3_etapa);
    $this->assertSame('Parecer quatro', $row->parecer_4_etapa);
});

// Defeito conhecido: editar só com o código e o usuário apaga o regente, a
// série secundária, as etapas do Educacenso, a disciplina dispensada e os
// horários por turno, porque a edição grava NULL no que não recebeu valor.
// É o que a tela de pareceres da turma faz.
test('defeito editar sem informar os campos opcionais os apaga', function () {
    $id = ($this->full)()->cadastra();

    $object = new clsPmieducarTurma($id, $this->user);
    $object->cod_curso = null;

    $this->assertTrue($object->edita());

    $row = ($this->row)($id);

    foreach ([
        'ref_cod_regente', 'ref_ref_cod_escola_mult', 'ref_ref_cod_serie_mult', 'cod_curso_profissional', 'etapa_educacenso',
        'etapa_agregada', 'ref_cod_disciplina_dispensada', 'local_funcionamento_diferenciado', 'classe_especial', 'formacao_alternancia',
        'hora_inicial_matutino', 'hora_inicio_intervalo_matutino', 'hora_fim_intervalo_matutino', 'hora_final_matutino',
        'hora_inicial_vespertino', 'hora_inicio_intervalo_vespertino', 'hora_fim_intervalo_vespertino', 'hora_final_vespertino',
    ] as $column) {
        $this->assertNull($row->{$column}, $column);
    }

    // Os campos cujo padrão é falso, e os que não têm ramo de limpeza, ficam como estavam.
    $this->assertSame('07:30:00', $row->hora_inicial);
    $this->assertSame('11:45:00', $row->hora_final);
    $this->assertSame(2, $row->tipo_boletim_diferenciado);
    $this->assertSame('{0,5}', $row->tipo_atendimento);
    $this->assertSame(1, $row->tipo_mediacao_didatico_pedagogico);
    $this->assertSame('Turma Completa', $row->nm_turma);
    $this->assertSame('{2,3,4}', $row->dias_semana);
    $this->assertSame(1, $row->nao_informar_educacenso);
});

test('editar com nulo limpa e com falso preserva', function () {
    $id = ($this->full)()->cadastra();

    $object = new clsPmieducarTurma($id, $this->user);
    $object->cod_curso = null;

    foreach (['hora_final', 'hora_inicio_intervalo', 'hora_fim_intervalo', 'tipo_boletim_diferenciado', 'tipo_atendimento', 'tipo_mediacao_didatico_pedagogico'] as $property) {
        $object->{$property} = null;
    }

    foreach ([
        'hora_inicial_matutino', 'hora_inicio_intervalo_matutino', 'hora_fim_intervalo_matutino', 'hora_final_matutino',
        'hora_inicial_vespertino', 'hora_inicio_intervalo_vespertino', 'hora_fim_intervalo_vespertino', 'hora_final_vespertino',
    ] as $property) {
        $object->{$property} = false;
    }

    $this->assertTrue($object->edita());

    $row = ($this->row)($id);

    $this->assertNull($row->hora_final);
    $this->assertNull($row->hora_inicio_intervalo);
    $this->assertNull($row->hora_fim_intervalo);
    $this->assertNull($row->tipo_boletim_diferenciado);
    $this->assertNull($row->tipo_atendimento);
    $this->assertNull($row->tipo_mediacao_didatico_pedagogico);
    $this->assertSame('07:30:00', $row->hora_inicial_matutino);
    $this->assertSame('17:15:00', $row->hora_final_vespertino);
});

// Defeito conhecido: a edição monta a atribuição de `hora_inicial` duas
// vezes no mesmo UPDATE; com a hora informada, ou nula, o banco recusa.
test('defeito editar com hora inicial estoura', function () {
    $object = new clsPmieducarTurma($this->scenario->schoolClass()->getKey(), $this->user, hora_inicial: '07:30:00');
    $object->cod_curso = null;

    $this->expectException(Exception::class);

    $object->edita();
});

test('defeito editar com hora inicial nula estoura', function () {
    $object = new clsPmieducarTurma($this->scenario->schoolClass()->getKey(), $this->user);
    $object->cod_curso = null;
    $object->hora_inicial = null;

    $this->expectException(Exception::class);

    $object->edita();
});

// Defeito conhecido: a edição grava `cod_curso`, coluna que não existe na
// tabela de turmas (o nome é `ref_cod_curso`).
test('defeito editar com cod curso estoura', function () {
    $object = new clsPmieducarTurma($this->scenario->schoolClass()->getKey(), $this->user);
    $object->cod_curso = $this->scenario->course->getKey();

    $this->expectException(Exception::class);

    $object->edita();
});

// Defeito conhecido: a classe aceita uma data de fechamento e tenta gravá-la
// em `data_fechamento`, coluna que não existe na tabela de turmas.
test('defeito cadastrar com data de fechamento estoura', function () {
    $object = ($this->full)();
    $object->data_fechamento = '2030-12-20';

    $this->expectException(Exception::class);

    $object->cadastra();
});

test('defeito editar com data de fechamento estoura', function () {
    $object = new clsPmieducarTurma($this->scenario->schoolClass()->getKey(), $this->user, data_fechamento: '2030-12-20');
    $object->cod_curso = null;

    $this->expectException(Exception::class);

    $object->edita();
});

test('detalha e informa o ano letivo alunos inep e disciplina dispensada', function () {
    $discipline = $this->scenario->discipline('Ensino Religioso')->getKey();
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'ref_cod_disciplina_dispensada' => $discipline]);
    $id = $schoolClass->getKey();
    $object = new clsPmieducarTurma($id);

    $this->assertSame('Turma Alfa', $object->detalhe()['nm_turma']);
    $this->assertNotFalse($object->checaAnoLetivoEmAndamento());
    $this->assertEmpty($object->possuiAlunosVinculados());
    $this->assertNull($object->getInep());
    $this->assertTrue(clsPmieducarTurma::verificaDisciplinaDispensada($id, $discipline));
    $this->assertNull(clsPmieducarTurma::getDisciplinaDispensada($this->scenario->schoolClass()->getKey()));

    $this->scenario->enrolled('Ana Alves', $schoolClass);
    SchoolClassInepFactory::new()->create(['cod_turma' => $id, 'cod_turma_inep' => 12345678]);
    SchoolClassInepFactory::new()->create(['cod_turma' => $id, 'cod_turma_inep' => 87654321, 'turma_turno_id' => 1]);
    LegacySchoolAcademicYear::query()->where('ref_cod_escola', $this->scenario->school->getKey())->update(['andamento' => 2]);

    $this->assertNotEmpty($object->possuiAlunosVinculados());
    $this->assertEquals(12345678, $object->getInep());
    $this->assertEquals(87654321, $object->getInepTurno(1));
    $this->assertEmpty($object->checaAnoLetivoEmAndamento());
});

test('lista por cada filtro', function () {
    [$wanted, $unwanted] = ($this->pair)();

    $both = [$wanted->getKey(), $unwanted->getKey()];

    $list = function (array $filters) use ($both) {
        $rows = (new clsPmieducarTurma)->lista(...$filters) ?: [];

        return array_values(array_intersect(array_column($rows, 'cod_turma'), $both));
    };

    foreach (($this->filters)($wanted) as $filters) {
        $this->assertSame([$wanted->getKey()], $list($filters), json_encode($filters));
    }

    $school = ['int_ref_ref_cod_escola' => $wanted->ref_ref_cod_escola];

    $this->assertSame([$wanted->getKey()], $list($school + ['somenteAnoLetivoEmAndamento' => true]));
    $this->assertSame([], $list($school + ['int_ativo' => 0]));
    $this->assertSame([], $list($school + ['visivel' => false]));
});

// Defeito conhecido: o filtro por nome de lista() monta a consulta com uma
// variável que não existe; a condição vira "qualquer turma tem nome" e não
// filtra nada.
test('defeito o filtro por nome de lista nao filtra', function () {
    [$wanted, $unwanted] = ($this->pair)();

    $ids = array_column((new clsPmieducarTurma)->lista(str_nm_turma: 'Alfa'), 'cod_turma');

    $this->assertContains($wanted->getKey(), $ids);
    $this->assertContains($unwanted->getKey(), $ids);
});

test('lista pode esconder as turmas fora do educacenso', function () {
    $hidden = $this->scenario->schoolClass(['nao_informar_educacenso' => 1])->getKey();

    $object = new clsPmieducarTurma;

    $this->assertEquals([$hidden], array_column($object->lista(int_cod_turma: $hidden), 'cod_turma'));

    $object->listarNaoInformarEducacenso = false;

    $this->assertFalse($object->lista(int_cod_turma: $hidden));

    $object->listarNaoInformarEducacenso = true;

    $object->setCamposLista('cod_turma');

    $this->assertEquals([$hidden], $object->lista(int_cod_turma: $hidden));
});

test('lista3 por cada filtro', function () {
    [$wanted, $unwanted] = ($this->pair)();

    $both = [$wanted->getKey(), $unwanted->getKey()];

    $list = function (array $filters) use ($both) {
        $rows = (new clsPmieducarTurma)->lista3(...$filters) ?: [];

        return array_values(array_intersect(array_column($rows, 'cod_turma'), $both));
    };

    foreach (($this->filters)($wanted) as $filters) {
        $this->assertSame([$wanted->getKey()], $list($filters), json_encode($filters));
    }

    $school = ['int_ref_ref_cod_escola' => $wanted->ref_ref_cod_escola];

    $this->assertSame([], $list($school + ['int_ativo' => 0]));
    $this->assertSame([], $list($school + ['visivel' => false]));

    $object = new clsPmieducarTurma;
    $object->setCamposLista('cod_turma');

    $this->assertEquals([$wanted->getKey()], $object->lista3(int_cod_turma: $wanted->getKey()));
    $this->assertFalse($object->lista3(int_cod_turma: 999999999));
});

test('lista3 acha a turma pela serie e pelo curso das series adicionais', function () {
    $second = $this->scenario->grade('Segundo Ano');
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Multi', 'multiseriada' => 1]);

    LegacySchoolClassGradeFactory::new()->create([
        'escola_id' => $this->scenario->school,
        'serie_id' => $second,
        'turma_id' => $schoolClass,
    ]);

    $object = new clsPmieducarTurma;

    $this->assertContains($schoolClass->getKey(), array_column($object->lista3(int_ref_ref_cod_serie: $second->getKey()), 'cod_turma'));
    $this->assertContains($schoolClass->getKey(), array_column($object->lista3(int_ref_cod_curso: $this->scenario->course->getKey()), 'cod_turma'));
    $this->assertContains($schoolClass->getKey(), array_column($object->lista(int_ref_ref_cod_serie: $second->getKey()), 'cod_turma'));
});

test('lista3 restringe as escolas do usuario informado', function () {
    [$wanted, $unwanted] = ($this->pair)();

    LegacyUserSchoolFactory::new()->create(['ref_cod_usuario' => $this->user, 'ref_cod_escola' => $wanted->ref_ref_cod_escola]);

    $object = new clsPmieducarTurma;
    $object->codUsuario = $this->user;

    $ids = array_column($object->lista3(), 'cod_turma');

    $this->assertContains($wanted->getKey(), $ids);
    $this->assertNotContains($unwanted->getKey(), $ids);
});

test('lista3 filtra pelo nome', function () {
    [$wanted] = ($this->pair)();

    $this->assertContains($wanted->getKey(), array_column((new clsPmieducarTurma)->lista3(str_nm_turma: 'Alfa'), 'cod_turma'));
    $this->assertFalse((new clsPmieducarTurma)->lista3(str_nm_turma: 'Nome que nenhuma turma tem'));
});
