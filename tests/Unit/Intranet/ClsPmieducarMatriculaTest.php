<?php

use App\Models\LegacyRegistration;
use Database\Factories\LegacyGradeSequenceFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * O que a classe legada de matrícula faz e as telas de matrícula não
 * alcançam: a gravação e a edição de cada campo, os filtros das duas
 * listagens, o detalhe pela escola e as consultas e marcações avulsas.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->row = function (int $id): object {
        return DB::table('pmieducar.matricula')->where('cod_matricula', $id)->first();
    };

    $this->fresh = function (): clsPmieducarMatricula {
        return new clsPmieducarMatricula(
            ref_ref_cod_escola: $this->scenario->school->getKey(),
            ref_ref_cod_serie: $this->scenario->grade->getKey(),
            ref_usuario_cad: $this->user,
            ref_cod_aluno: $this->scenario->student('Ana Alves')->getKey(),
            aprovado: App_Model_MatriculaSituacao::EM_ANDAMENTO,
            ativo: 1,
            ano: 2030,
            ultima_matricula: 1,
            modulo: 2,
            formando: 1,
            descricao_reclassificacao: "Reclassificado por 'desempenho'",
            matricula_reclassificacao: 1,
            ref_cod_curso: $this->scenario->course->getKey(),
            matricula_transferencia: 't',
            semestre: 2,
            data_matricula: '2030-03-10',
            data_cancel: '2030-06-30',
            observacoes: "Veio da rede 'particular'",
            modalidadeEnsino: clsPmieducarMatricula::MODELO_EAD,
        );
    };

    /**
     * Duas matrículas que diferem em tudo o que as listagens filtram: a
     * primeira é a que cada filtro deve devolver sozinha.
     *
     * @return array{0: LegacyRegistration, 1: LegacyRegistration, 2: int}
     */
    $this->pair = function (): array {
        $second = LegacyUserFactory::new()->admin()->create();
        $other = EnrollmentScenario::for($second, 2029);

        $wanted = $this->scenario->registration([
            'ref_cod_aluno' => $this->scenario->student('Ana Alves', ['analfabeto' => 1]),
            'aprovado' => App_Model_MatriculaSituacao::APROVADO,
            'ref_usuario_cad' => $this->user,
            'ref_usuario_exc' => $this->user,
            'data_cadastro' => '2030-03-10 08:00:00',
            'data_exclusao' => '2030-04-10 08:00:00',
            'modulo' => 2,
            'matricula_reclassificacao' => 1,
            'matricula_transferencia' => true,
            'semestre' => 2,
        ]);

        $schoolClass = $this->scenario->schoolClass();
        $this->scenario->enroll($wanted, $schoolClass);

        $other->course->update(['padrao_ano_escolar' => 0]);

        $unwanted = $other->registration([
            'ref_cod_aluno' => $other->student('Bruno Barros', ['analfabeto' => 0]),
            'ref_usuario_cad' => $second->getKey(),
            'ref_usuario_exc' => $second->getKey(),
            'data_cadastro' => '2030-06-10 08:00:00',
            'ultima_matricula' => 0,
            'modulo' => 1,
            'semestre' => 1,
        ]);

        DB::table('cadastro.fisica_deficiencia')->insert([
            'ref_idpes' => $wanted->student->ref_idpes,
            'ref_cod_deficiencia' => DB::table('cadastro.deficiencia')->insertGetId(['nm_deficiencia' => 'Baixa visão'], 'cod_deficiencia'),
        ]);

        return [$wanted, $unwanted, $schoolClass->getKey()];
    };

    /**
     * @return array<int, array<string, mixed>>
     */
    $this->filters = function (LegacyRegistration $wanted, int $schoolClass): array {
        $school = ['int_ref_ref_cod_escola' => $wanted->ref_ref_cod_escola];

        return [
            ['int_cod_matricula' => $wanted->getKey()],
            $school,
            ['int_ref_ref_cod_serie' => $wanted->ref_ref_cod_serie],
            ['int_ref_usuario_exc' => $this->user],
            ['int_ref_usuario_cad' => $this->user],
            ['ref_cod_aluno' => $wanted->ref_cod_aluno],
            ['ref_cod_aluno' => [$wanted->ref_cod_aluno]],
            $school + ['int_aprovado' => App_Model_MatriculaSituacao::APROVADO],
            $school + ['int_aprovado' => [App_Model_MatriculaSituacao::APROVADO, App_Model_MatriculaSituacao::REPROVADO]],
            ['date_data_cadastro_ini' => '2030-03-01', 'date_data_cadastro_fim' => '2030-04-01'],
            ['date_data_exclusao_ini' => '2030-04-01', 'date_data_exclusao_fim' => '2030-05-01'],
            $school + ['int_ativo' => 1],
            $school + ['int_ano' => 2030],
            ['int_ref_cod_curso' => $wanted->ref_cod_curso],
            $school + ['int_padrao_ano_escolar' => 1],
            $school + ['int_ref_cod_instituicao' => $this->scenario->institution],
            $school + ['int_ultima_matricula' => 1],
            $school + ['int_modulo' => 2],
            $school + ['int_analfabeto' => 1],
            $school + ['int_matricula_reclassificacao' => 1],
            $school + ['boo_matricula_transferencia' => true],
            $school + ['boo_com_deficiencia' => true],
            $school + ['int_semestre' => 2],
            ['int_ref_cod_turma' => $schoolClass],
            ['int_ref_cod_turma' => $schoolClass, 'matriculas_turmas_transferidas_abandono' => true],
            ['arr_int_cod_matricula' => [$wanted->getKey()]],
            $school + ['int_ano' => 2030, 'int_mes_defasado' => 3],
        ];
    };

    $user = LegacyUserFactory::new()->admin()->create();

    $this->user = $user->getKey();
    $this->scenario = EnrollmentScenario::for($user, 2030);
});

