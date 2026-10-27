<?php

use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacyGrade;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolGrade;
use App\Models\SchoolClassInep;
use App\Services\iDiarioService;
use App\Services\SchoolClassInepService;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->school = $this->scenario->school;
    $this->course = $this->scenario->course;
    $this->first = $this->scenario->grade;
    $this->second = $this->scenario->grade('Segundo Ano');
    $this->third = $this->scenario->grade('Terceiro Ano');
    $this->math = $this->scenario->discipline('Matemática');

    // As validações leem a regra de avaliação da série no ano.
    $this->rule = fn (LegacyGrade $grade, array $attributes = []) => $this->scenario->evaluationRule($attributes + [
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
        'tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA,
    ], $grade);
    $this->firstRule = ($this->rule)($this->first);

    $this->config = LegacyGeneralConfigurationFactory::new()->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'url_novo_educacao' => null,
        'token_novo_educacao' => null,
    ]);

    $this->type = LegacySchoolClassTypeFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution]);
    $this->morning = $this->scenario->period(1)->getKey();
    $this->stageType = DB::table('pmieducar.modulo')->where('ref_cod_instituicao', $this->scenario->institution)->value('cod_modulo');

    // O formulário da turma como o legado envia, no mínimo para cadastrar.
    $this->form = fn (array $overrides = []) => $overrides + [
        'nm_turma' => 'Turma A',
        'sgl_turma' => 'A',
        'max_aluno' => 25,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->school->getKey(),
        'ref_cod_curso' => $this->course->getKey(),
        'ref_cod_serie' => $this->first->getKey(),
        'ano_letivo' => 2030,
        'turma_turno_id' => $this->morning,
        'ref_cod_turma_tipo' => $this->type->getKey(),
        'tipo_boletim' => 1,
        'tipo_mediacao_didatico_pedagogico' => App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL,
        'data_inicio' => [''],
        'data_fim' => [''],
        'dias_letivos' => [''],
        'ref_cod_modulo' => '',
    ];

    // A edição manda a chave da turma e a cadeia escola, curso e série com
    // outro nome de campo.
    $this->edit = fn (LegacySchoolClass $schoolClass, array $overrides = []) => ($this->form)($overrides + [
        'cod_turma' => $schoolClass->getKey(),
        'nm_turma' => $schoolClass->nm_turma,
        'ref_cod_escola_' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_curso_' => $schoolClass->ref_cod_curso,
        'ref_cod_serie_' => $schoolClass->ref_ref_cod_serie,
        'turma_turno_id' => $schoolClass->turma_turno_id,
    ]);

    $this->schoolClass = fn (array $attributes = []) => $this->scenario->schoolClass($attributes + [
        'nm_turma' => 'Turma A',
        'turma_turno_id' => $this->morning,
        'ref_cod_turma_tipo' => $this->type,
    ]);

    $this->multi = fn (array $grades, array $overrides = []) => $overrides + [
        'multiseriada' => 1,
        'mult_serie_id' => array_map(fn (LegacyGrade $grade) => $grade->getKey(), $grades),
        'mult_boletim_id' => array_fill(0, count($grades), 1),
        'mult_boletim_diferenciado_id' => array_fill(0, count($grades), ''),
    ];

    $this->store = fn (array $data) => $this->post('/turma', $data);
    $this->delete = fn (array $data) => $this->delete('/turma', $data);

    $this->idiario = function () {
        $this->config->forceFill(['url_novo_educacao' => 'https://idiario.example', 'token_novo_educacao' => 'token'])->save();

        return $this->mock(iDiarioService::class);
    };
});

