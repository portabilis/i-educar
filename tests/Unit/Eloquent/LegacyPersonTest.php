<?php

use App\Models\Employee;
use App\Models\LegacyDeficiency;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->model = LegacyPersonFactory::new()->create();
});

test('create using eloquent', function () {
    $this->assertDatabaseHas($this->model->getTable(), $this->model->getAttributes());
});

test('update using eloquent', function () {
    $oldAttributes = $this->model->getAttributes();
    $this->model->fill(LegacyPersonFactory::new()->make()->toArray())->save();

    $this->assertDatabaseMissing($this->model->getTable(), $oldAttributes);
    $this->assertDatabaseHas($this->model->getTable(), $this->model->getAttributes());
});

test('delete using eloquent', function () {
    $attributes = $this->model->getAttributes();
    $this->model->delete();

    $this->assertDatabaseMissing($this->model->getTable(), $attributes);
});

test('find using eloquent', function () {
    $found = LegacyPerson::query()->find($this->model->getKey())->getAttributes();
    $created = $this->model->getAttributes();

    expect(array_intersect_key($created, $found))->toEqual($created);
});

test('relationships', function () {
    foreach ([
        'phone' => LegacyPhone::class,
        'individual' => LegacyIndividual::class,
        'employee' => Employee::class,
        'childrenAsMother' => LegacyIndividual::class,
        'childrenAsFather' => LegacyIndividual::class,
        'dependentsAsResponsible' => LegacyIndividual::class,
    ] as $relation => $relatedClass) {
        $method = 'has'.ucfirst($relation);
        $person = LegacyPersonFactory::new()->{$method}()->create();
        $related = $person->{$relation};

        if ($person->{$relation}() instanceof HasMany) {
            expect($related)->toHaveCount(1);
            $related = $related->first();
        }

        expect($related)->toBeInstanceOf($relatedClass);
    }
});

test('attributes', function () {
    expect($this->model->idpes)->toEqual($this->model->id);
    expect($this->model->nome)->toEqual($this->model->name);
});

test('relationship deficiencies', function () {
    LegacyIndividualFactory::new()->hasDeficiency()->create(['idpes' => $this->model]);

    expect($this->model->deficiencies)->toHaveCount(1);
    expect($this->model->deficiencies->first())->toBeInstanceOf(LegacyDeficiency::class);
});

test('relationship considerable deficiencies', function () {
    LegacyIndividualFactory::new()->hasDeficiency(['desconsidera_regra_diferenciada' => false])->create(['idpes' => $this->model]);

    expect($this->model->considerableDeficiencies)->toHaveCount(1);
    expect($this->model->considerableDeficiencies->first())->toBeInstanceOf(LegacyDeficiency::class);
});

test('relationships with children and dependents', function () {
    $childWithMother = LegacyIndividualFactory::new()->create([
        'idpes_mae' => $this->model->getKey(),
    ]);
    $childWithFather = LegacyIndividualFactory::new()->create([
        'idpes_pai' => $this->model->getKey(),
    ]);
    $dependent = LegacyIndividualFactory::new()->create([
        'idpes_responsavel' => $this->model->getKey(),
    ]);

    expect($this->model->childrenAsMother->contains($childWithMother))->toBeTrue();
    expect($this->model->childrenAsFather->contains($childWithFather))->toBeTrue();
    expect($this->model->dependentsAsResponsible->contains($dependent))->toBeTrue();
});

test('filter by each link type and multiple links', function () {
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

        expect($ids)->toEqualCanonicalizing(array_map(fn ($person) => $person->getKey(), $expected));
    }

    foreach ([null, '', 'all', 'todos'] as $linkType) {
        expect(LegacyPerson::query()->whereKey([
            $student->getKey(), $employee->getKey(), $mother->getKey(),
            $father->getKey(), $responsible->getKey(), $multiple->getKey(),
            $unlinked->getKey(), $inactiveStudent->getKey(), $inactiveEmployee->getKey(),
        ])->whereLinkType($linkType)->count())->toBe(9);
    }
});