test('cadastra com todos os campos', function () {
    $object = ($this->fresh)();
    $object->turno_pre_matricula = 2;
    $object->dependencia = true;

    $id = $object->cadastra();
    $row = ($this->row)($id);

    $this->assertSame(2, $row->modulo);
    $this->assertSame(1, $row->formando);
    $this->assertSame(1, $row->matricula_reclassificacao);
    $this->assertSame("Reclassificado por 'desempenho'", $row->descricao_reclassificacao);
    $this->assertTrue($row->matricula_transferencia);
    $this->assertSame(2, $row->semestre);
    $this->assertStringStartsWith('2030-03-10', $row->data_matricula);
    $this->assertStringStartsWith('2030-06-30', $row->data_cancel);
    $this->assertSame("Veio da rede 'particular'", $row->observacoes);
    $this->assertSame(2, $row->turno_pre_matricula);
    $this->assertTrue($row->dependencia);
    $this->assertSame(clsPmieducarMatricula::MODELO_EAD, $row->modalidade_ensino);
    $this->assertSame(1, $row->ativo);
});

test('nao cadastra sem os campos obrigatorios', function () {
    $this->assertFalse((new clsPmieducarMatricula)->cadastra());
});

// Defeito conhecido: a classe grava o motivo de abandono em `ref_cod_abandono`,
// coluna que não existe na tabela (o nome é `ref_cod_abandono_tipo`).
test('defeito cadastrar com motivo de abandono estoura', function () {
    $object = ($this->fresh)();
    $object->ref_cod_abandono = 1;

    $this->expectException(Exception::class);

    $object->cadastra();
});

test('defeito editar com motivo de abandono estoura', function () {
    $object = new clsPmieducarMatricula($this->scenario->registration()->getKey());
    $object->ref_cod_abandono = 1;

    $this->expectException(Exception::class);

    $object->edita();
});