test('cadastra a turma com os campos do censo, os componentes, as etapas e o código Inep', function () {
    ($this->store)(($this->form)([
        'dias_semana' => [2, 3, 4],
        'tipo_atendimento' => (string) TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO,
        'etapa_educacenso' => 39,
        'cod_curso_profissional' => ['12'],
        'cod_eixo_curso_profissional' => '7',
        'organizacao_curricular' => [1],
        'formas_organizacao_turma' => '',
        'area_itinerario' => ['1', '2'],
        'cod_curso_profissional_intinerario' => ['9'],
        'carga_horaria_total' => '800',
        'nao_informar_educacenso' => 'on',
        'visivel' => 1,
        'codigo_inep_educacenso' => '12345',
        'disciplinas' => [$this->math->getKey() => $this->math->getKey()],
        'carga_horaria' => [$this->math->getKey() => '80'],
        'docente_vinculado' => [$this->math->getKey() => 1],
        'etapas_especificas' => [$this->math->getKey() => 1],
        'etapas_utilizadas' => [$this->math->getKey() => '1,2'],
        'ref_cod_modulo' => $this->stageType,
        'data_inicio' => ['01/02/2030', '01/08/2030'],
        'data_fim' => ['30/06/2030', '15/12/2030'],
        'dias_letivos' => [100, 100],
    ]))
        ->assertOk()
        ->assertExactJson(['msg' => 'Edição efetuada com sucesso.'])
        ->assertSessionHas('success', 'Edição efetuada com sucesso.');

    $schoolClass = LegacySchoolClass::query()->where('nm_turma', 'Turma A')->first();

    expect($schoolClass)->not->toBeNull()
        ->and($schoolClass->ano)->toBe(2030)
        ->and($schoolClass->ref_ref_cod_serie)->toBe($this->first->getKey())
        ->and($schoolClass->ref_ref_cod_escola)->toBe($this->school->getKey())
        ->and($schoolClass->ref_usuario_cad)->toBe($this->user->getKey())
        ->and($schoolClass->multiseriada)->toBe(0)
        ->and($schoolClass->getRawOriginal('dias_semana'))->toBe('{2,3,4}')
        ->and($schoolClass->getRawOriginal('tipo_atendimento'))->toBe('{0}')
        ->and($schoolClass->getRawOriginal('atividades_complementares'))->toBe('{}')
        ->and($schoolClass->etapa_educacenso)->toBe(39)
        ->and($schoolClass->cod_curso_profissional)->toBe(12)
        ->and($schoolClass->cod_eixo_curso_profissional)->toBeNull()
        ->and($schoolClass->getRawOriginal('organizacao_curricular'))->toBe('{1}')
        ->and($schoolClass->getRawOriginal('area_itinerario'))->toBe('{1,2}')
        ->and($schoolClass->cod_curso_profissional_intinerario)->toBe(9)
        ->and($schoolClass->carga_horaria_total)->toBe(800)
        ->and($schoolClass->nao_informar_educacenso)->toBe(1)
        ->and($schoolClass->visivel)->toBeTrue();

    $component = LegacyDisciplineSchoolClass::query()->where('turma_id', $schoolClass->getKey())->first();
    expect($component->componente_curricular_id)->toBe($this->math->getKey())
        ->and((float) $component->carga_horaria)->toBe(80.0)
        ->and($component->docente_vinculado)->toBe(1)
        ->and($component->etapas_especificas)->toBe(1)
        ->and($component->etapas_utilizadas)->toBe('1,2');

    expect($schoolClass->schoolClassStages()->orderBy('sequencial')->pluck('data_fim')->map(fn ($date) => $date->format('Y-m-d'))->all())->toBe(['2030-06-30', '2030-12-15'])
        ->and(SchoolClassInep::query()->where('cod_turma', $schoolClass->getKey())->pluck('cod_turma_inep', 'turma_turno_id')->all())->toBe(['' => 12345]);
});

