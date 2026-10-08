<?php

namespace Tests\Unit\Legacy;

use App\Models\LegacyInstitution;
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
use Portabilis_Business_Professor;
use Tests\TestCase;

class PortabilisBusinessProfessorTest extends TestCase
{
    use DatabaseTransactions;

    private LegacyInstitution $institution;

    private LegacySchoolClass $schoolClass;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitutionFactory::new()->current();

        DB::table('pmieducar.instituicao')
            ->where('cod_instituicao', $this->institution->getKey())
            ->update(['exigir_vinculo_turma_professor' => 1]);

        $this->schoolClass = LegacySchoolClassFactory::new()->create();
        $this->employeeId = EmployeeFactory::new()->create()->getKey();
    }

    private function link(LegacySchoolClass $schoolClass, ?int $disciplineId = null, ?int $employeeId = null, ?int $year = null): void
    {
        $teacher = LegacySchoolClassTeacherFactory::new()->create([
            'instituicao_id' => $this->institution->getKey(),
            'turma_id' => $schoolClass->getKey(),
            'servidor_id' => $employeeId ?? $this->employeeId,
            'ano' => $year ?? $schoolClass->ano,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $teacher->getKey(),
            'componente_curricular_id' => $disciplineId ?? LegacyDisciplineFactory::new()->create()->getKey(),
        ]);
    }

    private function otherSchoolClass(): LegacySchoolClass
    {
        return LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
            'ref_ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
            'ref_cod_curso' => $this->schoolClass->ref_cod_curso,
        ]);
    }

    public function test_lista_somente_as_turmas_do_vinculo()
    {
        $this->link($this->schoolClass);
        $this->link($this->otherSchoolClass(), employeeId: EmployeeFactory::new()->create()->getKey());

        $inactive = $this->otherSchoolClass();
        $this->link($inactive);
        $inactive->update(['ativo' => 0]);

        $hidden = $this->otherSchoolClass();
        $this->link($hidden);
        $hidden->update(['visivel' => false]);

        $schoolClasses = Portabilis_Business_Professor::turmasAlocado(
            $this->institution->getKey(),
            $this->schoolClass->ref_ref_cod_escola,
            $this->schoolClass->ref_ref_cod_serie,
            $this->employeeId
        );

        $this->assertSame([$this->schoolClass->getKey()], array_map('intval', array_column($schoolClasses, 'id')));
    }

    public function test_lista_curso_e_serie_das_turmas_do_vinculo()
    {
        $this->link($this->schoolClass);

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
    }

    public function test_lista_os_componentes_do_vinculo()
    {
        $discipline = LegacyDisciplineFactory::new()->create();
        $this->link($this->schoolClass, $discipline->getKey());

        $disciplines = Portabilis_Business_Professor::componentesCurricularesAlocado(
            $this->institution->getKey(),
            $this->schoolClass->getKey(),
            $this->schoolClass->ano,
            $this->employeeId
        );

        $this->assertSame([$discipline->getKey()], array_map('intval', array_column($disciplines, 'id')));
    }

    public function test_filtra_o_ano_do_vinculo_quando_informado()
    {
        $this->link($this->schoolClass);
        $this->link($this->otherSchoolClass(), year: $this->schoolClass->ano - 1);

        $previousGrade = LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola]);
        $this->link(LegacySchoolClassFactory::new()->create([
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
    }

    public function test_ano_invalido_devolve_lista_vazia()
    {
        $this->link($this->schoolClass);

        foreach (['40000', '1.5', 'abc', '-1'] as $year) {
            $this->assertSame([], Portabilis_Business_Professor::turmasAlocado(
                $this->institution->getKey(),
                $this->schoolClass->ref_ref_cod_escola,
                $this->schoolClass->ref_ref_cod_serie,
                $this->employeeId,
                $year
            ));
        }
    }

    public function test_turma_multisseriada_aparece_pela_serie_da_turma_serie()
    {
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

        $this->link($this->schoolClass);

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
    }

    public function test_sem_vinculo_as_listas_ficam_vazias()
    {
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
    }

    public function test_nome_do_curso_e_da_serie_so_leva_a_descricao_quando_ela_esta_preenchida()
    {
        $this->link($this->schoolClass);

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
    }

    public function test_sem_o_campo_marcado_o_nome_do_curso_ministrado_so_leva_a_descricao_quando_ela_esta_preenchida()
    {
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
    }
}