test('avanca o modulo da matricula', function () {
    $registration = $this->scenario->registration(['modulo' => 1]);

    $this->assertTrue((new clsPmieducarMatricula($registration->getKey(), ref_usuario_exc: $this->user))->avancaModulo());
    $this->assertSame(2, ($this->row)($registration->getKey())->modulo);
    $this->assertFalse((new clsPmieducarMatricula($registration->getKey()))->avancaModulo());
});

test('edita cada campo', function () {
    $registration = $this->scenario->registration();
    $other = EnrollmentScenario::for(LegacyUserFactory::new()->admin()->create(), 2031);
    $student = $other->student('Bruno Barros');

    $object = new clsPmieducarMatricula(
        cod_matricula: $registration->getKey(),
        ref_ref_cod_escola: $other->school->getKey(),
        ref_ref_cod_serie: $other->grade->getKey(),
        ref_usuario_exc: $this->user,
        ref_usuario_cad: $this->user,
        ref_cod_aluno: $student->getKey(),
        aprovado: App_Model_MatriculaSituacao::APROVADO,
        data_exclusao: '2030-12-20',
        ativo: 0,
        ano: 2031,
        ultima_matricula: 0,
        modulo: 3,
        formando: 1,
        descricao_reclassificacao: 'Descrição nova',
        matricula_reclassificacao: 1,
        ref_cod_curso: $other->course->getKey(),
        semestre: 2,
        data_matricula: '2031-02-10',
        data_cancel: '2031-06-30',
        observacoes: 'Observações novas',
        modalidadeEnsino: clsPmieducarMatricula::MODELO_SEMIPRESENCIAL,
    );

    $object->turno_pre_matricula = 3;
    $object->dependencia = true;

    $this->assertTrue($object->edita());

    $row = ($this->row)($registration->getKey());

    $this->assertSame($other->school->getKey(), $row->ref_ref_cod_escola);
    $this->assertSame($other->grade->getKey(), $row->ref_ref_cod_serie);
    $this->assertSame($other->course->getKey(), $row->ref_cod_curso);
    $this->assertSame($student->getKey(), $row->ref_cod_aluno);
    $this->assertSame(App_Model_MatriculaSituacao::APROVADO, $row->aprovado);
    $this->assertSame(0, $row->ativo);
    $this->assertSame(2031, $row->ano);
    $this->assertSame(0, $row->ultima_matricula);
    $this->assertSame(3, $row->modulo);
    $this->assertSame(1, $row->formando);
    $this->assertSame(1, $row->matricula_reclassificacao);
    $this->assertSame('Descrição nova', $row->descricao_reclassificacao);
    $this->assertSame(2, $row->semestre);
    $this->assertStringStartsWith('2031-02-10', $row->data_matricula);
    $this->assertStringStartsWith('2031-06-30', $row->data_cancel);
    $this->assertStringStartsWith('2030-12-20', $row->data_exclusao);
    $this->assertSame('Observações novas', $row->observacoes);
    $this->assertSame(3, $row->turno_pre_matricula);
    $this->assertTrue($row->dependencia);
    $this->assertSame(clsPmieducarMatricula::MODELO_SEMIPRESENCIAL, $row->modalidade_ensino);
    $this->assertSame($this->user, $row->ref_usuario_exc);
});

test('edita limpando o cancelamento e as observacoes', function () {
    $registration = $this->scenario->registration(['data_cancel' => '2030-06-30', 'observacoes' => 'Antigas']);

    $this->assertTrue((new clsPmieducarMatricula($registration->getKey(), observacoes: null))->edita());

    $row = ($this->row)($registration->getKey());

    $this->assertNull($row->data_cancel);
    $this->assertNull($row->observacoes);
});