test('o eixo do curso profissional fica só nas etapas de qualificação', function () {
    ($this->store)(($this->form)(['etapa_educacenso' => 67, 'cod_eixo_curso_profissional' => '7', 'cod_curso_profissional' => ['12'], 'tipo_atendimento' => '']))
        ->assertOk();

    $schoolClass = LegacySchoolClass::query()->where('nm_turma', 'Turma A')->first();

    expect($schoolClass->cod_eixo_curso_profissional)->toBe(7)
        ->and($schoolClass->cod_curso_profissional)->toBeNull()
        ->and($schoolClass->tipo_atendimento)->toBeNull()
        ->and($schoolClass->carga_horaria_total)->toBeNull()
        ->and($schoolClass->visivel)->toBeFalse()
        ->and($schoolClass->nao_informar_educacenso)->toBe(0);
});

test('as atividades complementares ficam quando o tipo de turma as tem, e a classe especial sai quando não é curricular', function () {
    ($this->store)(($this->form)([
        'tipo_atendimento' => (string) TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR,
        'atividades_complementares' => [11, 12],
        'classe_especial' => 1,
    ]))->assertOk();

    $schoolClass = LegacySchoolClass::query()->where('nm_turma', 'Turma A')->first();

    expect($schoolClass->getRawOriginal('atividades_complementares'))->toBe('{11,12}')
        ->and($schoolClass->getRawOriginal('tipo_atendimento'))->toBe('{4}')
        ->and($schoolClass->classe_especial)->toBeNull();
});

test('com o iDiário configurado, os componentes são gravados consultando o serviço', function () {
    ($this->idiario)()->shouldNotReceive('getClassroomsActivityByDiscipline');

    ($this->store)(($this->form)([
        'disciplinas' => [$this->math->getKey() => $this->math->getKey()],
        'carga_horaria' => [$this->math->getKey() => '80'],
    ]))->assertOk();

    expect(LegacyDisciplineSchoolClass::query()->where('componente_curricular_id', $this->math->getKey())->count())->toBe(1);
});

test('edita a turma trocando o nome, limpando os componentes e gravando os códigos Inep dos turnos', function () {
    $schoolClass = ($this->schoolClass)();
    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $this->math,
        'ano_escolar_id' => $this->first,
        'escola_id' => $this->school,
        'turma_id' => $schoolClass,
    ]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 2]);

    ($this->store)(($this->edit)($schoolClass, [
        'nm_turma' => 'Turma B',
        'codigo_inep_matutino' => '111',
        'codigo_inep_vespertino' => '222',
        'ref_cod_modulo' => $this->stageType,
        'data_inicio' => ['01/02/2030'],
        'data_fim' => ['15/12/2030'],
        'dias_letivos' => [200],
    ]))
        ->assertOk();

    $schoolClass->refresh();

    expect($schoolClass->nm_turma)->toBe('Turma B')
        ->and($schoolClass->ref_usuario_exc)->toBe($this->user->getKey())
        ->and(LegacyDisciplineSchoolClass::query()->where('turma_id', $schoolClass->getKey())->count())->toBe(0)
        ->and($schoolClass->schoolClassStages()->count())->toBe(1)
        ->and(SchoolClassInep::query()->where('cod_turma', $schoolClass->getKey())->pluck('cod_turma_inep', 'turma_turno_id')->all())->toBe([1 => 111, 2 => 222]);
});

test('não troca o turno de uma turma com código Inep que tem alunos em turnos parciais', function () {
    $schoolClass = ($this->schoolClass)();
    Cache::put('hasStudentsPartials_' . $schoolClass->getKey(), true, 60);

    ($this->store)(($this->edit)($schoolClass, ['codigo_inep_educacenso' => '12345', 'turma_turno_id' => $this->scenario->period(2)->getKey()]))
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'Esta turma possui turno Matutino e contém os códigos INEP dos turnos parciais informados. Para atender as regras de importação do censo, não é possível alterar o turno da turma.']);
});

