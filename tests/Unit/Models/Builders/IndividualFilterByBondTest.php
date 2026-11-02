<?php

use App\Models\LegacyIndividual;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('filters individuals by student, employee, and responsible bonds', function () {
    $student = LegacyStudentFactory::new()->create();
    $studentIndividual = $student->individual;

    $employee = EmployeeFactory::new()->create();
    $employeeIndividual = $employee->individual;

    $responsible = LegacyIndividualFactory::new()->create();
    LegacyIndividualFactory::new()->create(['idpes_responsavel' => $responsible->getKey()]);

    $unlinked = LegacyIndividualFactory::new()->create();
    $ids = [
        $studentIndividual->getKey(),
        $employeeIndividual->getKey(),
        $responsible->getKey(),
        $unlinked->getKey(),
    ];
    $filter = fn (string $bond) => LegacyIndividual::query()
        ->whereKey($ids)
        ->filter(['vinculo' => $bond])
        ->pluck('idpes')
        ->all();

    expect($filter('Aluno'))->toBe([$studentIndividual->getKey()])
        ->and($filter('Servidor'))->toBe([$employeeIndividual->getKey()])
        ->and($filter('Responsável'))->toBe([$responsible->getKey()]);
});

test('rejects unsupported bond filters', function () {
    expect(fn () => LegacyIndividual::query()->filter(['vinculo' => 'Outro']))
        ->toThrow(InvalidArgumentException::class);
});
