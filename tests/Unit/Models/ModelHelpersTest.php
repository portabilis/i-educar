<?php

use App\Models\Announcement;
use App\Models\Builders\LegacyBuilder;
use App\Models\City;
use App\Models\Country;
use App\Models\DeficiencyType;
use App\Models\District;
use App\Models\Employee;
use App\Models\EnrollmentInep;
use App\Models\Enums\DayOfWeek;
use App\Models\LegacyAbsenceDelay;
use App\Models\LegacyAccess;
use App\Models\LegacyActiveLooking;
use App\Models\LegacyAgenda;
use App\Models\LegacyAgendaCommitment;
use App\Models\LegacyAgendaResponsible;
use App\Models\LegacyBondType;
use App\Models\LegacyCalendarDay;
use App\Models\LegacyCalendarNote;
use App\Models\LegacyCalendarYear;
use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyDocument;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyGrade;
use App\Models\LegacyGradeSequence;
use App\Models\LegacyInstitution;
use App\Models\LegacyIssuingBody;
use App\Models\LegacyKnowledgeArea;
use App\Models\LegacyModel;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use App\Models\LegacySchool;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LogUnification;
use App\Models\LogUnificationOldData;
use App\Models\Phone;
use App\Models\SchoolHistoryStatus;
use App\Models\SchoolNotice;
use App\Models\SchoolSpace;
use App\Models\State;
use App\Models\UniformDistribution;
use App\Models\View\Discipline;
use App\Models\View\SchoolData;
use App\Models\View\Situation;
use Database\Factories\LegacyCalendarNoteFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyPersonFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('as relações dos modelos apontam para os modelos certos', function (string $model, string $relation) {
    expect((new $model)->$relation())->toBeInstanceOf(Relation::class);
})->with([
    [LegacyEvaluationRule::class, 'deficiencyEvaluationRule'],
    [LegacyEvaluationRule::class, 'gradeYears'],
    [LegacyPerson::class, 'legacyEmployee'],
    [LegacyPerson::class, 'student'],
    [LegacySchool::class, 'director'],
    [LegacySchool::class, 'secretary'],
    [LegacySchool::class, 'registrations'],
    [LegacySchool::class, 'activeSearches'],
    [LegacyAccess::class, 'person'],
    [LegacyAccess::class, 'user'],
    [LegacyAccess::class, 'employee'],
    [LegacyCalendarDay::class, 'calendarDayReason'],
    [LegacyCalendarDay::class, 'calendarYear'],
    [Announcement::class, 'createdByUser'],
    [EnrollmentInep::class, 'enrollment'],
    [LegacyAgenda::class, 'commitments'],
    [LegacyAgendaCommitment::class, 'agenda'],
    [LegacyAgendaResponsible::class, 'agenda'],
    [LegacyCalendarYear::class, 'school'],
    [LegacyEnrollment::class, 'registrationScore'],
    [LegacyKnowledgeArea::class, 'disciplines'],
    [LegacyGrade::class, 'evaluationRule'],
    [LegacyGrade::class, 'evaluationRuleDifferentiated'],
    [LegacyGrade::class, 'disciplines'],
    [LegacyGrade::class, 'schoolGradeDisciplines'],
    [LegacyGrade::class, 'allDisciplines'],
    [LegacySchoolHistory::class, 'schoolHistoryGradeCourse'],
    [Employee::class, 'employeeAllocation'],
    [Employee::class, 'posGraduations'],
    [Employee::class, 'place'],
    [Employee::class, 'withdrawals'],
    [Employee::class, 'schoolClassTeachers'],
    [LegacyDisciplineScore::class, 'discipline'],
    [LegacyDocument::class, 'issuingBody'],
    [LogUnificationOldData::class, 'unification'],
    [SchoolNotice::class, 'employee'],
    [SchoolSpace::class, 'school'],
]);

test('a relação de um para um monta a tabela de junção e instancia o modelo relacionado', function () {
    $grade = new LegacyGrade;

    expect($grade->belongsToOne(LegacyEvaluationRule::class))->toBeInstanceOf(Relation::class)
        ->and($grade->evaluationRule()->newRelatedInstanceFor($grade))->toBeInstanceOf(LegacyEvaluationRule::class);
});