test('torna a turma multisseriada, troca uma das séries e volta a ser de uma série só', function () {
    ($this->rule)($this->second);
    ($this->rule)($this->third);
    $schoolClass = ($this->schoolClass)();
    LegacyDisciplineSchoolClassFactory::new()->create(['componente_curricular_id' => $this->math, 'ano_escolar_id' => $this->first, 'escola_id' => $this->school, 'turma_id' => $schoolClass]);

    $grades = fn () => LegacySchoolClassGrade::query()->where('turma_id', $schoolClass->getKey())->orderBy('serie_id')->pluck('serie_id')->all();

    ($this->store)(($this->edit)($schoolClass, ($this->multi)([$this->first, $this->second])))->assertOk();

    expect($grades())->toBe([$this->first->getKey(), $this->second->getKey()])
        ->and($schoolClass->refresh()->multiseriada)->toBe(1)
        ->and(LegacyDisciplineSchoolClass::query()->where('turma_id', $schoolClass->getKey())->count())->toBe(0);

    ($this->store)(($this->edit)($schoolClass, ($this->multi)([$this->first, $this->third], ['mult_boletim_diferenciado_id' => [2, 2]])))->assertOk();

    expect($grades())->toBe([$this->first->getKey(), $this->third->getKey()])
        ->and(LegacySchoolClassGrade::query()->where('turma_id', $schoolClass->getKey())->pluck('boletim_diferenciado_id')->all())->toBe([2, 2]);

    // Ao voltar, só as outras séries saem; a principal fica na tabela.
    ($this->store)(($this->edit)($schoolClass))->assertOk();

    expect($grades())->toBe([$this->first->getKey()])
        ->and($schoolClass->refresh()->multiseriada)->toBe(0);
});

