<?php

use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use App\Models\LegacyUser;
use Database\Factories\LegacyTransferRequestFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyTransferRequest::class, relations: [
    'oldRegistration' => LegacyRegistration::class,
    'newRegistration' => LegacyRegistration::class,
    'transferType' => LegacyTransferType::class,
    'destinationSchool' => LegacySchool::class,
    'createdByUser' => LegacyUser::class,
    'deletedByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();

test('scope active', function () {
    LegacyTransferRequestFactory::new()->create(['ativo' => 0]);
    $found = $this->instanceNewEloquentModel()->active()->get();
    $this->assertCount(1, $found);
});

test('scope unattended', function () {
    LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_entrada' => null]);
    $found = $this->instanceNewEloquentModel()->unattended()->get();
    $this->assertCount(1, $found);
});