test('nao edita nem detalha sem codigo', function () {
    $empty = new clsPmieducarMatricula;

    $this->assertFalse($empty->edita());
    $this->assertFalse($empty->detalhe());
    $this->assertFalse($empty->existe());
    $this->assertFalse($empty->cadastraObs('x', 1, 1));
    $this->assertFalse($empty->cadastraObservacaoFalecido('x'));
    $this->assertFalse($empty->setPessoaFalecido());
    $this->assertFalse($empty->setSaidaEscola('x', '2030-12-20'));
    $this->assertFalse($empty->matriculaAlunoAndamento(null, null));
});

test('detalha pelo codigo respeitando ativo e ultima matricula e pela escola', function () {
    $registration = $this->scenario->registration();
    $id = $registration->getKey();

    $this->assertEquals($id, (new clsPmieducarMatricula($id, ativo: 1, ultima_matricula: 1))->detalhe()['cod_matricula']);
    $this->assertNotFalse((new clsPmieducarMatricula($id))->existe());

    $registration->update(['ultima_matricula' => 0]);

    $this->assertEmpty((new clsPmieducarMatricula($id, ativo: 1, ultima_matricula: 1))->detalhe());

    // Sem código, o detalhe procura pela escola.
    $bySchool = (new clsPmieducarMatricula(ref_ref_cod_escola: $this->scenario->school->getKey()))->detalhe();

    $this->assertEquals($id, $bySchool['cod_matricula']);
});

test('informa as series que iniciam uma sequencia', function () {
    $second = $this->scenario->grade('Segundo Ano');
    $third = $this->scenario->grade('Terceiro Ano');

    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $this->scenario->grade, 'ref_serie_destino' => $second]);
    LegacyGradeSequenceFactory::new()->create(['ref_serie_origem' => $second, 'ref_serie_destino' => $third]);

    $origins = array_column((new clsPmieducarMatricula)->getInicioSequencia(), 'ref_serie_origem');

    $this->assertContains($this->scenario->grade->getKey(), $origins);
    $this->assertNotContains($second->getKey(), $origins);
});

test('registra o falecimento na matricula e na pessoa', function () {
    $registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves')]);

    $this->assertTrue((new clsPmieducarMatricula($registration->getKey()))->cadastraObservacaoFalecido(''));

    $row = ($this->row)($registration->getKey());

    $this->assertSame(App_Model_MatriculaSituacao::FALECIDO, $row->aprovado);
    $this->assertSame('Não informado', $row->observacao);
    $this->assertTrue(DB::table('cadastro.fisica')->where('idpes', $registration->student->ref_idpes)->value('falecido'));
});

test('guarda a observacao do falecimento e a data de cadastro informada', function () {
    $registration = $this->scenario->registration();

    (new clsPmieducarMatricula($registration->getKey()))->cadastraObservacaoFalecido('Óbito informado pela família');

    $this->assertSame('Óbito informado pela família', ($this->row)($registration->getKey())->observacao);
    $this->assertSame('2030-03-10 08:00:00', (new clsPmieducarMatricula(data_cadastro: '2030-03-10 08:00:00'))->data_cadastro);
});

test('registra a saida da escola', function () {
    $registration = $this->scenario->registration();

    $this->assertTrue((new clsPmieducarMatricula($registration->getKey()))->setSaidaEscola('', '2030-12-20'));

    $row = ($this->row)($registration->getKey());

    $this->assertTrue($row->saida_escola);
    $this->assertSame('Não informado', $row->observacao);
    $this->assertStringStartsWith('2030-12-20', $row->data_saida_escola);

    (new clsPmieducarMatricula($registration->getKey()))->setSaidaEscola('Concluiu o curso', '2030-12-21');

    $this->assertSame('Concluiu o curso', ($this->row)($registration->getKey())->observacao);
});

test('informa a matricula em andamento do aluno no ano', function () {
    $registration = $this->scenario->registration();
    $object = new clsPmieducarMatricula;

    $this->assertEquals($registration->getKey(), $object->matriculaAlunoAndamento($registration->ref_cod_aluno, 2030)['cod_matricula']);
    $this->assertEmpty($object->matriculaAlunoAndamento($registration->ref_cod_aluno, 2029));
});

