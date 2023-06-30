<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\Individual;
use App\Models\LegacySchool;
use App\Models\ManagerAccessCriteria;
use App\Models\ManagerLinkType;
use App\Models\ManagerRole;
use App\Models\SchoolManager;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\EloquentTestCase;

class SchoolManagerTest extends EloquentTestCase
{
    protected $relations = [
        'individual' => Individual::class,
        'school' => LegacySchool::class,
        'employee' => Employee::class,
        'role' => ManagerRole::class,
        'accessCriteria' => ManagerAccessCriteria::class,
        'linkType' => ManagerLinkType::class,
    ];

    protected function getEloquentModelName(): string
    {
        return SchoolManager::class;
    }

    public function testIsChief(): void
    {
        $this->assertEquals($this->model->chief, $this->model->isChief());
    }

    public function testScopeOfSchool(): void
    {
        $school = LegacySchoolFactory::new()->create();
        SchoolManagerFactory::new()->create(['school_id' => $school]);
        SchoolManagerFactory::new()->create();

        $modelSearch = $this->instanceNewEloquentModel()->ofSchool($school->id)->get();
        $this->assertCount(1, $modelSearch);
        $this->assertInstanceOf(SchoolManager::class, $modelSearch->first());
    }
}
