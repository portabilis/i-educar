<?php

use App\Models\FileRelation;
use App\Models\LegacyBenefit;
use App\Models\LegacyDeficiency;
use App\Models\LegacyDocument;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentHistoricalHeightWeight;
use App\Models\LegacyStudentMedicalRecord;
use App\Models\RegistrationStatus;
use App\Models\StudentInep;
use Database\Factories\FileFactory;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyDocumentFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolHistoryDisciplineFactory;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Support\Facades\DB;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudent::class, relations: [
    'individual' => LegacyIndividual::class,
    'person' => LegacyPerson::class,
    'registrations' => LegacyRegistration::class,
    'inep' => StudentInep::class,
    'benefits' => LegacyBenefit::class,
    'deficiencies' => LegacyDeficiency::class,
    'medicalRecord' => LegacyStudentMedicalRecord::class,
    'historicalHeightWeight' => LegacyStudentHistoricalHeightWeight::class,
    'schoolHistories' => LegacySchoolHistory::class,
    'lastRegistration' => LegacyRegistration::class,
]));

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->tipo_responsavel, $this->model->guardianType);
    $this->assertEquals($this->model->inep ? $this->model->inep->number : null, $this->model->inepNumber);
    $this->assertEquals($this->model->aluno_estado_id, $this->model->stateRegistrationId);
});

test('get guardian name', function () {
    $individual = LegacyIndividualFactory::new()->father()->mother()->guardian()->create();
    $model = LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);

    $join = $model->individual->mother->name . ', ' . $model->individual->father->name;
    $expected = match ($model->guardianType) {
        'm' => $model->individual->mother->name,
        'p' => $model->individual->father->name,
        'r' => $model->individual->responsible->name,
        'a' => strlen($join) < 3 ? null : $join,
        default => null
    };
    $this->assertEquals($expected, $model->getGuardianName());
});

test('get guardian cpf', function () {
    $join = ($this->model->individual->mother->individual->cpf ?? 'não informado') . ', ' . ($this->individual->model->father->individual->cpf ?? 'não informado');
    $expected = match ($this->model->guardianType) {
        'm' => $this->individual->mother->individual->cpf ?? 'não informado',
        'p' => $this->individual->father->individual->cpf ?? 'não informado',
        'r' => $this->individual->responsible->individual->cpf ?? 'não informado',
        'a' => strlen($join) < 3 ? null : $join,
        default => null
    };
    $this->assertEquals($expected, $this->model->getGuardianCpf());
});

test('scope active', function () {
    LegacyStudentFactory::new()->create(['ativo' => 0]);
    $found = $this->instanceNewEloquentModel()->active()->get();
    $this->assertCount(1, $found);
});

test('scope male', function () {
    LegacyIndividual::query()->update([
        'sexo' => null,
    ]);

    $individual = LegacyIndividualFactory::new()->create(['sexo' => 'M']);
    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);
    $found = $this->instanceNewEloquentModel()->male()->get();
    $this->assertCount(1, $found);
});

test('scope female', function () {
    LegacyIndividual::query()->update([
        'sexo' => null,
    ]);

    $individual = LegacyIndividualFactory::new()->create(['sexo' => 'F']);
    $student2 = LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual,
    ]);
    $found = $this->instanceNewEloquentModel()->female()->whereIn('cod_aluno', [
        $student2->cod_aluno,
        $this->model->cod_aluno,
    ])->get();
    $this->assertCount(1, $found);
});

test('document and single deficiency', function () {
    $student = LegacyStudentFactory::new()->create();
    LegacyDocumentFactory::new()->create(['idpes' => $student->ref_idpes]);
    $deficiency = LegacyDeficiencyFactory::new()->create();
    $student->deficiencies()->attach($deficiency);

    $this->assertInstanceOf(LegacyDocument::class, $student->document);
    $this->assertSame($student->ref_idpes, $student->document->idpes);
    $this->assertInstanceOf(LegacyDeficiency::class, $student->deficiency);
    $this->assertSame($deficiency->getKey(), $student->deficiency->getKey());
});

test('school history disciplines', function () {
    $student = LegacyStudentFactory::new()->create();
    $history = LegacySchoolHistoryFactory::new()->create(['ref_cod_aluno' => $student]);
    LegacySchoolHistoryDisciplineFactory::new()->create([
        'historico_escolar_id' => $history->id,
        'ref_ref_cod_aluno' => $student->getKey(),
        'ref_sequencial' => $history->sequencial,
    ]);

    $this->assertCount(1, $student->schoolHistoryDisciplines);
    $this->assertInstanceOf(LegacySchoolHistoryDiscipline::class, $student->schoolHistoryDisciplines->first());
});

test('last registration is the newest active one', function () {
    $student = LegacyStudentFactory::new()->create();
    LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ano' => 2030, 'ativo' => 1]);
    $newest = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ano' => 2031, 'ativo' => 1]);
    LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $student, 'ano' => 2032, 'ativo' => 0]);

    $this->assertSame($newest->getKey(), $student->lastRegistration->getKey());
});

