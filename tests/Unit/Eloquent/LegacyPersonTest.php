<?php

use App\Models\Builders\LegacyPersonBuilder;
use App\Models\Employee;
use App\Models\LegacyDeficiency;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(function () {
    $this->eloquent(LegacyPerson::class, relations: [
        'phone' => LegacyPhone::class,
        'individual' => LegacyIndividual::class,
        'employee' => Employee::class,
    ]);

    $this->createIndividual = function (?int $cpf, ?int $reasonId): int {
        $individual = LegacyIndividualFactory::new()->create(['cpf' => $cpf]);

        LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reasonId]);

        return $individual->getKey();
    };

    $this->createCpfSituations = function (): array {
        $reason = DocumentAbsenceReasonFactory::new()->create();
        $inactiveReason = DocumentAbsenceReasonFactory::new()->create(['deleted_at' => now()]);
        $systemReason = DocumentAbsenceReasonFactory::new()->create(['from_system' => true]);

        return [
            'with_cpf' => ($this->createIndividual)(12345678909, null),
            'null_without_reason' => ($this->createIndividual)(null, null),
            'zero_without_reason' => ($this->createIndividual)(0, null),
            'null_with_reason' => ($this->createIndividual)(null, $reason->getKey()),
            'zero_with_reason' => ($this->createIndividual)(0, $reason->getKey()),
            'zero_with_inactive_reason' => ($this->createIndividual)(0, $inactiveReason->getKey()),
            'null_with_system_reason' => ($this->createIndividual)(null, $systemReason->getKey()),
            'zero_with_system_reason' => ($this->createIndividual)(0, $systemReason->getKey()),
        ];
    };

    $this->queryPeople = function (array $people): LegacyPersonBuilder {
        return LegacyPerson::query()->whereKey(array_values($people));
    };
});

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->model->id, $this->model->idpes);
    $this->assertEquals($this->model->name, $this->model->nome);
});

test('relationship deficiencies', function () {
    LegacyIndividualFactory::new()->hasDeficiency()->create(['idpes' => $this->model]);

    $this->assertCount(1, $this->model->deficiencies);
    $this->assertInstanceOf(LegacyDeficiency::class, $this->model->deficiencies->first());
});

test('relationship considerable deficiencies', function () {
    LegacyIndividualFactory::new()->hasDeficiency(['desconsidera_regra_diferenciada' => false])->create(['idpes' => $this->model]);

    $this->assertCount(1, $this->model->considerableDeficiencies);
    $this->assertInstanceOf(LegacyDeficiency::class, $this->model->considerableDeficiencies->first());
});

test('where cpf situation with cpf ignores zero cpf', function () {
    $people = ($this->createCpfSituations)();

    $this->assertEqualsCanonicalizing(
        [$people['with_cpf']],
        ($this->queryPeople)($people)->whereCpfSituation('with_cpf')->pluck('idpes')->all()
    );
});

test('where cpf situation justified absence includes zero cpf', function () {
    $people = ($this->createCpfSituations)();

    $this->assertEqualsCanonicalizing(
        [$people['null_with_reason'], $people['zero_with_reason'], $people['zero_with_inactive_reason']],
        ($this->queryPeople)($people)->whereCpfSituation('justified_absence')->pluck('idpes')->all()
    );
});

test('where cpf situation unjustified absence includes zero cpf', function () {
    $people = ($this->createCpfSituations)();

    $this->assertEqualsCanonicalizing(
        [$people['null_without_reason'], $people['zero_without_reason'], $people['null_with_system_reason'], $people['zero_with_system_reason']],
        ($this->queryPeople)($people)->whereCpfSituation('unjustified_absence')->pluck('idpes')->all()
    );
});

test('where absence reason includes zero cpf and ignores informed cpf', function () {
    $people = ($this->createCpfSituations)();
    $reasonId = LegacyIndividual::query()->whereKey($people['zero_with_reason'])->value('document_absence_reason_id');
    $people['with_cpf_and_reason'] = ($this->createIndividual)(81388232758, $reasonId);

    $this->assertEqualsCanonicalizing(
        [$people['null_with_reason'], $people['zero_with_reason']],
        ($this->queryPeople)($people)->whereAbsenceReason($reasonId)->pluck('idpes')->all()
    );
});

test('where absence reason accepts inactive reason', function () {
    $people = ($this->createCpfSituations)();
    $reasonId = LegacyIndividual::query()->whereKey($people['zero_with_inactive_reason'])->value('document_absence_reason_id');

    $this->assertEqualsCanonicalizing(
        [$people['zero_with_inactive_reason']],
        ($this->queryPeople)($people)->whereAbsenceReason($reasonId)->pluck('idpes')->all()
    );
});
