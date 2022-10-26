<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyUserSchool;
use App\Models\SchoolInep;
use App\Models\SchoolManager;
use Tests\EloquentTestCase;

class LegacySchoolTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'institution' => LegacyInstitution::class,
        'academicYears' => [LegacySchoolAcademicYear::class],
        'person' => LegacyPerson::class,
        'organization' => LegacyOrganization::class,
        'schoolUsers' => [LegacyUserSchool::class],
        'inep' => SchoolInep::class,
        'schoolManagers' =>[SchoolManager::class]
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchool::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'cod_escola',
            'name' => 'fantasia'
        ];
    }

    public function testId(): void
    {
        $this->assertEquals($this->model->cod_escola, $this->model->id);
    }

    public function testName(): void
    {
        $this->assertEquals($this->model->organization->fantasia, $this->model->name);
    }
}
