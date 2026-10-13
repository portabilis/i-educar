<?php

use App\Models\EmployeeAllocation;
use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use App\Services\AcademicYearService;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Os casos abaixo passam por ramos internos do serviço que a cópia de ano letivo
// só alcança com dados repetidos ou inconsistentes; por isso chamam os métodos
// privados diretamente.
beforeEach(function () {
    $this->service = new AcademicYearService;
    $this->call = fn (string $method, ...$arguments) => (fn () => $this->{$method}(...$arguments))->call($this->service);
    $this->school = LegacySchoolFactory::new()->create();
});

test('não copia turma de origem que não existe mais', function () {
    $grade = LegacyGradeFactory::new()->create();

    ($this->call)('copySchoolClass', ['cod_turma' => 0, 'ref_ref_cod_escola' => $this->school->getKey(), 'nm_turma' => 'Sumida', 'ref_ref_cod_serie' => $grade->getKey()], 2029, 2030);

    expect(LegacySchoolClass::query()->where('nm_turma', 'Sumida')->exists())->toBeFalse();
});

test('não duplica alocação, vínculo de professor, componente do professor nem regra de avaliação que já existem no ano novo', function () {
    LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => 2029, 'ativo' => 1]);
    $allocation = EmployeeAllocationFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => 2029]);
    EmployeeAllocationFactory::new()->create(['ref_cod_escola' => $this->school, 'ano' => 2030, 'ref_cod_servidor' => $allocation->ref_cod_servidor, 'ref_cod_servidor_funcao' => $allocation->ref_cod_servidor_funcao]);

    // A cópia de servidores (não professores) só considera funções que não são de professor.
    DB::table('pmieducar.funcao')
        ->whereIn('cod_funcao', DB::table('pmieducar.servidor_funcao')->where('ref_cod_servidor', $allocation->ref_cod_servidor)->select('ref_cod_funcao'))
        ->update(['professor' => 0]);

    ($this->call)('copyEmployeeAllocations', $this->school->getKey(), 2030);

    expect(EmployeeAllocation::query()->where('ref_cod_escola', $this->school->getKey())->where('ano', 2030)->count())->toBe(1);

    $origin = LegacySchoolClassFactory::new()->create(['ano' => 2029]);
    $destination = LegacySchoolClassFactory::new()->create(['ano' => 2030]);
    $teacher = LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $origin, 'ano' => 2029]);
    $copy = LegacySchoolClassTeacherFactory::new()->create(['turma_id' => $destination, 'ano' => 2030, 'servidor_id' => $teacher->servidor_id]);
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacySchoolClassTeacherDisciplineFactory::new()->create(['professor_turma_id' => $teacher, 'componente_curricular_id' => $discipline]);
    LegacySchoolClassTeacherDisciplineFactory::new()->create(['professor_turma_id' => $copy, 'componente_curricular_id' => $discipline]);

    ($this->call)('copySchoolClassTeachers', $origin->getKey(), $destination->getKey(), 2029, 2030, $this->school->getKey());
    ($this->call)('copySchoolClassTeacherDisciplines', $teacher, $copy);

    expect(LegacySchoolClassTeacher::query()->where('turma_id', $destination->getKey())->count())->toBe(1)
        ->and(LegacySchoolClassTeacherDiscipline::query()->where('professor_turma_id', $copy->getKey())->count())->toBe(1);

    $grade = LegacyGradeFactory::new()->create();
    LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $this->school, 'ref_cod_serie' => $grade, 'anos_letivos' => '{2029}']);
    LegacyEvaluationRuleGradeYearFactory::new()->create(['serie_id' => $grade, 'ano_letivo' => 2029]);
    LegacyEvaluationRuleGradeYearFactory::new()->create(['serie_id' => $grade, 'ano_letivo' => 2030]);

    ($this->call)('copyEvaluationRulesForNewYear', $this->school->getKey(), 2029, 2030);

    expect(LegacyEvaluationRuleGradeYear::query()->where('serie_id', $grade->getKey())->where('ano_letivo', 2030)->count())->toBe(1);
});

test('faltas por componente na etapa removida contam como dados lançados', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ref_ref_cod_escola' => $this->school, 'ano' => 2030]);
    $absences = LegacyStudentAbsenceFactory::new()->discipline()->create(['matricula_id' => $registration]);
    LegacyDisciplineAbsenceFactory::new()->create(['falta_aluno_id' => $absences, 'etapa' => 3]);

    expect(($this->call)('hasDataInStages', [3], 2030, $this->school->getKey()))->toBeTrue();
});

test('valida os dados da etapa, reconhece anos bissextos e conta as cópias por escola', function () {
    expect(($this->call)('validateAcademicYearModuleData', []))->toBeFalse()
        ->and(($this->call)('isLeapYear', '2028-03-01'))->toBeTrue()
        ->and(($this->call)('isLeapYear', '2100-03-01'))->toBeFalse()
        ->and(($this->call)('getCopyResults', 999999, 2030, []))->toBe([]);

    ($this->call)('incrementCopyCounter', 999999, 'turmas');

    expect((fn () => $this->copyCounters[999999]['turmas'])->call($this->service))->toBe(1);
});

test('erro inesperado ao validar a escola vira erro da escola', function () {
    (fn () => $this->batchSchools = new class extends Collection
    {
        public function get($key, $default = null)
        {
            throw new RuntimeException('cadastro corrompido');
        }
    })->call($this->service);

    expect(($this->call)('validateSchoolForAction', 42, ['year' => 2030], 'finalizar'))->toBe([
        'success' => false,
        'error' => ['school_id' => 42, 'school_name' => 'Escola ID 42', 'type' => 'error', 'error' => "Escola 'Escola ID 42': cadastro corrompido"],
    ]);
});
