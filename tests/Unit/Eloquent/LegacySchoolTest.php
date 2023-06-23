<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUserSchool;
use App\Models\SchoolInep;
use App\Models\SchoolManager;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Tests\EloquentTestCase;

class LegacySchoolTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'institution' => LegacyInstitution::class,
        'academicYears' => LegacySchoolAcademicYear::class,
        'person' => LegacyPerson::class,
        'organization' => LegacyOrganization::class,
        'inep' => SchoolInep::class,
        'schoolUsers' => LegacyUserSchool::class,
        'schoolManagers' => SchoolManager::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchool::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'cod_escola',
            'person_id' => 'ref_idpes',
            'name' => 'fantasia',
        ];
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->cod_escola, $this->model->id);
        $this->assertEquals($this->model->organization->fantasia, $this->model->name);
    }

    public function testRelationshipCourses(): void
    {
        $school = LegacySchoolFactory::new()->hasAttached(LegacyCourseFactory::new(), ['ref_usuario_cad' => 1, 'data_cadastro' => now()], 'courses')->create();
        $this->assertCount(1, $school->courses);
        $this->assertInstanceOf(LegacyCourse::class, $school->courses->first());
    }

    public function testRelationshipGrades(): void
    {
        $school = LegacySchoolFactory::new()->hasAttached(LegacyGradeFactory::new(), ['ref_usuario_cad' => 1, 'data_cadastro' => now()], 'grades')->create();
        $this->assertCount(1, $school->grades);
        $this->assertInstanceOf(LegacyGrade::class, $school->grades->first());
    }

    public function testRelationshipSchoolClasses(): void
    {
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ref_cod_serie' => $grade,
        ]);
        $this->assertCount(1, $school->schoolClasses);
        $this->assertInstanceOf(LegacySchoolClass::class, $school->schoolClasses->first());
    }

    public function testRelationshipStages(): void
    {
        $school = LegacySchoolFactory::new()->create();
        $schoolAcademicYear = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);
        $academicYearStage = LegacyAcademicYearStageFactory::new()->create([
            'ref_ref_cod_escola' => $school,
            'ref_ano' => $schoolAcademicYear->year,
        ]);
        $this->assertCount(1, $school->stages);
        $this->assertInstanceOf(LegacyAcademicYearStage::class, $school->stages->first());
    }
}