test('recusa as séries de uma turma multisseriada', function (Closure $scenario) {
    [$schoolClass, $grades, $message] = $scenario->call($this);

    ($this->store)(($this->edit)($schoolClass, ($this->multi)($grades)))
        ->assertStatus(422)
        ->assertExactJson(['msg' => $message]);
})->with([
    'nenhuma série' => fn () => [($this->schoolClass)(), [], 'Você deve selecionar pelo menos 2 séries em turmas multisseriadas.'],
    'uma série só' => fn () => [($this->schoolClass)(), [$this->first], 'Você deve selecionar pelo menos 2 séries em turmas multisseriadas.'],
    'séries repetidas' => fn () => [($this->schoolClass)(), [$this->first, $this->first], 'Não é possível adicionar séries iguais em turmas multisseriadas.'],
    'tipos de presença diferentes' => function () {
        ($this->rule)($this->second, ['tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE]);

        return [($this->schoolClass)(), [$this->first, $this->second], 'As séries selecionadas devem possuir o mesmo tipo de apuração de presença (geral ou por componente).'];
    },
    'pareceres descritivos diferentes' => function () {
        ($this->rule)($this->second, ['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL]);

        return [($this->schoolClass)(), [$this->first, $this->second], 'As séries selecionadas devem possuir o mesmo tipo de parecer descritivo.'];
    },
    'pareceres descritivos diferentes pela regra diferenciada da escola' => function () {
        $this->school->forceFill(['utiliza_regra_diferenciada' => true])->save();
        $differentiated = ($this->rule)($this->third, ['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL]);
        ($this->rule)($this->second);
        LegacyEvaluationRuleGradeYear::query()->where('serie_id', $this->second->getKey())->update(['regra_avaliacao_diferenciada_id' => $differentiated->getKey()]);

        return [($this->schoolClass)(), [$this->first, $this->second], 'As séries selecionadas devem possuir o mesmo tipo de parecer descritivo.'];
    },
    'recuperações diferentes' => function () {
        ($this->rule)($this->second, ['tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS]);

        return [($this->schoolClass)(), [$this->first, $this->second], 'As séries selecionadas devem possuir o mesmo tipo de recuperação.'];
    },
    'boletim diferenciado obrigatório' => function () {
        ($this->rule)($this->second, ['regra_diferenciada_id' => $this->firstRule->getKey()]);

        return [($this->schoolClass)(), [$this->first, $this->second], "O campo '<b>Boletim diferenciado</b>' é obrigatório para a(s) série(s): Segundo Ano, pois a regra de avaliação possui regra diferenciada definida."];
    },
    'série removida com matrículas' => function () {
        ($this->rule)($this->second);
        ($this->rule)($this->third);
        $schoolClass = ($this->schoolClass)(['multiseriada' => 1]);
        foreach ([$this->first, $this->second] as $grade) {
            LegacySchoolClassGrade::query()->create(['escola_id' => $this->school->getKey(), 'serie_id' => $grade->getKey(), 'turma_id' => $schoolClass->getKey(), 'boletim_id' => 1]);
        }
        $this->scenario->enroll($this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Bia Borges'), 'ref_ref_cod_serie' => $this->second]), $schoolClass);

        return [$schoolClass, [$this->first, $this->third], 'Não é possível remover a(s) série(s): Segundo Ano, pois existem matrículas vinculadas.'];
    },
    'série original com matrículas fora da turma multisseriada' => function () {
        ($this->rule)($this->second);
        ($this->rule)($this->third);
        $schoolClass = ($this->schoolClass)();
        $this->scenario->enrolled('Bia Borges', $schoolClass);

        return [$schoolClass, [$this->second, $this->third], 'Não foi possível alterar a turma para ser multisseriada, pois a série original possui matrículas vinculadas.'];
    },
]);

test('não volta a ser de uma série só enquanto as outras séries têm matrículas', function () {
    $schoolClass = ($this->schoolClass)(['multiseriada' => 1]);
    foreach ([$this->first, $this->second] as $grade) {
        LegacySchoolClassGrade::query()->create(['escola_id' => $this->school->getKey(), 'serie_id' => $grade->getKey(), 'turma_id' => $schoolClass->getKey(), 'boletim_id' => 1]);
    }
    $this->scenario->enroll($this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Bia Borges'), 'ref_ref_cod_serie' => $this->second]), $schoolClass);

    ($this->store)(($this->edit)($schoolClass))
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'Não é possível remover a(s) série(s): Segundo Ano, pois existem matrículas vinculadas.']);
});

test('troca a série de uma turma sem matrículas', function () {
    ($this->rule)($this->second);
    $schoolClass = ($this->schoolClass)();

    ($this->store)(($this->edit)($schoolClass, ['ref_cod_serie_' => $this->second->getKey()]))->assertOk();

    expect($schoolClass->refresh()->ref_ref_cod_serie)->toBe($this->second->getKey());
});

test('não troca a série de uma turma com matrículas', function () {
    ($this->rule)($this->second);
    $schoolClass = ($this->schoolClass)();
    $this->scenario->enrolled('Bia Borges', $schoolClass);

    ($this->store)(($this->edit)($schoolClass, ['ref_cod_serie_' => $this->second->getKey()]))
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'Não é possível alterar a série da turma pois a mesma possui matrículas vinculadas.']);
});

test('não cadastra outra turma enquanto a série bloqueia o cadastro com vagas em aberto', function () {
    LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->where('ref_cod_serie', $this->first->getKey())->update(['bloquear_cadastro_turma_para_serie_com_vagas' => 1]);
    ($this->schoolClass)(['max_aluno' => 10]);

    ($this->store)(($this->form)(['nm_turma' => 'Turma B']))
        ->assertStatus(422)
        ->assertExactJson(['msg' => "Não é possivel cadastrar turmas, pois ainda existem 10 vagas em aberto na turma 'Turma A' desta serie e turno. Tal limitação ocorre devido definição feita para estae scola e série."]);
});

test('exige o boletim diferenciado quando a regra da série tem regra diferenciada', function () {
    ($this->rule)($this->second, ['regra_diferenciada_id' => $this->firstRule->getKey()]);

    ($this->store)(($this->form)(['ref_cod_serie' => $this->second->getKey()]))
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'O campo Boletim diferenciado é obrigatório quando a regra de avaliação da série possui regra diferenciada definida.']);

    ($this->store)(($this->form)(['ref_cod_serie' => $this->second->getKey(), 'tipo_boletim_diferenciado' => 2]))
        ->assertOk();
});

test('não remove uma etapa que já tem notas lançadas', function () {
    $schoolClass = ($this->schoolClass)();
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 2]);
    $registration = $this->scenario->enrolled('Bia Borges', $schoolClass)->registration;

    $oneStage = ['ref_cod_modulo' => $this->stageType, 'data_inicio' => ['01/02/2030'], 'data_fim' => ['15/12/2030'], 'dias_letivos' => [200]];

    LegacyDisciplineScoreFactory::new()->create([
        'nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]),
        'componente_curricular_id' => $this->math,
        'etapa' => 2,
    ]);

    ($this->store)(($this->edit)($schoolClass, $oneStage))
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'Não foi possível remover uma das etapas pois existem notas ou faltas lançadas.']);
});

