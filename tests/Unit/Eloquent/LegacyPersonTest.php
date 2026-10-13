<?php

namespace Tests\Unit\App\Models;

use App\Models\Employee;
use App\Models\LegacyDeficiency;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\EloquentTestCase;

class LegacyPersonTest extends EloquentTestCase
{
    protected $relations = [
        'phone' => LegacyPhone::class,
        'individual' => LegacyIndividual::class,
        'employee' => Employee::class,
        'childrenAsMother' => LegacyIndividual::class,
        'childrenAsFather' => LegacyIndividual::class,
        'dependentsAsResponsible' => LegacyIndividual::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyPerson::class;
    }

    public function test_attributes()
    {
        $this->assertEquals($this->model->id, $this->model->idpes);
        $this->assertEquals($this->model->name, $this->model->nome);
    }

    public function test_relationship_deficiencies(): void
    {
        LegacyIndividualFactory::new()->hasDeficiency()->create(['idpes' => $this->model]);

        $this->assertCount(1, $this->model->deficiencies);
        $this->assertInstanceOf(LegacyDeficiency::class, $this->model->deficiencies->first());
    }

    public function test_relationship_considerable_deficiencies(): void
    {
        LegacyIndividualFactory::new()->hasDeficiency(['desconsidera_regra_diferenciada' => false])->create(['idpes' => $this->model]);

        $this->assertCount(1, $this->model->considerableDeficiencies);
        $this->assertInstanceOf(LegacyDeficiency::class, $this->model->considerableDeficiencies->first());
    }

    public function test_relationships_with_children_and_dependents(): void
    {
        $childWithMother = LegacyIndividualFactory::new()->create([
            'idpes_mae' => $this->model->getKey(),
        ]);
        $childWithFather = LegacyIndividualFactory::new()->create([
            'idpes_pai' => $this->model->getKey(),
        ]);
        $dependent = LegacyIndividualFactory::new()->create([
            'idpes_responsavel' => $this->model->getKey(),
        ]);

        $this->assertTrue($this->model->childrenAsMother->contains($childWithMother));
        $this->assertTrue($this->model->childrenAsFather->contains($childWithFather));
        $this->assertTrue($this->model->dependentsAsResponsible->contains($dependent));
    }

    public function test_filter_by_each_link_type_and_multiple_links(): void
    {
        $student = LegacyIndividualFactory::new()->create()->person;
        $employee = LegacyIndividualFactory::new()->create()->person;
        $mother = LegacyIndividualFactory::new()->create()->person;
        $father = LegacyIndividualFactory::new()->create()->person;
        $responsible = LegacyIndividualFactory::new()->create()->person;
        $multiple = LegacyIndividualFactory::new()->create()->person;
        $unlinked = LegacyIndividualFactory::new()->create()->person;
        $inactiveStudent = LegacyIndividualFactory::new()->create()->person;
        $inactiveEmployee = LegacyIndividualFactory::new()->create()->person;

        LegacyStudentFactory::new()->create(['ref_idpes' => $student->getKey()]);
        LegacyStudentFactory::new()->create(['ref_idpes' => $multiple->getKey()]);
        EmployeeFactory::new()->create(['id' => $employee->getKey()]);
        EmployeeFactory::new()->create(['id' => $multiple->getKey()]);
        LegacyStudentFactory::new()->inactive()->create(['ref_idpes' => $inactiveStudent->getKey()]);
        $inactiveEmployeeRecord = EmployeeFactory::new()->create(['id' => $inactiveEmployee->getKey()]);
        $inactiveEmployeeRecord->ativo = 0;
        $inactiveEmployeeRecord->save();
        LegacyIndividualFactory::new()->create(['idpes_mae' => $mother->getKey()]);
        LegacyIndividualFactory::new()->create(['idpes_pai' => $father->getKey()]);
        LegacyIndividualFactory::new()->create(['idpes_responsavel' => $responsible->getKey()]);

        foreach ([
            'student' => [$student, $multiple],
            'employee' => [$employee, $multiple],
            'mother' => [$mother],
            'father' => [$father],
            'responsible' => [$responsible],
        ] as $linkType => $expected) {
            $ids = LegacyPerson::query()->whereKey([
                $student->getKey(), $employee->getKey(), $mother->getKey(),
                $father->getKey(), $responsible->getKey(), $multiple->getKey(),
                $unlinked->getKey(), $inactiveStudent->getKey(), $inactiveEmployee->getKey(),
            ])->filter(['link_type' => $linkType])->pluck('idpes')->all();

            $this->assertEqualsCanonicalizing(array_map(fn ($person) => $person->getKey(), $expected), $ids);
        }

        foreach ([null, '', 'all', 'todos'] as $linkType) {
            $this->assertSame(
                9,
                LegacyPerson::query()->whereKey([
                    $student->getKey(), $employee->getKey(), $mother->getKey(),
                    $father->getKey(), $responsible->getKey(), $multiple->getKey(),
                    $unlinked->getKey(), $inactiveStudent->getKey(), $inactiveEmployee->getKey(),
                ])->whereLinkType($linkType)->count()
            );
        }
    }
}
