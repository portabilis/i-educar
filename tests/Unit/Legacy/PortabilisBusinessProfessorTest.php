<?php

use App\Models\LegacySchoolClass;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

function vinculaProfessorNaTurma(LegacySchoolClass $schoolClass, ?int $disciplineId = null, ?int $employeeId = null, ?int $year = null): void
{
    $teacher = LegacySchoolClassTeacherFactory::new()->create([
        'instituicao_id' => test()->institution->getKey(),
        'turma_id' => $schoolClass->getKey(),
        'servidor_id' => $employeeId ?? test()->employeeId,
        'ano' => $year ?? $schoolClass->ano,
    ]);

    LegacySchoolClassTeacherDisciplineFactory::new()->create([
        'professor_turma_id' => $teacher->getKey(),
        'componente_curricular_id' => $disciplineId ?? LegacyDisciplineFactory::new()->create()->getKey(),
    ]);
}

function outraTurmaDaMesmaSerie(LegacySchoolClass $schoolClass): LegacySchoolClass
{
    return LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
    ]);
}

beforeEach(function () {
    $this->institution = LegacyInstitutionFactory::new()->current();

    DB::table('pmieducar.instituicao')
        ->where('cod_instituicao', $this->institution->getKey())
        ->update(['exigir_vinculo_turma_professor' => 1]);

    $this->schoolClass = LegacySchoolClassFactory::new()->create();
    $this->employeeId = EmployeeFactory::new()->create()->getKey();
});

test('lista somente as turmas do vinculo', function () {
    vinculaProfessorNaTurma($this->schoolClass);
    vinculaProfessorNaTurma(outraTurmaDaMesmaSerie($this->schoolClass), employeeId: EmployeeFactory::new()->create()->getKey());

    $inactive = outraTurmaDaMesmaSerie($this->schoolClass);
    vinculaProfessorNaTurma($inactive);
    $inactive->update(['ativo' => 0]);

    $hidden = outraTurmaDaMesmaSerie($this->schoolClass);
    vinculaProfessorNaTurma($hidden);
    $hidden->update(['visivel' => false]);

    $schoolClasses = Portabilis_Business_Professor::turmasAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->schoolClass->ref_ref_cod_serie,
        $this->employeeId
    );

    $this->assertSame([$this->schoolClass->getKey()], array_map('intval', array_column($schoolClasses, 'id')));
});

test('lista curso e serie das turmas do vinculo', function () {
    vinculaProfessorNaTurma($this->schoolClass);

    $courses = Portabilis_Business_Professor::cursosAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->employeeId
    );

    $grades = Portabilis_Business_Professor::seriesAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->schoolClass->ref_cod_curso,
        $this->employeeId
    );

    $this->assertSame([$this->schoolClass->ref_cod_curso], array_map('intval', array_column($courses, 'id')));
    $this->assertSame([$this->schoolClass->ref_ref_cod_serie], array_map('intval', array_column($grades, 'id')));
});

test('lista os componentes do vinculo', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    vinculaProfessorNaTurma($this->schoolClass, $discipline->getKey());

    $disciplines = Portabilis_Business_Professor::componentesCurricularesAlocado(
        $this->institution->getKey(),
        $this->schoolClass->getKey(),
        $this->schoolClass->ano,
        $this->employeeId
    );

    $this->assertSame([$discipline->getKey()], array_map('intval', array_column($disciplines, 'id')));
});

test('filtra o ano do vinculo quando informado', function () {
    vinculaProfessorNaTurma($this->schoolClass);
    vinculaProfessorNaTurma(outraTurmaDaMesmaSerie($this->schoolClass), year: $this->schoolClass->ano - 1);

    $previousGrade = LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola]);
    vinculaProfessorNaTurma(LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $previousGrade->grade_id,
        'ref_cod_curso' => $previousGrade->grade->ref_cod_curso,
        'ano' => $this->schoolClass->ano - 1,
    ]));

    $courses = Portabilis_Business_Professor::cursosAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->employeeId,
        $this->schoolClass->ano
    );

    $grades = Portabilis_Business_Professor::seriesAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $previousGrade->grade->ref_cod_curso,
        $this->employeeId,
        $this->schoolClass->ano
    );

    $this->assertNotContains($previousGrade->grade->ref_cod_curso, array_map('intval', array_column($courses, 'id')));
    $this->assertEmpty($grades);

    $args = [
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->schoolClass->ref_ref_cod_serie,
        $this->employeeId,
    ];

    $comAno = Portabilis_Business_Professor::turmasAlocado(...[...$args, $this->schoolClass->ano]);
    $semAno = Portabilis_Business_Professor::turmasAlocado(...$args);

    $this->assertSame([$this->schoolClass->getKey()], array_map('intval', array_column($comAno, 'id')));
    $this->assertCount(2, $semAno);
});

test('ano invalido devolve lista vazia', function () {
    vinculaProfessorNaTurma($this->schoolClass);

    foreach (['40000', '1.5', 'abc', '-1'] as $year) {
        $this->assertSame([], Portabilis_Business_Professor::turmasAlocado(
            $this->institution->getKey(),
            $this->schoolClass->ref_ref_cod_escola,
            $this->schoolClass->ref_ref_cod_serie,
            $this->employeeId,
            $year
        ));
    }
});