test('não remove uma etapa que já tem lançamentos no iDiário', function () {
    $schoolClass = ($this->schoolClass)();
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 2]);
    $oneStage = ['ref_cod_modulo' => $this->stageType, 'data_inicio' => ['01/02/2030'], 'data_fim' => ['15/12/2030'], 'dias_letivos' => [200]];

    // A primeira regra consulta o iDiário e passa; a segunda consulta de novo e barra.
    ($this->idiario)()->shouldReceive('getStepActivityByClassroom')->with($schoolClass->getKey(), 2030, 2)->andReturn(false, true);

    ($this->store)(($this->edit)($schoolClass, $oneStage))
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'Não foi possível remover uma das etapas pois existem notas ou faltas lançadas no diário online.']);
});

test('a primeira regra de etapas não consegue barrar pelo iDiário', function () {
    $schoolClass = ($this->schoolClass)();
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 2]);
    $oneStage = ['ref_cod_modulo' => $this->stageType, 'data_inicio' => ['01/02/2030'], 'data_fim' => ['15/12/2030'], 'dias_letivos' => [200]];

    ($this->idiario)()->shouldReceive('getStepActivityByClassroom')->andReturn(true);

    // A regra monta a exceção de validação com uma string, o que estoura
    // antes de virar resposta 422.
    ($this->store)(($this->edit)($schoolClass, $oneStage))->assertStatus(500);
});

// Os componentes da turma são apagados antes de o legado comparar o que sai,
// então nada chega a ser consultado no iDiário por aqui.
test('um erro inesperado no cadastro vira resposta 500 com a mensagem', function () {
    $this->mock(SchoolClassInepService::class)->shouldReceive('save')->andThrow(new RuntimeException('Inep fora do ar.'));

    ($this->store)(($this->form)())
        ->assertStatus(500)
        ->assertExactJson(['msg' => 'Inep fora do ar.']);
});

test('exclui a turma', function () {
    $schoolClass = ($this->schoolClass)();

    ($this->delete)(['cod_turma' => $schoolClass->getKey()])
        ->assertOk()
        ->assertExactJson(['msg' => 'Exclusão efetuada com sucesso.'])
        ->assertSessionHas('success', 'Exclusão efetuada com sucesso.');

    $schoolClass->refresh();

    expect($schoolClass->ativo)->toBe(0)
        ->and($schoolClass->visivel)->toBeFalse()
        ->and($schoolClass->data_exclusao)->not->toBeNull();
});

test('não exclui uma turma com enturmações ativas', function () {
    $schoolClass = ($this->schoolClass)();
    $this->scenario->enrolled('Bia Borges', $schoolClass);

    ($this->delete)(['cod_turma' => $schoolClass->getKey()])
        ->assertStatus(422)
        ->assertExactJson(['msg' => 'A turma possui enturmações ativas.']);

    expect($schoolClass->refresh()->ativo)->toBe(1);
});

test('um erro inesperado na exclusão vira resposta 500 com a mensagem', function () {
    ($this->delete)(['cod_turma' => 'abc'])
        ->assertStatus(500)
        ->assertJsonStructure(['msg']);
});