test('os filtros dos builders montam consultas válidas', function (Closure $query) {
    expect($query()->get())->toBeIterable();
})->with([
    'curso por nome, ano, modalidade, matrículas e ativo' => [fn () => LegacyCourse::query()->whereName('Ensino')->whereYearEq(2030)->hasModality()->whereActive(1)],
    'cursos com matrículas no ano' => [fn () => LegacyCourse::query()->registrationsActiveCurrentYear()],
    'cursos com matrículas no ano anterior' => [fn () => LegacyCourse::query()->registrationsActiveLastYear()],
    'série por nome, ano letivo e ativa' => [fn () => LegacyGrade::query()->whereName('Ano')->whereAcademicYear(2030)->whereActive(1)],
    'faltas e atrasos' => [fn () => LegacyAbsenceDelay::query()->whereInstitution(1)->whereSchool(1)->whereJustified(1)->whereDateBetween('2030-01-01', '2030-12-31')],
    'acessos' => [fn () => LegacyAccess::query()->whereInstitution(1)->whereSchool(1)->whereActive(true)->whereStartAfter('2030-01-01')->whereEndBefore('2030-12-31')->wherePerson(1)],
    'países' => [fn () => Country::query()->whereName('Brasil')],
    'servidores' => [fn () => Employee::query()->whereWorkload(40)->whereSchool(1)],
    'distritos' => [fn () => District::query()->whereName('Centro')->whereCountryId(1)],
    'dispensas por curso' => [fn () => LegacyDisciplineExemption::query()->whereCourse(1)],
    'sequências de série' => [fn () => LegacyGradeSequence::query()->whereInstitution(1)->whereCourseOrigin(1)->whereCourseDestiny(1)],
    'instituições' => [fn () => LegacyInstitution::query()->whereInstitution(1)->whereName('Prefeitura')->orderByName()],
    'escolas' => [fn () => LegacySchool::query()->whereName('Aurora')->whereSchool(1)->whereActive(1)],
    'cidades' => [fn () => City::query()->whereName('Florianópolis')],
    'componentes' => [fn () => Discipline::query()->whereName('Matemática')],
    'busca ativa' => [fn () => LegacyActiveLooking::query()->whereSituation(1)],
    'funções do servidor' => [fn () => LegacyEmployeeRole::query()->whereRole(1)],
    'unificações de alunos' => [fn () => LogUnification::query()->student()],
    'estados' => [fn () => State::query()->whereName('Santa Catarina')],
    'histórico escolar' => [fn () => LegacySchoolHistory::query()->whereDependency(true)->whereNotExtraCurricular(false)->onlyGradeNumeric()->whereCourseEja()->whereStartAfter(2020)->whereEndBefore(2030)->whereCourses("'Ensino Fundamental'")->validStatusEja()->validStatus()],
    'histórico escolar sem dependência' => [fn () => LegacySchoolHistory::query()->whereDependency(false)],
    'base: busca por código, filtros e colunas legadas' => [fn () => LegacySchool::query()->whereSearch('1,2')->whereFilter('active,1|ativo,1|organization.fantasia,Aurora')->orderBy('id')->whereNot('id', 0)->whereNotIn('id', [0])],
    'base: busca por nome' => [fn () => LegacyInstitution::query()->whereSearch('Prefeitura')],
]);

// Modelo de escola com o builder legado, sem o mapa de colunas ou com ele vazio.
function modeloComBuilderLegado(?array $legacy = null): Model
{
    $model = is_null($legacy)
        ? new class extends Model
        {
            protected $table = 'pmieducar.escola';

            protected $primaryKey = 'cod_escola';

            public function newEloquentBuilder($query)
            {
                return new LegacyBuilder($query);
            }
        }
    : new class extends Model
    {
        public array $legacy = [];

        protected $table = 'pmieducar.escola';

        protected $primaryKey = 'cod_escola';

        public function newEloquentBuilder($query)
        {
            return new LegacyBuilder($query);
        }
    };

    return $model;
}

test('o recurso de países usa os ativos ordenados pelo nome', function () {
    expect(Country::query()->getResource())->toBeIterable();
});

test('o builder base informa o filtro guardado e mantém colunas quando o modelo não tem mapa legado', function () {
    $builder = LegacySchool::query();
    $builder->filter(['name' => 'Aurora']);

    expect($builder->getFilter('name'))->toBe('Aurora')
        ->and($builder->getFilter('inexistente', 'padrão'))->toBe('padrão')
        ->and(modeloComBuilderLegado()->newQuery()->whereKey(0)->resource(['cod_escola as value']))->toBeEmpty()
        ->and(modeloComBuilderLegado([])->newQuery()->whereKey(0)->resource(['cod_escola']))->toBeEmpty();
});