test('turma multisseriada aparece pela serie da turma serie', function () {
    $schoolGrade = LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
    ]);

    foreach ([$this->schoolClass->ref_ref_cod_serie, $schoolGrade->grade_id] as $gradeId) {
        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $this->schoolClass->ref_ref_cod_escola,
            'serie_id' => $gradeId,
            'turma_id' => $this->schoolClass->getKey(),
        ]);
    }

    vinculaProfessorNaTurma($this->schoolClass);

    $schoolClasses = Portabilis_Business_Professor::turmasAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $schoolGrade->grade_id,
        $this->employeeId
    );

    $courses = Portabilis_Business_Professor::cursosAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->employeeId
    );

    $grades = Portabilis_Business_Professor::seriesAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $schoolGrade->grade->ref_cod_curso,
        $this->employeeId
    );

    $this->assertSame([$this->schoolClass->getKey()], array_map('intval', array_column($schoolClasses, 'id')));
    $this->assertContains($schoolGrade->grade->ref_cod_curso, array_map('intval', array_column($courses, 'id')));
    $this->assertContains($schoolGrade->grade_id, array_map('intval', array_column($grades, 'id')));
});

test('sem vinculo as listas ficam vazias', function () {
    EmployeeAllocationFactory::new()->create([
        'ref_cod_servidor' => $this->employeeId,
        'ref_ref_cod_instituicao' => $this->institution->getKey(),
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'periodo' => $this->schoolClass->turma_turno_id,
    ]);

    DB::table('pmieducar.instituicao')
        ->where('cod_instituicao', $this->institution->getKey())
        ->update(['exigir_vinculo_turma_professor' => 0]);

    $this->assertNotEmpty(Portabilis_Business_Professor::turmasAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->schoolClass->ref_ref_cod_serie,
        $this->employeeId
    ));

    DB::table('pmieducar.instituicao')
        ->where('cod_instituicao', $this->institution->getKey())
        ->update(['exigir_vinculo_turma_professor' => 1]);

    $this->assertEmpty(Portabilis_Business_Professor::turmasAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->schoolClass->ref_ref_cod_serie,
        $this->employeeId
    ));

    $this->assertEmpty(Portabilis_Business_Professor::cursosAlocado(
        $this->institution->getKey(),
        $this->schoolClass->ref_ref_cod_escola,
        $this->employeeId
    ));
});

test('nome do curso e da serie so leva a descricao quando ela esta preenchida', function () {
    vinculaProfessorNaTurma($this->schoolClass);

    $courseName = DB::table('pmieducar.curso')->where('cod_curso', $this->schoolClass->ref_cod_curso)->value('nm_curso');
    $gradeName = DB::table('pmieducar.serie')->where('cod_serie', $this->schoolClass->ref_ref_cod_serie)->value('nm_serie');

    foreach ([['', ''], [null, ''], ['Anos iniciais', ' (Anos iniciais)']] as [$description, $suffix]) {
        DB::table('pmieducar.curso')->where('cod_curso', $this->schoolClass->ref_cod_curso)->update(['descricao' => $description]);
        DB::table('pmieducar.serie')->where('cod_serie', $this->schoolClass->ref_ref_cod_serie)->update(['descricao' => $description]);

        $courses = Portabilis_Business_Professor::cursosAlocado(
            $this->institution->getKey(),
            $this->schoolClass->ref_ref_cod_escola,
            $this->employeeId
        );

        $grades = Portabilis_Business_Professor::seriesAlocado(
            $this->institution->getKey(),
            $this->schoolClass->ref_ref_cod_escola,
            $this->schoolClass->ref_cod_curso,
            $this->employeeId
        );

        $this->assertSame([$courseName . $suffix], array_column($courses, 'nome'));
        $this->assertSame([$gradeName . $suffix], array_column($grades, 'nome'));
    }
});

test('sem o campo marcado o nome do curso ministrado so leva a descricao quando ela esta preenchida', function () {
    DB::table('pmieducar.instituicao')
        ->where('cod_instituicao', $this->institution->getKey())
        ->update(['exigir_vinculo_turma_professor' => 0]);

    $schoolId = $this->schoolClass->ref_ref_cod_escola;
    $courseId = $this->schoolClass->ref_cod_curso;

    if (!DB::table('pmieducar.escola_curso')->where('ref_cod_escola', $schoolId)->where('ref_cod_curso', $courseId)->exists()) {
        LegacySchoolCourseFactory::new()->create(['ref_cod_escola' => $schoolId, 'ref_cod_curso' => $courseId]);
    }

    DB::table('pmieducar.servidor_curso_ministra')->insert([
        'ref_cod_curso' => $courseId,
        'ref_ref_cod_instituicao' => $this->institution->getKey(),
        'ref_cod_servidor' => $this->employeeId,
    ]);

    $courseName = DB::table('pmieducar.curso')->where('cod_curso', $courseId)->value('nm_curso');

    foreach ([['', ''], [null, ''], ['Anos iniciais', ' (Anos iniciais)']] as [$description, $suffix]) {
        DB::table('pmieducar.curso')->where('cod_curso', $courseId)->update(['descricao' => $description]);

        $courses = Portabilis_Business_Professor::cursosAlocado($this->institution->getKey(), $schoolId, $this->employeeId);

        $this->assertSame([$courseName . $suffix], array_column($courses, 'nome'));
    }
});