test('lista por cada filtro', function () {
    [$wanted, $unwanted, $schoolClass] = ($this->pair)();

    $both = [$wanted->getKey(), $unwanted->getKey()];

    $list = function (array $filters) use ($both) {
        $rows = (new clsPmieducarMatricula)->lista(...$filters) ?: [];

        return array_values(array_intersect(array_column($rows, 'cod_matricula'), $both));
    };

    foreach (($this->filters)($wanted, $schoolClass) as $filters) {
        $this->assertSame([$wanted->getKey()], $list($filters), json_encode($filters));
    }

    $school = ['int_ref_ref_cod_escola' => $wanted->ref_ref_cod_escola];

    $this->assertSame([], $list($school + ['int_ativo' => 0]));
    $this->assertSame([], $list($school + ['boo_com_deficiencia' => false]));
});

test('lista com a data de nascimento e com um campo so', function () {
    [$wanted] = ($this->pair)();

    $rows = (new clsPmieducarMatricula)->lista(int_cod_matricula: $wanted->getKey(), boo_data_nasc: true);

    $this->assertArrayHasKey('data_nasc', $rows[0]);
    $this->assertSame('Ana Alves', $rows[0]['nome']);

    $object = new clsPmieducarMatricula;
    $object->setCamposLista('cod_matricula');

    $this->assertEquals([$wanted->getKey()], $object->lista(int_cod_matricula: $wanted->getKey()));
    $this->assertFalse($object->lista(int_cod_matricula: 999999999));
});

test('lista transferidos por cada filtro', function () {
    [$wanted, $unwanted, $schoolClass] = ($this->pair)();

    $both = [$wanted->getKey(), $unwanted->getKey()];

    $list = function (array $filters) use ($both) {
        $rows = (new clsPmieducarMatricula)->lista_transferidos(...$filters) ?: [];

        return array_values(array_intersect(array_column($rows, 'cod_matricula'), $both));
    };

    foreach (($this->filters)($wanted, $schoolClass) as $filters) {
        $this->assertSame([$wanted->getKey()], $list($filters), json_encode($filters));
    }

    $school = ['int_ref_ref_cod_escola' => $wanted->ref_ref_cod_escola];

    $this->assertSame([], $list($school + ['int_ativo' => 0]));
    $this->assertSame([], $list($school + ['boo_com_deficiencia' => false]));

    $rows = (new clsPmieducarMatricula)->lista_transferidos(int_cod_matricula: $wanted->getKey(), boo_data_nasc: true);

    $this->assertArrayHasKey('data_nasc', $rows[0]);

    $object = new clsPmieducarMatricula;
    $object->setCamposLista('cod_matricula');

    $this->assertEquals([$wanted->getKey()], $object->lista_transferidos(int_cod_matricula: $wanted->getKey()));
    $this->assertFalse($object->lista_transferidos(int_cod_matricula: 999999999));
});

// Defeito conhecido: os filtros por formando e por texto de reclassificação
// consultam colunas que não existem na tabela de alunos.
test('defeito o filtro por formando estoura', function () {
    $this->expectException(Exception::class);

    (new clsPmieducarMatricula)->lista(int_formando: 1);
});

test('defeito o filtro por texto de reclassificacao estoura', function () {
    $this->expectException(Exception::class);

    (new clsPmieducarMatricula)->lista(int_matricula_reclassificacao: 'texto');
});

test('defeito o filtro por formando estoura nos transferidos', function () {
    $this->expectException(Exception::class);

    (new clsPmieducarMatricula)->lista_transferidos(int_formando: 1);
});

test('defeito o filtro por texto de reclassificacao estoura nos transferidos', function () {
    $this->expectException(Exception::class);

    (new clsPmieducarMatricula)->lista_transferidos(int_matricula_reclassificacao: 'texto');
});