test('acessores de notas, médias, faltas, telefones, documentos, dias da semana, vínculos e distribuição de uniformes', function () {
    $score = (new LegacyDisciplineScore)->forceFill(['nota' => 7.25, 'nota_arredondada' => 7.5, 'nota_original' => 6, 'nota_recuperacao' => 8]);
    $empty = (new LegacyDisciplineScore)->forceFill(['nota_arredondada' => '']);
    $average = (new LegacyDisciplineScoreAverage)->forceFill(['media_arredondada' => 8.25]);

    expect($score->score())->toBe('7,5')
        ->and($score->score(2, false, false))->toBe('7,25')
        ->and($score->score(1, true))->toEqual(0)
        ->and($empty->score())->toBe('')
        ->and($score->originalScore())->toBe('6,0')
        ->and($empty->originalScore())->toBeNull()
        ->and($score->recoveryScore())->toBe('8,0')
        ->and($empty->recoveryScore())->toBeNull()
        ->and($average->average(1))->toBe('8,2')
        ->and((new LegacyDisciplineScoreAverage)->average(1))->toBeNull()
        ->and((new LegacyDisciplineAbsence)->forceFill(['quantidade' => 3])->absence(true))->toBe(0)
        ->and((new LegacyGeneralAbsence)->forceFill(['quantidade' => 4])->absence())->toBe(4)
        ->and((new LegacyPhone)->forceFill(['fone' => '33334444', 'ddd' => 48])->number)->toBe('(48)&nbsp;33334444')
        ->and((new LegacyPhone)->forceFill(['fone' => '33334444'])->number)->toBe('33334444')
        ->and((new LegacyPhone)->number)->toBeNull()
        ->and((new Phone)->formatted_number)->toBeNull()
        ->and((new Phone)->forceFill(['number' => '33334444'])->formatted_number)->toBe('3333-4444')
        ->and((new LegacyInstitution)->forceFill(['telefone' => '33334444', 'ddd_telefone' => 48])->phone)->toBe('(48) 33334444')
        ->and((new LegacyInstitution)->cellphone)->toBe('(##) #####-####')
        ->and((new LegacyDocument)->forceFill(['tipo_cert_civil' => 91, 'num_termo' => '1', 'num_livro' => '2', 'num_folha' => '3'])->birth_registration)->toBe('Termo: 1 Livro: 2 Folha: 3')
        ->and((new LegacyDocument)->forceFill(['tipo_cert_civil' => 92, 'certidao_nascimento' => '123'])->birth_registration)->toBe('123')
        ->and(collect(DayOfWeek::cases())->map->name()->all())->toBe(['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'])
        ->and(DayOfWeek::SATURDAY->shortName())->toBe('Sáb')
        ->and((new LegacyBondType)->forceFill(['nm_vinculo' => 'Efetivo', 'abreviatura' => 'EF'])->name)->toBe('Efetivo')
        ->and((new LegacyBondType)->forceFill(['abreviatura' => 'EF'])->abbreviation)->toBe('EF')
        ->and((new UniformDistribution)->labels())->toHaveKey('sneakers', 'Tênis')
        ->and(DeficiencyType::getValueDescription(DeficiencyType::DISORDER))->toBe('Transtorno')
        ->and(State::getNameByAbbreviation(null))->toBe('')
        ->and((new LegacyActiveLooking)->getEndDate())->toBeNull()
        ->and((new SchoolData)->forceFill(['cep' => '88000000'])->postpone)->toBe('88000-000');
});

test('o histórico escolar interpreta frequência, série, curso e situação', function () {
    $history = new LegacySchoolHistory;
    $history->frequencia = '1.234,5';
    $history->nm_serie = '5º ano';
    $history->historico_grade_curso_id = LegacySchoolHistory::GRADE_EJA;
    $history->aprovado = SchoolHistoryStatus::REPROVED;

    expect($history->getAttributes()['frequencia'])->toBe('1234.5')
        ->and($history->grade)->toBe(5)
        ->and((new LegacySchoolHistory)->forceFill(['nm_serie' => 'Pré'])->grade)->toBeNull()
        ->and($history->courseIsEja())->toBeTrue()
        ->and($history->courseIsSerie())->toBeFalse()
        ->and($history->courseIsAno())->toBeFalse()
        ->and($history->isOnGoing())->toBeFalse()
        ->and($history->isTransferred())->toBeFalse()
        ->and($history->isReproved())->toBeTrue()
        ->and((new LegacySchoolHistoryDiscipline)->forceFill(['tipo_base' => ComponenteCurricular_Model_TipoBase::DIVERSIFICADA])->isDiversified())->toBeTrue();
});

test('a regra de avaliação informa recuperação, presença, parecer e nota geral', function () {
    $rule = new LegacyEvaluationRule;
    $rule->tipo_recuperacao_paralela = LegacyEvaluationRule::PARALLEL_REMEDIAL_NONE;
    $rule->tipo_presenca = 1;
    $rule->parecer_descritivo = 0;
    $rule->nota_geral_por_etapa = 1;

    expect($rule->isNone())->toBeTrue()
        ->and($rule->isByStage())->toBeFalse()
        ->and($rule->isGeneralAbsence())->toBeTrue()
        ->and($rule->hasOpinion())->toBeFalse()
        ->and($rule->isGeneralScore())->toBeTrue();
});

