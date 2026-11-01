<?php

use App\Models\EmployeeGraduation;
use App\Models\LegacyPhone;
use App\Services\EmployeeGraduationService;
use App\Services\PhoneService;
use Database\Factories\EmployeeGraduationFactory;
use Database\Factories\LegacyPersonFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('apaga todas as formações do servidor', function () {
    $graduation = EmployeeGraduationFactory::new()->create();
    EmployeeGraduationFactory::new()->create(['employee_id' => $graduation->employee_id]);

    (new EmployeeGraduationService)->deleteAll($graduation->employee_id);

    expect(EmployeeGraduation::query()->where('employee_id', $graduation->employee_id)->exists())->toBeFalse();
});

test('telefone com só o DDD ou só o número não é gravado', function () {
    $person = LegacyPersonFactory::new()->create();

    (new PhoneService)->save($person->getKey(), 1, '48', null, 1);
    (new PhoneService)->save($person->getKey(), 1, null, '33334444', 1);

    expect(LegacyPhone::query()->where('idpes', $person->getKey())->exists())->toBeFalse();
});