test('registration transfer is the current transfer of a student without a newer registration', function () {
    $student = LegacyStudentFactory::new()->create();

    $this->assertNull($student->registration_transfer);

    $transfer = LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $student,
        'ativo' => 1,
        'aprovado' => RegistrationStatus::TRANSFERRED,
    ]);
    LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $transfer, 'ativo' => 1]);

    $this->assertSame($transfer->getKey(), $student->fresh()->registration_transfer->getKey());
});

test('has report', function () {
    $student = LegacyStudentFactory::new()->create();

    $this->assertFalse($student->hasReport());

    $student->files()->attach(FileFactory::new()->create(), ['type' => FileRelation::TYPE_MEDICAL_REPORT]);

    $this->assertTrue($student->hasReport());

    $student->load('medicalReports');

    $this->assertTrue($student->hasReport());
});

test('guardions are the parents actually registered', function () {
    $this->assertTrue(LegacyStudentFactory::new()->create()->getGuardions()->isEmpty());

    $mother = LegacyIndividualFactory::new()->create();
    DB::table('cadastro.pessoa')->where('idpes', $mother->getKey())->update(['nome' => 'NÃO REGISTRADO']);
    $individual = LegacyIndividualFactory::new()->father()->create(['idpes_mae' => $mother]);
    $student = LegacyStudentFactory::new()->create(['ref_idpes' => $individual]);

    $guardions = $student->getGuardions();

    $this->assertCount(1, $guardions);
    $this->assertSame($individual->father->name, $guardions->first()->name);
});

test('guardian name and cpf of the responsible', function () {
    $guardian = LegacyIndividualFactory::new()->create(['cpf' => 12345678909]);
    $individual = LegacyIndividualFactory::new()->create(['idpes_responsavel' => $guardian]);
    $student = LegacyStudentFactory::new()->create(['ref_idpes' => $individual, 'tipo_responsavel' => 'r']);

    $this->assertSame($guardian->person->name, $student->getGuardianName());
    $this->assertSame('123.456.789-09', $student->getGuardianCpf());
});

test('filters by the registrations', function () {
    $ana = LegacyStudentFactory::new()->create();
    $bia = LegacyStudentFactory::new()->create();
    $old = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $ana, 'ano' => 2030, 'ultima_matricula' => 0]);
    $current = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $ana, 'ano' => 2031, 'ultima_matricula' => 1]);
    LegacyRegistrationFactory::new()->create([
        'ref_cod_aluno' => $bia,
        'ano' => 2031,
        'ref_ref_cod_escola' => $old->ref_ref_cod_escola,
        'ultima_matricula' => 1,
    ]);

    $ids = fn ($query) => $query->whereIn('cod_aluno', [$ana->getKey(), $bia->getKey()])->orderBy('cod_aluno')->pluck('cod_aluno')->all();

    $this->assertSame([$ana->getKey()], $ids(LegacyStudent::query()->whereRegistrationYear(2030)));
    $this->assertSame([$ana->getKey(), $bia->getKey()], $ids(LegacyStudent::query()->whereRegistrationYear(2031)));
    $this->assertSame([$ana->getKey(), $bia->getKey()], $ids(LegacyStudent::query()->whereSchool($old->ref_ref_cod_escola)));
    $this->assertSame([$bia->getKey()], $ids(LegacyStudent::query()->whereLastRegistrationSchool($old->ref_ref_cod_escola)));
    $this->assertSame([$ana->getKey()], $ids(LegacyStudent::query()->whereCourse($current->ref_cod_curso)));
    $this->assertSame([$ana->getKey()], $ids(LegacyStudent::query()->whereGrade($current->ref_ref_cod_serie)));
});

test('filters by name report and activity', function () {
    $named = fn (string $name) => LegacyStudentFactory::new()->create([
        'ref_idpes' => LegacyIndividualFactory::new()->create([
            'idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'F']),
        ]),
    ]);
    $ana = $named('Ana Alves');
    $bia = $named('Bia Borges');
    $ana->files()->attach(FileFactory::new()->create(), ['type' => FileRelation::TYPE_MEDICAL_REPORT]);

    $ids = fn ($query) => $query->whereIn('cod_aluno', [$ana->getKey(), $bia->getKey()])->pluck('cod_aluno')->all();

    $this->assertSame([$ana->getKey()], $ids(LegacyStudent::query()->whereStudentNameLike('na al')));
    $this->assertSame([$ana->getKey()], $ids(LegacyStudent::query()->whereWithReport()));
    $this->assertSame([$bia->getKey()], $ids(LegacyStudent::query()->whereWithoutReport()));

    $bia->update(['ativo' => 0]);

    $this->assertSame([$ana->getKey()], $ids(LegacyStudent::query()->whereActive()));
});