test('nomes da pessoa e da organização em maiúsculas, nome social e unificação de tipo inválido', function () {
    config(['legacy.app.uppercase_names' => true]);

    $person = LegacyPersonFactory::new()->create(['nome' => 'ana  alves']);
    $organization = LegacyOrganizationFactory::new()->create(['fantasia' => 'escola aurora']);
    $individual = LegacyIndividualFactory::new()->create(['nome_social' => 'Ana Social']);

    expect($person->fresh()->nome)->toBe('ANA ALVES')
        ->and($organization->fresh()->fantasia)->toBe('ESCOLA AURORA')
        ->and($individual->person->fresh()->real_name)->toBe('Ana Social')
        ->and(fn () => (new LogUnification)->forceFill(['type' => 'outro'])->getAdapter())->toThrow(Exception::class, 'Tipo de unificação inválido');
});

test('o soft delete legado apaga de vez, restaura, avisa os eventos e filtra pelo campo ativo', function () {
    $eventos = [];
    foreach (['softDeleted', 'restoring', 'restored', 'forceDeleting', 'forceDeleted'] as $evento) {
        LegacyCalendarNote::$evento(function () use (&$eventos, $evento) {
            $eventos[] = $evento;
        });
    }

    $notes = LegacyCalendarNoteFactory::new()->count(4)->create();
    [$a, $b, $c, $d] = $notes->all();

    $a->delete();

    expect($a->trashed())->toBeTrue()
        ->and(LegacyCalendarNote::query()->whereKey($a->getKey())->exists())->toBeFalse()
        ->and(LegacyCalendarNote::query()->withTrashed(false)->whereKey($b->getKey())->exists())->toBeTrue()
        ->and(LegacyCalendarNote::query()->onlyTrashed()->whereKey($a->getKey())->exists())->toBeTrue()
        ->and(LegacyCalendarNote::query()->withoutTrashed()->whereKey($b->getKey())->exists())->toBeTrue();

    $a->restore();
    $a->restoreQuietly();
    LegacyCalendarNote::query()->whereKey($b->getKey())->delete();
    LegacyCalendarNote::query()->whereKey($b->getKey())->restore();
    LegacyCalendarNote::query()->join('pmieducar.calendario_ano_letivo', 'calendario_ano_letivo.cod_calendario_ano_letivo', '=', 'calendario_anotacao.cod_calendario_anotacao')->whereKey(0)->delete();

    expect($a->fresh()->trashed())->toBeFalse()
        ->and($b->fresh()->trashed())->toBeFalse()
        ->and($a->isForceDeleting())->toBeFalse()
        ->and($a->forceDelete())->toBeTrue()
        ->and($b->forceDeleteQuietly())->toBeTrue()
        ->and(LegacyCalendarNote::forceDestroy([]))->toBe(0)
        ->and(LegacyCalendarNote::forceDestroy(collect([$c->getKey()])))->toBe(1)
        ->and(LegacyCalendarNote::forceDestroy(LegacyCalendarNote::query()->whereKey($d->getKey())->get()))->toBe(1)
        ->and($eventos)->toContain('softDeleted', 'restoring', 'restored', 'forceDeleting', 'forceDeleted');

    LegacyCalendarNote::forceDeleting(fn () => false);
    LegacyCalendarNote::restoring(fn () => false);
    $e = LegacyCalendarNoteFactory::new()->create();

    expect($e->forceDelete())->toBeFalse()
        ->and($e->restore())->toBeFalse();
});

test('situação da matrícula, colunas que não são texto no builder base e modelo legado sem mapa', function () {
    $legacyModel = new class extends LegacyModel
    {
        protected $table = 'pmieducar.escola';

        protected $guarded = [];
    };
    $legacyModel->fill(['sigla' => 'EA']);

    expect(Situation::query()->situation(16)->get())->toBeIterable()
        ->and(Situation::query()->situation(1)->get())->toBeIterable()
        ->and(LegacySchool::query()->orderBy(DB::raw('cod_escola'))->whereNot(fn ($q) => $q->where('cod_escola', 0))->whereNotIn(DB::raw('cod_escola'), [0])->limit(1)->get())->toBeIterable()
        ->and($legacyModel->toArray())->toBe(['sigla' => 'EA'])
        ->and(DeficiencyType::getValueDescription(DeficiencyType::DEFICIENCY))->toBe('Deficiência')
        ->and((new LegacyDocument)->forceFill(['sigla_uf_exp_rg' => 'SC'])->setRelation('issuingBody', (new LegacyIssuingBody)->forceFill(['sigla' => 'SSP']))->issuing_body_with_uf)->toBe('SSP/SC');
});
